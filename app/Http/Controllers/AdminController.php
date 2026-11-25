<?php

namespace App\Http\Controllers;

use App\Models\FoodListing;
use App\Models\Reservation;
use App\Models\VendorOutlet;
use App\Services\AdminSummaryService;

// HANDLES ADMIN: ANALYTICS ONLY
class AdminController extends Controller
{
    protected $adminSummaryService;

    // Shared AdminSummaryService.php - supplies the 4 top KPI cards in admin/panel-overview.blade
    public function __construct(AdminSummaryService $adminSummaryService)
    {
        $this->adminSummaryService = $adminSummaryService;
    }

    // Connected view: admin/dashboard.blade
    public function dashboard()
    {
        // this function is called when admin login/clicks "Analytics" tab in panel-overview.blade, referring to web.php GET (/admin/dashboard)
        // it loads summary cards, breakdowns, waste stats, and recent reservations, then opens admin/dashboard.blade (in the return view below)


        // variables from functions below
        $summaryStatistics = $this->adminSummaryService->getSummaryStatistics();
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

    // Summarizes how many listings each vendor outlet has created, how many are still in stock, and how many are sold out/ended
    private function getVendorBreakdown()
    {
        // Separate pageName so paging Vendor Breakdown do not change Recent Reservations
        $vendorBreakdown = VendorOutlet::with('foodListings')
            ->orderBy('outlet_name')
            ->paginate(5, pageName: 'vendor_page')  // pageName = solution to not refresh entire table in single page
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