<?php

namespace App\Http\Controllers;

use App\Models\FoodListing;

class ConsumerController extends Controller
{
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

    // My Rescues page - Active Rescues for consumers
    public function rescues()
    {
        return view('consumer.rescues'); // rescues.blade
    }

    // My Rescues History page - Past Rescues for consumers
    public function rescueHistory()
    {
        return view('consumer.rescues-history'); // rescues-history.blade
    }
}
