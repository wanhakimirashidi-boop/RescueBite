<?php

namespace App\Http\Controllers;

use App\Models\FoodListing;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VendorController extends Controller
{
    // shows the vendor inventory page using data from VendorOutlet, FoodListing, Reservation, and vendor/dashboard.blade.php
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

    // opens the Add Surplus form in vendor/create-food-listing.blade.php for the logged-in vendor outlet
    // to create food listing
    public function createFoodListing()
    {
        $vendorOutlet = auth()->user()->vendorOutlet;

        return view('vendor.create-food-listing', [
            'vendorOutlet' => $vendorOutlet,
        ]);
    }

    // checks the Add Surplus form, stores its image, saves a FoodListing record, then returns to the vendor dashboard
    // this receives data from create-food-listing.blade.php and use the FoodListing model
    public function storeFoodListing(Request $request)
    {
        $validated = $request->validate([
            'item_name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'quantity' => ['required', 'integer', 'min:1'],
            'discounted_price' => ['required', 'numeric', 'min:0'],
            'original_price' => ['required', 'numeric', 'gte:discounted_price'],
            'expiry_time' => ['required', 'date', 'after:now'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $imagePath = $request->file('image')->store('food-listings', 'public');

        unset($validated['image']);
        $validated['vendor_outlet_id'] = auth()->user()->vendorOutlet->id;
        $validated['image_path'] = $imagePath;
        $validated['status'] = 'active';

        FoodListing::create($validated);

        return redirect()->route('vendor.dashboard')->with('success', 'Food listing added successfully.');
    }

    // opens the Edit Image form for one food listing after confirming that it belongs to the logged in vendor
    // this sends the selected FoodListing model to edit-food-listing-image.blade.php
    public function editFoodListingImage(FoodListing $foodListing)
    {
        $vendorOutlet = auth()->user()->vendorOutlet;

        $this->ensureFoodListingBelongsToVendor($foodListing, $vendorOutlet->id);

        return view('vendor.edit-food-listing-image', [
            'vendorOutlet' => $vendorOutlet,
            'foodListing' => $foodListing,
        ]);
    }

    // replace one listing image, deletes the old stored image, and updates image_path in the FoodListing model
    // this receives the new file from edit-food-listing-image.blade.php
    public function updateFoodListingImage(Request $request, FoodListing $foodListing)
    {
        $vendorOutlet = auth()->user()->vendorOutlet;

        $this->ensureFoodListingBelongsToVendor($foodListing, $vendorOutlet->id);

        $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($foodListing->image_path) {
            Storage::disk('public')->delete($foodListing->image_path);
        }

        $imagePath = $request->file('image')->store('food-listings', 'public');

        $foodListing->update([
            'image_path' => $imagePath,
        ]);

        return redirect()->route('vendor.dashboard')->with('success', 'Food image updated successfully.');
    }

    // delete one food listing and its stored image after confirming that the listing belongs to the logged in vendor
    // this is called by the Delete form in vendor/dashboard.blade.php
    public function destroyFoodListing(FoodListing $foodListing)
    {
        $vendorOutlet = auth()->user()->vendorOutlet;

        $this->ensureFoodListingBelongsToVendor($foodListing, $vendorOutlet->id);

        if ($foodListing->image_path) {
            Storage::disk('public')->delete($foodListing->image_path);
        }

        $foodListing->delete();

        return redirect()->route('vendor.dashboard')->with('success', 'Food listing deleted successfully.');
    }

    // prevents Vendor A from editing or deleting Vendor B’s listing
    // checks that the selected FoodListing belongs to the current VendorOutlet before image editing or deletion
    private function ensureFoodListingBelongsToVendor(FoodListing $foodListing, $vendorOutletId)
    {
        if ($foodListing->vendor_outlet_id !== $vendorOutletId) {
            abort(403);
        }
    }
}
