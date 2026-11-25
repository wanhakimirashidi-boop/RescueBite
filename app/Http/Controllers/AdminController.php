<?php

namespace App\Http\Controllers;

use App\Models\FoodListing;
use App\Models\Reservation;
use App\Models\User;
use App\Models\VendorOutlet;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Connected view: admin/dashboard.blade
    public function dashboard()
    {
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
        $summaryStatistics = $this->getSummaryStatistics();         // getSummaryStatistics() from function below
        $searchTerm = request('search');                            // search from "name=search" in admin/users.blade.php

        // specifically for admin/users.blade (line 52). bcs we need user to hold vendorOutlet if they have one for that blade section
        $userQuery = User::with('vendorOutlet')                     // with('vendorOutlet') = after finding all users, also show their vendorOutlet (Vendor only) IF they have one
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

        // Active consumer accounts eligible to be approved as vendors with an assigned outlet.
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

    // Blocks or unblocks a consumer or vendor account from the Admin Users table.
    public function toggleUserBlock(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Admin accounts cannot be blocked.');
        }

        $newBlockedStatus = !$user->is_blocked;

        $user->update([
            'is_blocked' => $newBlockedStatus,
        ]);

        if ($newBlockedStatus) {
            return redirect()->route('admin.users')->with('success', $user->name . ' has been blocked.');
        }

        return redirect()->route('admin.users')->with('success', $user->name . ' has been unblocked.');
    }

    // Promotes a consumer account to vendor and assigns one vendor outlet.
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
}
// END OF ADMIN ANALYTICS SECTION