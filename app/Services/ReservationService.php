<?php

namespace App\Services;

use App\Models\FoodListing;
use App\Models\Reservation;
use Illuminate\Support\Str;

class ReservationService
{
    // Checks whether food listing can still be reserved
    public function isFoodListingAvailable(FoodListing $foodListing)
    {
        if ($foodListing->status !== 'active') {
            return false;
        }

        if ($foodListing->quantity < 1) {
            return false;
        }

        if ($foodListing->expiry_time->isPast()) {
            return false;
        }

        return true;
    }

    // Creates a reservation, makes a pickup code, and reduces the available food quantity
    public function createReservation($consumerId, FoodListing $foodListing)
    {
        $reservation = Reservation::create([
            'consumer_id' => $consumerId,
            'food_listing_id' => $foodListing->id,
            'verify_code' => $this->generateVerifyCode(),
            'status' => 'reserved',
        ]);

        $foodListing->decrement('quantity');

        return $reservation;
    }

    // Creates short pickup code thats different from other codes
    private function generateVerifyCode()
    {
        do {
            $verifyCode = 'RB-' . Str::upper(Str::random(6));
        } while (Reservation::where('verify_code', $verifyCode)->exists());

        return $verifyCode;
    }
}