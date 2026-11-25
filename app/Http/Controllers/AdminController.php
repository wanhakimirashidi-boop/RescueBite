<?php

namespace App\Http\Controllers;

use App\Models\FoodListing;
use App\Models\Reservation;
use App\Models\User;
use App\Models\VendorOutlet;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // FOR ADMIN: ANALYTICS section
    // Connected view: admin/dashboard.blade
    public function dashboard()
    {
        // this function is called when admin login/clicks "Analytics" tab in panel-overview.blade, referring to web.php GET (/admin/dashboard)
        // it loads summary cards, breakdowns, waste stats, and recent reservations, then opens admin/dashboard.blade (in the return view below)


        // variables from functions below
        $summaryStatistics = $this->getSummaryStatistics();
        $vendorBreakdown = $this->getVendorBreakdown();
        $categoryBreakdown = $this->getCategoryBreakdown();
        $wasteAnalytics = $this->getWasteAnalytics();
        $recentReservations = $this->getRecentReservations();

        return view('admin.dashboard', [
            'summaryStatistics' => $summaryStatistics,
            'vendorBreakdown' => $vendorBreakdown,
            'categoryBreakdown' => $categoryBreakdown,
            'wasteAnalytics' => $wasteAnalytics,
            'recentReservations' => $recentReservations,
        ]);
    }

    // START OF ADMIN USERS SECTION
    // Connected view: admin/users.blade.php
    public function users()
    {
        // this function is called when admin clicks "Users" tab or submits Search in admin/users.blade, referring to web.php GET (/admin/users)
        // it loads paginated users (filtered if ?search= exists) and active consumers, then opens admin/users.blade (in the return view below)

        $summaryStatistics = $this->getSummaryStatistics();         // getSummaryStatistics() from function below
        $searchTerm = request('search');                            // search from "name=search" in admin/users.blade.php

        // specifically for admin/users.blade (line 52). bcs we need user to hold vendorOutlet if they have one for that blade section
        $userQuery = User::with('vendorOutlet')                     // with('vendorOutlet') = after finding all users, also show their vendorOutlet (Vendor) ONLY IF they have one
            ->orderBy('name');

        // % for similarity search, like a "contains" search
        if ($searchTerm) {
            $userQuery->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $searchTerm . '%')
                    ->orWhere('role', 'like', '%' . $searchTerm . '%');
            });
        }

        // withQueryString() = RUNS the searched sql query 
        $users = $userQuery->paginate(10)->withQueryString();

        // Active consumer eligible to be approved as vendors with an assigned outlet || refer to admin/users.blade (line 107)
        $consumerUsers = User::where('role', 'consumer')
            ->where('is_blocked', false)
            ->orderBy('name')
            ->get();

        return view('admin.users', [
            'summaryStatistics' => $summaryStatistics,
            'users' => $users,
            'consumerUsers' => $consumerUsers,
            'searchTerm' => $searchTerm,
        ]);
    }

    // function is called when admin click "Block" or "Unblock" button in admin/users.blade and it refer to web.php PATCH then opens this function
    // e.g; "block" button clicked, it will change/toggle current user is_blocked status 
    public function toggleUserBlock(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Admin accounts cannot be blocked.');
        }

        // NOT(user is blocked) || REFER EXAMPLE ABOVE
        $newBlockedStatus = !$user->is_blocked; // is_blocked uses boolean on DB

        $user->update([
            'is_blocked' => $newBlockedStatus,
        ]);

        // web.php refer to User ID, so it automatically detect which user opened the page
        if ($newBlockedStatus) {
            return redirect()->route('admin.users')->with('success', $user->name . ' has been blocked.');
        }

        return redirect()->route('admin.users')->with('success', $user->name . ' has been unblocked.');
    }

    // this function is called when admin clicks "Approve as Vendor" button in admin/users.blade, referring to web.php POST (/admin/users/grant-vendor)
    public function grantVendorAccess(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'outlet_name' => ['required', 'string', 'max:255'],
            'outlet_location' => ['required', 'string', 'max:255'],
        ]);

        $selectedUser = User::findOrFail($validated['user_id']);

        if ($selectedUser->role !== 'consumer') {
            return back()->withInput()->with('error', 'Only consumer accounts can be upgraded to a vendor.');
        }

        if ($selectedUser->vendorOutlet) {
            return back()->withInput()->with('error', 'This user already manages a vendor outlet.');
        }

        $selectedUser->update([
            'role' => 'vendor',
        ]);

        VendorOutlet::create([
            'user_id' => $selectedUser->id,
            'outlet_name' => $validated['outlet_name'],
            'outlet_location' => $validated['outlet_location'],
        ]);

        // it validates the form, changes selected consumer's role to 'vendor', and creates their row in vendor_outlets table
        return redirect()->route('admin.users')
            ->with('success', $selectedUser->name . ' has been approved as a vendor for ' . $validated['outlet_name'] . '.');
    }
    // END OF ADMIN USERS SECTION


    
    // START OF ADMIN DASHBOARD SECTION
    // 4 Cards at very top (Total Users, Food Rescued, Active Listing, Total Savings).
    private function getSummaryStatistics()
    {
        // count total USERS (excluding admin)
        $consumerCount = User::where('role', 'consumer')
            ->count();
        $vendorCount = User::where('role', 'vendor')
            ->count();

        $totalUsers = $consumerCount + $vendorCount;

        // count total number of CLAIMED reservations & calculate TOTAL SAVINGS from claimed reservations
        $claimedReservations = Reservation::with('foodListing') //foodListing = Reservation.php
            ->where('status', 'claimed')
            ->get();

        // count total number of "FOOD RESCUED"
        $foodRescuedCount = $claimedReservations->count();

        // count total number of "ACTIVE LISTING" that are not expired
        $activeListingCount = FoodListing::where('status', 'active')
            ->where('quantity', '>', 0)
            ->where('expiry_time', '>', now())
            ->count();


        // calculate total SAVINGS from claimed reservations
        $totalSavings = 0;

        // Formula for Total Savings card: $totalSavings = (original price - discounted price) for each claimed reservation
        foreach ($claimedReservations as $claimedReservation) {
            if ($claimedReservation->foodListing) {
                $savingAmount = $claimedReservation->foodListing->original_price - $claimedReservation->foodListing->discounted_price;

                if ($savingAmount > 0) {
                    $totalSavings += $savingAmount;
                }
            }
        }

        return [
            'totalUsers' => $totalUsers,
            'consumerCount' => $consumerCount,
            'vendorCount' => $vendorCount,
            'foodRescuedCount' => $foodRescuedCount,
            'activeListingCount' => $activeListingCount,
            'totalSavings' => $totalSavings,
        ];
    }
    // END OF ADMIN DASHBOARD SECTION

    // START OF ADMIN ANALYTICS SECTION
    // Summarizes how many listings each vendor outlet has created, how many are still in stock, and how many are sold out/ended
    private function getVendorBreakdown()
    {
        // Separate pageName so paging Vendor Breakdown do not change Recent Reservations
        $vendorBreakdown = VendorOutlet::with('foodListings')
            ->orderBy('outlet_name')
            ->paginate(5, pageName: 'vendor_page')  //pageName = solution to not refresh entire table in single page
            ->withQueryString();

        // Loop through each vendor outlet to calculate the total listings, in-stock listings, and sold-out listings
        foreach ($vendorBreakdown as $vendorOutlet) {
            $totalListed = $vendorOutlet->foodListings->count();
            $inStockCount = 0;

            // Check each food listing to see if it's in stock (active, quantity > 0, and not expired)
            foreach ($vendorOutlet->foodListings as $foodListing) {
                $isInStock = $foodListing->status === 'active'
                    && $foodListing->quantity > 0
                    && $foodListing->expiry_time->isFuture();

                if ($isInStock) {
                    $inStockCount++;
                }
            }

            $soldOutCount = $totalListed - $inStockCount;

            $vendorOutlet->total_listed = $totalListed;
            $vendorOutlet->in_stock_count = $inStockCount;
            $vendorOutlet->sold_out_count = $soldOutCount;
        }

        return $vendorBreakdown;
    }

    // Groups all food listings by category and calculates each category's percentage share
    private function getCategoryBreakdown()
    {
        // let SQL count how many listings exist PER CATEGORY
        $categoryTotals = FoodListing::selectRaw('category, COUNT(*) as listing_count')
            ->groupBy('category')
            ->orderByDesc('listing_count')
            ->get();

        $totalListings = 0;

        // Calculate the total number of listings across all categories to determine percentages
        foreach ($categoryTotals as $categoryTotal) {
            $totalListings += $categoryTotal->listing_count;
        }

        $categoryBreakdown = [];

        // Formula: percentage = (category listing count / total listings) * 100
        foreach ($categoryTotals as $categoryTotal) {
            $percentage = 0;

            if ($totalListings > 0) {
                $percentage = (int) round(($categoryTotal->listing_count / $totalListings) * 100);
            }

            $categoryBreakdown[] = [
                'category' => $categoryTotal->category,
                'listingCount' => $categoryTotal->listing_count,
                'percentage' => $percentage,
                'styleClass' => $this->getCategoryCardClass($categoryTotal->category),  // get the CSS class for the category card
            ];
        }

        return $categoryBreakdown;
    }

    // Collects waste and rescue analytics across claimed, unclaimed (reserved), and expired records
    private function getWasteAnalytics()
    {
        $claimedCount = Reservation::where('status', 'claimed')->count();
        $unclaimedCount = Reservation::where('status', 'reserved')->count();
        $expiredReservationCount = Reservation::where('status', 'expired')->count();
        $expiredQuantityLeft = (int) FoodListing::where('status', 'expired')->sum('quantity');

        // Calculate percentage of claimed reservations "PICKUP COMPLETION" over the total of claimed and expired reservations
        $completedOrMissedTotal = $claimedCount + $expiredReservationCount;
        $rescueRate = 0;

        if ($completedOrMissedTotal > 0) {
            $rescueRate = (int) round(($claimedCount / $completedOrMissedTotal) * 100);
        }

        return [
            'claimedCount' => $claimedCount,
            'unclaimedCount' => $unclaimedCount,
            'expiredReservationCount' => $expiredReservationCount,
            'expiredQuantityLeft' => $expiredQuantityLeft,
            'rescueRate' => $rescueRate,
        ];
    }

    // Gets recent reservations with consumer, food listing, and outlet details
    private function getRecentReservations()
    {
        // Separate pageName so paging Recent Reservations does not change Vendor Breakdown
        return Reservation::with(['consumer', 'foodListing.vendorOutlet'])
            ->orderByDesc('created_at')
            ->paginate(5, pageName: 'reservations_page')
            ->withQueryString();
    }

    // Assign food categories to CSS classes in admin-panel.css
    private function getCategoryCardClass($categoryName)
    {
        $normalizedCategory = strtolower($categoryName);

        if ($normalizedCategory === 'pastries') {
            return 'admin-category-tile-pastries';
        }

        if ($normalizedCategory === 'food') {
            return 'admin-category-tile-food';
        }

        if ($normalizedCategory === 'snacks') {
            return 'admin-category-tile-snacks';
        }

        if ($normalizedCategory === 'drinks') {
            return 'admin-category-tile-drinks';
        }

        return 'admin-category-tile-default';
    }
    // END OF ADMIN ANALYTICS SECTION



    // START OF ADMIN REPORTS SECTION
    // this function is called when admin clicks "Reports" tab or submits filters in admin/reports.blade, referring to web.php GET (/admin/reports)
    public function reports(Request $request)
    {
        $filters = $this->getValidatedReportFilters($request);  // from function below. To validate the date & outlet filters shared by reports, print PDF, and CSV export

        $summaryStatistics = $this->getSummaryStatistics();
        $vendorOutlets = VendorOutlet::orderBy('outlet_name')->get();
        $reportData = $this->buildReportData($filters['fromDate'], $filters['toDate'], $filters['selectedOutletId']);

        return view('admin.reports', [
            'summaryStatistics' => $summaryStatistics,
            'vendorOutlets' => $vendorOutlets,
            'reportData' => $reportData,
            'fromDate' => $filters['fromDate'],
            'toDate' => $filters['toDate'],
            'selectedOutletId' => $filters['selectedOutletId'],
        ]);
    }

    // this function is called when admin clicks "Export As PDF" in admin/reports.blade, referring to web.php GET (/admin/reports/print)
    public function printReport(Request $request)
    {
        $filters = $this->getValidatedReportFilters($request);
        $reportData = $this->buildReportData($filters['fromDate'], $filters['toDate'], $filters['selectedOutletId']);

        return view('admin.reports-print', [
            'reportData' => $reportData,
            'fromDate' => $filters['fromDate'],
            'toDate' => $filters['toDate'],
        ]);
    }

    // this function is called when admin clicks "Export As Excel" in admin/reports.blade, referring to web.php GET (/admin/reports/export-csv)
    public function exportReportCsv(Request $request)
    {
        $filters = $this->getValidatedReportFilters($request);
        $fromDate = $filters['fromDate'];
        $toDate = $filters['toDate'];
        $reportData = $this->buildReportData($fromDate, $toDate, $filters['selectedOutletId']);

        $fromDateLabel = 'All dates';
        if ($fromDate) {
            $fromDateLabel = $fromDate;
        }

        $toDateLabel = 'All dates';
        if ($toDate) {
            $toDateLabel = $toDate;
        }

        return response()->streamDownload(function () use ($reportData, $fromDateLabel, $toDateLabel) {
            $fileHandle = fopen('php://output', 'w');

            fputcsv($fileHandle, ['RescueBite Waste Reduction Report']);
            fputcsv($fileHandle, ['Outlet Filter', $reportData['selectedOutletName']]);
            fputcsv($fileHandle, ['From Date', $fromDateLabel]);
            fputcsv($fileHandle, ['To Date', $toDateLabel]);
            fputcsv($fileHandle, []);

            fputcsv($fileHandle, ['Summary Metric', 'Value']);
            fputcsv($fileHandle, ['Items Rescued (Claimed)', $reportData['itemsRescued']]);
            fputcsv($fileHandle, ['Active Vendors', $reportData['activeVendorsCount']]);
            fputcsv($fileHandle, ['Overall Savings (RM)', number_format($reportData['overallSavings'], 2, '.', '')]);
            fputcsv($fileHandle, ['Expired Reservations', $reportData['expiredReservationsCount']]);
            fputcsv($fileHandle, ['Unsold Food Wasted (Qty)', $reportData['unsoldFoodWasted']]);
            fputcsv($fileHandle, []);

            fputcsv($fileHandle, ['Outlet', 'Location', 'Items Rescued', 'Expired Reservations', 'Unsold Food Wasted', 'Total Savings (RM)']);

            foreach ($reportData['outletBreakdown'] as $outletRow) {
                fputcsv($fileHandle, [
                    $outletRow['outletName'],
                    $outletRow['outletLocation'],
                    $outletRow['itemsRescued'],
                    $outletRow['expiredReservations'],
                    $outletRow['unsoldFoodWasted'],
                    number_format($outletRow['totalSavings'], 2, '.', ''),
                ]);
            }

            fclose($fileHandle);
        }, 'rescuebite-waste-report.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    // Validates and reads date & outlet filters shared by; reports, print PDF, and CSV export
    private function getValidatedReportFilters(Request $request)
    {
        // 'from_date' & etc came from admin/report.blade "name="from_date"" || and checks for those conditions
        $validated = $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
            'outlet_id' => ['nullable', 'exists:vendor_outlets,id'],
        ]);

        $fromDate = null;
        $toDate = null;
        $selectedOutletId = null;

        // isset checks 2 things at once
        // e.g; Does 'from_date' exist in the $validated array, and is it not null
        if (isset($validated['from_date'])) {
            $fromDate = $validated['from_date'];
        }

        if (isset($validated['to_date'])) {
            $toDate = $validated['to_date'];
        }

        if (isset($validated['outlet_id'])) {
            $selectedOutletId = $validated['outlet_id'];
        }

        return [
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'selectedOutletId' => $selectedOutletId,
        ];
    }

    // FOR "Outlet Savings & Waste Breakdown" SECTION
    // Builds filtered waste reduction totals and per outlet savings for; Reports, Print PDF, and CSV Export
    private function buildReportData($fromDate, $toDate, $selectedOutletId)
    {
        $claimedQuery = Reservation::with(['foodListing.vendorOutlet'])
            ->where('status', 'claimed');

        $expiredReservationQuery = Reservation::with(['foodListing.vendorOutlet'])
            ->where('status', 'expired');

        $expiredListingQuery = FoodListing::with('vendorOutlet')
            ->where('status', 'expired');

        if ($fromDate) {
            $startOfRange = Carbon::parse($fromDate)->startOfDay();
            $claimedQuery->where('claimed_at', '>=', $startOfRange);
            $expiredReservationQuery->where('updated_at', '>=', $startOfRange);
            $expiredListingQuery->where('expiry_time', '>=', $startOfRange);
        }

        if ($toDate) {
            $endOfRange = Carbon::parse($toDate)->endOfDay();
            $claimedQuery->where('claimed_at', '<=', $endOfRange);
            $expiredReservationQuery->where('updated_at', '<=', $endOfRange);
            $expiredListingQuery->where('expiry_time', '<=', $endOfRange);
        }

        if ($selectedOutletId) {
            $claimedQuery->whereHas('foodListing', function ($foodListingQuery) use ($selectedOutletId) {
                $foodListingQuery->where('vendor_outlet_id', $selectedOutletId);
            });

            $expiredReservationQuery->whereHas('foodListing', function ($foodListingQuery) use ($selectedOutletId) {
                $foodListingQuery->where('vendor_outlet_id', $selectedOutletId);
            });

            $expiredListingQuery->where('vendor_outlet_id', $selectedOutletId);
        }

        $claimedReservations = $claimedQuery->get();
        $expiredReservations = $expiredReservationQuery->get();
        $expiredListings = $expiredListingQuery->get();

        $itemsRescued = $claimedReservations->count();
        $expiredReservationsCount = $expiredReservations->count();
        $unsoldFoodWasted = 0;

        foreach ($expiredListings as $expiredListing) {
            $unsoldFoodWasted += $expiredListing->quantity;
        }

        $overallSavings = 0;
        $activeVendorIds = [];

        foreach ($claimedReservations as $claimedReservation) {
            if ($claimedReservation->foodListing) {
                $savingAmount = $claimedReservation->foodListing->original_price - $claimedReservation->foodListing->discounted_price;

                if ($savingAmount > 0) {
                    $overallSavings += $savingAmount;
                }

                $activeVendorIds[$claimedReservation->foodListing->vendor_outlet_id] = true;
            }
        }

        // Also count outlets that had active listings in the selected filter.
        $activeListingsQuery = FoodListing::where('status', 'active')
            ->where('quantity', '>', 0)
            ->where('expiry_time', '>', now());

        if ($selectedOutletId) {
            $activeListingsQuery->where('vendor_outlet_id', $selectedOutletId);
        }

        foreach ($activeListingsQuery->get() as $activeListing) {
            $activeVendorIds[$activeListing->vendor_outlet_id] = true;
        }

        // Build per-outlet savings breakdown for the selected outlet (or all outlets).
        $outletQuery = VendorOutlet::orderBy('outlet_name');

        if ($selectedOutletId) {
            $outletQuery->where('id', $selectedOutletId);
        }

        $outlets = $outletQuery->get();
        $selectedOutletName = 'All outlets';

        if ($selectedOutletId && $outlets->count() > 0) {
            $selectedOutletName = $outlets->first()->outlet_name;
        }

        $outletBreakdown = [];

        foreach ($outlets as $outlet) {
            $outletRescued = 0;
            $outletSavings = 0;
            $outletExpiredReservations = 0;
            $outletUnsoldWasted = 0;

            foreach ($claimedReservations as $claimedReservation) {
                if ($claimedReservation->foodListing && $claimedReservation->foodListing->vendor_outlet_id === $outlet->id) {
                    $outletRescued++;
                    $savingAmount = $claimedReservation->foodListing->original_price - $claimedReservation->foodListing->discounted_price;

                    if ($savingAmount > 0) {
                        $outletSavings += $savingAmount;
                    }
                }
            }

            foreach ($expiredReservations as $expiredReservation) {
                if ($expiredReservation->foodListing && $expiredReservation->foodListing->vendor_outlet_id === $outlet->id) {
                    $outletExpiredReservations++;
                }
            }

            foreach ($expiredListings as $expiredListing) {
                if ($expiredListing->vendor_outlet_id === $outlet->id) {
                    $outletUnsoldWasted += $expiredListing->quantity;
                }
            }

            $outletBreakdown[] = [
                'outletName' => $outlet->outlet_name,
                'outletLocation' => $outlet->outlet_location,
                'itemsRescued' => $outletRescued,
                'expiredReservations' => $outletExpiredReservations,
                'unsoldFoodWasted' => $outletUnsoldWasted,
                'totalSavings' => $outletSavings,
            ];
        }

        return [
            'selectedOutletName' => $selectedOutletName,
            'itemsRescued' => $itemsRescued,
            'activeVendorsCount' => count($activeVendorIds),
            'overallSavings' => $overallSavings,
            'expiredReservationsCount' => $expiredReservationsCount,
            'unsoldFoodWasted' => $unsoldFoodWasted,
            'outletBreakdown' => $outletBreakdown,
        ];
    }
    // END OF ADMIN REPORTS SECTION
}