<?php

namespace App\Services;

use App\Models\FoodListing;
use App\Models\Reservation;

class FoodListingExpiryService
{
    // Marks passed active listings and their unclaimed reservations as expired
    public function expirePastDueRecords()
    {
        // find active listings where expiry has passed, then change its status
        $expiredFoodListingCount = FoodListing::where('status', 'active')
            ->where('expiry_time', '<=', now())
            ->update([
                'status' => 'expired',
            ]);

        // find unclaimed reservations where the listing has expired, then change its status
        $expiredReservationCount = Reservation::where('status', 'reserved')
            ->whereHas('foodListing', function ($foodListingQuery) {
                $foodListingQuery->where('expiry_time', '<=', now());
            })
            ->update([
                'status' => 'expired',
            ]);

        return [
            'expiredFoodListingCount' => $expiredFoodListingCount,
            'expiredReservationCount' => $expiredReservationCount,
        ];
    }
}
