<?php

namespace App\Http\Controllers;

use App\Models\FoodListing;
use App\Models\Reservation;
use App\Services\ReservationService;

class ConsumerController extends Controller
{
    protected $reservationService;

    // shared ReservationService.php - for: confirmation page and reservation action
    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    // shows active (instock & not expired) food listings from FoodListing and VendorOutlet in consumer/dashboard.blade
    public function dashboard()
    {
        $foodListings = FoodListing::with('vendorOutlet')
            ->where('status', 'active')
            ->where('quantity', '>', 0)
            ->where('expiry_time', '>', now())
            ->orderBy('expiry_time')
            ->get();

        return view('consumer.dashboard', [
            'foodListings' => $foodListings,
        ]);  // dashboard.blade
    }

    // Opens the confirmation page when clicked "Reserve" btn
    public function showReservationConfirmation(FoodListing $foodListing)
    {
        if (!$this->reservationService->isFoodListingAvailable($foodListing)) {
            return redirect()->route('dashboard')->with('error', 'This food listing is no longer available.');
        }

        $foodListing->load('vendorOutlet');

        return view('consumer.confirm-reservation', [
            'foodListing' => $foodListing,
        ]);
    }

    // Create a Reservation only after confirmation, then sends the consumer to My Rescues page
    // This receives the confirmed listing from consumer/confirm-reservation.blade.php
    public function storeReservation(FoodListing $foodListing)
    {
        if (!$this->reservationService->isFoodListingAvailable($foodListing)) {
            return redirect()->route('dashboard')->with('error', 'This food listing is no longer available.');
        }

        $reservation = $this->reservationService->createReservation(auth()->id(), $foodListing);

        return redirect()->route('consumer.rescues')->with('success', 'Food reserved successfully. Your pickup code is ' . $reservation->verify_code . '.');
    }

    // My Rescues page - Active Rescues for consumers
    public function rescues()
    {
        $reservations = Reservation::with(['foodListing.vendorOutlet'])
            ->where('consumer_id', auth()->id())
            ->where('status', 'reserved')
            ->orderByDesc('created_at')
            ->get();

        // rescues.blade
        return view('consumer.rescues', [ 
            'reservations' => $reservations,
        ]);
    }

    // My Rescues History page - Past Rescues for consumers
    public function rescueHistory()
    {
        return view('consumer.rescues-history'); // rescues-history.blade
    }

}
