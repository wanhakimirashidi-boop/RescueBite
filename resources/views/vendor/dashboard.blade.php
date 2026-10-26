{{-- Vendor Dashboard (RETURN VIEW) --}}

{{-- Shared template with vendor.blade.php --}}

@extends('layouts.vendor')

@section('title', 'Inventory | RescueBite Vendor Panel')

@section('content')
    <section class="vendor-hero">
        <div class="container">
            <p class="eyebrow">Vendor workspace</p>
            <h1>Vendor <span>Panel</span></h1>
            <p>Manage today&apos;s surplus food and prepare orders for collection.</p>
            {{-- &apos; is apostraphe (') --}}
        </div>
    </section>

    <section class="vendor-content" aria-labelledby="inventory-title">
        <div class="container">
            <div class="vendor-statistics">
                <article class="vendor-statistic">
                    <span>My listings</span>
                    <strong>{{ $listingCount }}</strong>
                    {{-- $listingCount & $totalClaimed below variables refer to VendorController.php --}}
                </article>
                <article class="vendor-statistic">
                    <span>Total reserved</span>
                    <strong>{{ $totalReserved }}</strong>
                </article>
                <article class="vendor-statistic">
                    <span>Claimed</span>
                    <strong>{{ $totalClaimed }}</strong>
                </article>
            </div>

            <nav class="vendor-tabs" aria-label="Vendor panel sections">
                <button class="vendor-tab vendor-tab-active" type="button">Inventory</button>
                <a class="vendor-tab" href="{{ route('vendor.food-listings.create') }}">Add Surplus</a>
                <button class="vendor-tab" type="button">Verify Code</button>
            </nav>

            <div class="vendor-list-heading">
                <div>
                    <p class="eyebrow">Today&apos;s surplus</p>
                    <h2 id="inventory-title">My listings</h2>
                </div>
                <a class="vendor-add-button" href="{{ route('vendor.food-listings.create') }}">Add surplus</a>
            </div>

            @if (session('success'))
                <p class="vendor-success-message">{{ session('success') }}</p>
            @endif

            <div class="vendor-inventory-list">
                @forelse ($foodListings as $foodListing)
                    <article class="vendor-listing-card">
                        @if ($foodListing->image_path)
                            <a class="vendor-listing-image-link" href="#food-image-{{ $foodListing->id }}">
                                <img src="{{ asset('storage/' . $foodListing->image_path) }}" alt="{{ $foodListing->item_name }}">
                            </a>
                            <div class="food-image-modal" id="food-image-{{ $foodListing->id }}">
                                <a class="food-image-modal-close" href="#">Close</a>
                                <img src="{{ asset('storage/' . $foodListing->image_path) }}" alt="{{ $foodListing->item_name }}">
                            </div>
                        @else
                            <div class="vendor-listing-image-placeholder">No image</div>
                        @endif
                        <div class="vendor-listing-details">
                            <div class="vendor-listing-title">
                                <div>
                                    <h3>{{ $foodListing->item_name }}</h3>
                                    <p>{{ $vendorOutlet->outlet_name }}, {{ $vendorOutlet->outlet_location }}</p>
                                </div>
                                @if ($foodListing->status === 'active')
                                    <span class="vendor-status vendor-status-live">Live</span>
                                @else
                                    <span class="vendor-status vendor-status-expired">{{ ucfirst($foodListing->status) }}</span>
                                @endif
                            </div>
                            <div class="vendor-listing-information">
                                <span>Quantity: {{ $foodListing->quantity }}</span>
                                <span>Reservations: {{ $foodListing->reservations_count }}</span>
                                <span>Expires: {{ $foodListing->expiry_time->format('g:i A') }}</span>
                            </div>
                            <div class="vendor-listing-actions">
                                <a class="vendor-edit-image-button" href="{{ route('vendor.food-listings.image.edit', $foodListing) }}">Edit image</a>
                                <form method="POST" action="{{ route('vendor.food-listings.destroy', $foodListing) }}" onsubmit="return confirm('Delete this food listing?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="vendor-delete-button" type="submit">Delete</button>
                                </form>
                            </div>
                        </div>
                        <div class="vendor-listing-price">
                            <strong>RM {{ $foodListing->discounted_price }}</strong>
                            <span>RM {{ $foodListing->original_price }}</span>
                        </div>
                    </article>
                @empty
                    <p class="vendor-note">No food listings have been added for this outlet yet.</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection
