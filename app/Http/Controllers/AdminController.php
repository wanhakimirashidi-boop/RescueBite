<?php

namespace App\Http\Controllers;

use App\Models\FoodListing;
use App\Models\Reservation;
use App\Models\User;
use App\Models\VendorOutlet;

class AdminController extends Controller
{
    // passes to admin/dashboard.blade
    public function dashboard()
    {
        // variables from functions below
        $summaryStatistics = $this->getSummaryStatistics();
        $vendorBreakdown = $this->getVendorBreakdown();
        $categoryBreakdown = $this->getCategoryBreakdown();
        $wasteAnalytics = $this->getWasteAnalytics();
        $recentReservations = Reservation::with(['foodListing.vendorOutlet'])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        return view('admin.dashboard', [
            'summaryStatistics' => $summaryStatistics,
            'vendorBreakdown' => $vendorBreakdown,
            'categoryBreakdown' => $categoryBreakdown,
            'wasteAnalytics' => $wasteAnalytics,
            'recentReservations' => $recentReservations,
        ]);
    }

    // Builds the 4 top KPI cards shown across the Admin Panel (Total Users, Food Rescued, Active Listing, Total Savings).
    private function getSummaryStatistics()
    {
        // count total number of USERS in the system
        $totalUsers = User::count();

        // count total number of CLAIMED reservations & calculate TOTAL SAVINGS from claimed reservations
        $claimedReservations = Reservation::with('foodListing') //foodListing = Reservation.php
            ->where('status', 'claimed')
            ->get();

        // count total number of CLAIMED reservations
        $foodRescuedCount = $claimedReservations->count();

        // count total number of ACTIVE LISTING that are not expired
        $activeListingCount = FoodListing::where('status', 'active')
            ->where('quantity', '>', 0)
            ->where('expiry_time', '>', now())
            ->count();


        // calculate total SAVINGS from claimed reservations
        $totalSavings = 0;

        // formula: $totalSavings = (original price - discounted price) for each claimed reservation
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
            'foodRescuedCount' => $foodRescuedCount,
            'activeListingCount' => $activeListingCount,
            'totalSavings' => $totalSavings,
        ];
    }

    // Summarizes how many listings each vendor outlet has created, how many are still in stock, and how many are sold out/ended
    private function getVendorBreakdown()
    {
        // get all vendor outlets with their food listings
        $vendorOutlets = VendorOutlet::with('foodListings')
            ->orderBy('outlet_name')
            ->get();

        $vendorBreakdown = [];

        // Loop through each vendor outlet to calculate the total listings, in-stock listings, and sold-out listings
        foreach ($vendorOutlets as $vendorOutlet) {
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

            $vendorBreakdown[] = [
                'outletName' => $vendorOutlet->outlet_name,
                'totalListed' => $totalListed,
                'inStockCount' => $inStockCount,
                'soldOutCount' => $soldOutCount,
            ];
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

    // assign food categories to CSS classes in admin-panel.css
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
