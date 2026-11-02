{{-- Consumer Dashboard (RETURN VIEW) --}}

{{-- Consumer Dashboard - Shared template with consumer.blade.php --}}

@extends('layouts.consumer')

@section('title', 'Browse Food | RescueBite')

@section('content') {{-- @yield in consumer.blade = leave blank space right there && dashboard.blade "@section('content')" paste it there --}}
    <section class="consumer-hero">
        <div class="container consumer-hero-content">
            <p class="eyebrow">Rescue a meal today</p>
            <h1>Good for you.<br><span>Good for Earth</span></h1>
            <p>Browse discounted surplus food from nearby outlets before it goes to waste.</p>
        </div>
    </section>

    <section class="consumer-listings" aria-labelledby="available-food-title">
        <div class="container">
            <div class="listing-toolbar">
                <div>
                    <p class="eyebrow">Available now</p>
                    <h2 id="available-food-title">Food near you</h2>
                </div>
            </div>

            <div class="consumer-food-grid">
                @forelse ($foodListings as $foodListing)
                    <article class="consumer-food-card">
                        @if ($foodListing->image_path)
                            <img src="{{ asset('storage/' . $foodListing->image_path) }}" alt="{{ $foodListing->item_name }}">
                        @else
                            <div class="consumer-listing-image-placeholder">No image</div>
                        @endif
                        <div class="consumer-food-content">
                            <p class="food-vendor">{{ $foodListing->vendorOutlet->outlet_name }}</p>
                            <h3>{{ $foodListing->item_name }}</h3>
                            <p class="food-location">{{ $foodListing->vendorOutlet->outlet_location }}</p>
                            <div class="food-details"><span>Pick up by {{ $foodListing->expiry_time->format('g:i A') }}</span><span>{{ $foodListing->quantity }} left</span></div>
                            <div class="consumer-price"><strong>RM {{ $foodListing->discounted_price }}</strong><span>RM {{ $foodListing->original_price }}</span></div>
                            <span class="consumer-reservation-note">Reservation will be added next.</span>
                        </div>
                    </article>
                @empty
                    <p class="dashboard-note">There are no active food listings right now. Please check again later.</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection
