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
                <button class="vendor-tab" type="button">Add Surplus</button>
                <button class="vendor-tab" type="button">Verify Code</button>
            </nav>

            <div class="vendor-list-heading">
                <div>
                    <p class="eyebrow">Today&apos;s surplus</p>
                    <h2 id="inventory-title">My listings</h2>
                </div>
                <button class="vendor-add-button" type="button">Add surplus</button>
            </div>

            <div class="vendor-inventory-list">
                @forelse ($foodListings as $foodListing)
                    <article class="vendor-listing-card">
                        <img src="{{ $foodListing->image_url }}" alt="{{ $foodListing->item_name }}">
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
