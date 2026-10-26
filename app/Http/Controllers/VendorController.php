<?php

namespace App\Http\Controllers;

use App\Models\Reservation;

class VendorController extends Controller
{
    public function dashboard()
    {
        $vendorOutlet = auth()->user()->vendorOutlet;   //vendorOutlet.php is from VendorOutlet model, relationship with User model
        $foodListings = $vendorOutlet->foodListings()   // listing that belongs to the vendor
            ->withCount('reservations')
            ->orderBy('expiry_time')
            ->get();

        // foodListings is from VendorOutlet.php model, relationship with FoodListing model
        $listingCount = $foodListings->count(); // number of listing

        $foodListingIds = $foodListings->pluck('id');

        // $totalReserved & $totalClaimed are from Reservation.php model, relationship with FoodListing model
        $totalReserved = Reservation::whereIn('food_listing_id', $foodListingIds)
            ->where('status', 'reserved')
            ->count();

        $totalClaimed = Reservation::whereIn('food_listing_id', $foodListingIds)
            ->where('status', 'claimed')
            ->count();

        return view('vendor.dashboard', [
            'vendorOutlet' => $vendorOutlet,
            'foodListings' => $foodListings,
            'listingCount' => $listingCount,
            'totalReserved' => $totalReserved,
            'totalClaimed' => $totalClaimed,
        ]);
    }
}