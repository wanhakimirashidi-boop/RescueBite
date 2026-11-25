<?php

namespace App\Services;

use App\Models\FoodListing;
use App\Models\Reservation;
use App\Models\User;

// SHARED TO AdminController.php, AdminUserController.php, and AdminReportController.php
class AdminSummaryService
{
    // 4 Cards at very top (Total Users, Food Rescued, Active Listing, Total Savings)
    // Shared across AdminController, AdminUserController, and AdminReportController because all 3 pages show panel-overview.blade
    public function getSummaryStatistics()
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
}
