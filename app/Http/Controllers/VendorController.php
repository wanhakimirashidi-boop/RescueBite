<?php

namespace App\Http\Controllers;

use App\Models\FoodListing;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    // to create food listing
    public function createFoodListing()
    {
        $vendorOutlet = auth()->user()->vendorOutlet;

        return view('vendor.create-food-listing', [
            'vendorOutlet' => $vendorOutlet,
        ]);
    }

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

    public function editFoodListingImage(FoodListing $foodListing)
    {
        $vendorOutlet = auth()->user()->vendorOutlet;

        $this->ensureFoodListingBelongsToVendor($foodListing, $vendorOutlet->id);

        return view('vendor.edit-food-listing-image', [
            'vendorOutlet' => $vendorOutlet,
            'foodListing' => $foodListing,
        ]);
    }

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
    private function ensureFoodListingBelongsToVendor(FoodListing $foodListing, $vendorOutletId)
    {
        if ($foodListing->vendor_outlet_id !== $vendorOutletId) {
            abort(403);
        }
    }
}
