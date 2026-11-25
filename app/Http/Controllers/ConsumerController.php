<?php

namespace App\Http\Controllers;

use App\Models\FoodListing;
use App\Models\Reservation;
use App\Models\VendorOutlet;
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
        // Reads the selected vendor outlet ID and category from the dashboard filter links.
        $selectedVendorOutletId = request('outlet');
        $selectedCategory = request('category');

        $foodListingQuery = $this->getAvailableFoodListingQuery()
            ->with('vendorOutlet');

        if ($selectedVendorOutletId) {
            $foodListingQuery->where('vendor_outlet_id', $selectedVendorOutletId);
        }

        if ($selectedCategory) {
            $foodListingQuery->where('category', $selectedCategory);
        }

        $foodListings = $foodListingQuery
            ->orderBy('expiry_time')
            ->get();

        // Supplies the vendor buttons from the vendor_outlets table.
        $vendorOutlets = VendorOutlet::orderBy('outlet_name')->get();

        // Keeps the main filter row compact when many vendor outlets are registered.
        $primaryVendorOutlets = $vendorOutlets->take(8);
        $additionalVendorOutlets = $vendorOutlets->skip(8);
        $selectedAdditionalVendorOutlet = null;

        foreach ($additionalVendorOutlets as $additionalVendorOutlet) {
            if ($selectedVendorOutletId == $additionalVendorOutlet->id) {
                $selectedAdditionalVendorOutlet = $additionalVendorOutlet;
                break;
            }
        }

        // Supplies category buttons only from food that is currently available to reserve.
        $categoryQuery = $this->getAvailableFoodListingQuery();

        if ($selectedVendorOutletId) {
            $categoryQuery->where('vendor_outlet_id', $selectedVendorOutletId);
        }

        $categories = $categoryQuery
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('consumer.dashboard', [
            'foodListings' => $foodListings,
            'primaryVendorOutlets' => $primaryVendorOutlets,
            'additionalVendorOutlets' => $additionalVendorOutlets,
            'selectedAdditionalVendorOutlet' => $selectedAdditionalVendorOutlet,
            'categories' => $categories,
            'selectedVendorOutletId' => $selectedVendorOutletId,
            'selectedCategory' => $selectedCategory,
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
        // request('search') reads search input from URL - e.g. /consumer/rescues-history?search=roti
        $searchTerm = request('search');

        // getConsumerReservations() is a private function below (line 93)
        $reservations = $this->getConsumerReservations('reserved', $searchTerm, 'created_at');

        // Gets expired reservations for only three days after their food listing expiry time
        $recentExpiredReservations = $this->getRecentExpiredReservations($searchTerm);

        // rescues.blade
        return view('consumer.rescues', [
            'reservations' => $reservations,
            'recentExpiredReservations' => $recentExpiredReservations,
            'searchTerm' => $searchTerm,
        ]);
    }

    // My Rescues History page - Past Rescues for consumers
    public function rescueHistory()
    {
        // request('search') reads search input from URL - e.g. /consumer/rescues-history?search=roti
        $searchTerm = request('search');    // refer to rescues-list-navigation.blade search form input name="search"

        // 'claimed' only get claimed reservations || $searchTerm allow search items || 'claimed_at' to get the newest
        $reservations = $this->getConsumerReservations('claimed', $searchTerm, 'claimed_at');

        return view('consumer.rescues-history', [
            'reservations' => $reservations,
            'searchTerm' => $searchTerm,
        ]); // rescues-history.blade
    }

    // Gets one consumer's reservations, optionally searches the food item or outlet & shows 10 records per page
    // This is shared by rescues.blade.blade and rescues-history.blade
    private function getConsumerReservations($reservationStatus, $searchTerm, $sortColumn)
    {
        $reservationQuery = $this->getConsumerReservationQuery($reservationStatus, $searchTerm);

        return $reservationQuery->orderByDesc($sortColumn)
            ->paginate(10)
            ->withQueryString();
    }

    // Gets expired reservations whose food listing expiry time was within the past three days
    // This sends records to the Recent Expiries section in consumer/rescues.blade.php
    private function getRecentExpiredReservations($searchTerm)
    {
        $threeDaysAgo = now()->subDays(3);
        $currentTime = now();

        return $this->getConsumerReservationQuery('expired', $searchTerm)
            ->whereHas('foodListing', function ($foodListingQuery) use ($threeDaysAgo, $currentTime) {
                $foodListingQuery->where('expiry_time', '>=', $threeDaysAgo)
                    ->where('expiry_time', '<=', $currentTime);
            })
            ->orderByDesc('updated_at')
            ->get();
    }

    // Builds the shared Reservation query for active, claimed, and expired consumer records
    // Both rescue list methods use this so their search rules stay the same
    private function getConsumerReservationQuery($reservationStatus, $searchTerm)
    {
        $reservationQuery = Reservation::with(['foodListing.vendorOutlet'])
            ->where('consumer_id', auth()->id())
            ->where('status', $reservationStatus);

        if ($searchTerm) {
            $reservationQuery->whereHas('foodListing', function ($foodListingQuery) use ($searchTerm) {
                $foodListingQuery->where('item_name', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('vendorOutlet', function ($vendorOutletQuery) use ($searchTerm) {
                        $vendorOutletQuery->where('outlet_name', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        return $reservationQuery;
    }

    // Builds the shared query for food that is active, has quantity, and has not passed its expiry time
    // This is used by the dashboard food grid and the category filter buttons
    private function getAvailableFoodListingQuery()
    {
        return FoodListing::where('status', 'active')
            ->where('quantity', '>', 0)
            ->where('expiry_time', '>', now());
    }
}
