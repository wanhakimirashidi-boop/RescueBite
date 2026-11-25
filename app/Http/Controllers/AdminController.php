<?php

namespace App\Http\Controllers;

use App\Models\FoodListing;
use App\Models\Reservation;
use App\Models\User;
use App\Models\VendorOutlet;

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

    // 4 Cards at very top (Total Users, Food Rescued, Active Listing, Total Savings).
    private function getSummaryStatistics()
    {
        // count total USERS (excluding admin)
        $consumerCount = User::where('role', 'consumer')->count();
        $vendorCount = User::where('role', 'vendor')->count();
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
        $expiredListingCount = FoodListing::where('status', 'expired')->count();
        $expiredQuantityLeft = (int) FoodListing::where('status', 'expired')->sum('quantity');

        $completedOrMissedTotal = $claimedCount + $expiredReservationCount;
        $rescueRate = 0;

        if ($completedOrMissedTotal > 0) {
            $rescueRate = (int) round(($claimedCount / $completedOrMissedTotal) * 100);
        }

        return [
            'claimedCount' => $claimedCount,
            'unclaimedCount' => $unclaimedCount,
            'expiredReservationCount' => $expiredReservationCount,
            'expiredListingCount' => $expiredListingCount,
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
