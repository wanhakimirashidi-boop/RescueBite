{{-- Consumer Active Rescues (RETURN VIEW) --}}

{{-- Active Rescues page--}}

@extends('layouts.consumer')

@section('title', 'Active Rescues | RescueBite')

@section('content')
    <section class="rescues-banner">
        <div class="container">
            <p class="eyebrow">Your saved meals</p>
            <h1>My Rescues</h1>
            <p>Keep your pickup code ready when you arrive at the outlet.</p>
        </div>
    </section>

    <section class="rescues-content" aria-labelledby="active-rescues-title">
        <div class="container">

            {{-- Navigation tabs for switching between active rescues and rescue history --}}
            @include('consumer.partials.rescue-list-navigation', ['currentList' => 'active'])
            {{-- temp variable $currentList = 'active' for rescue-list-navigation to decide which UI to display rescues.blade or rescue-history.blade --}}

            @if (session('success'))
                <p class="consumer-success-message">{{ session('success') }}</p>
            @endif

            <div class="rescue-list">
                @forelse ($reservations as $reservation)
                    <article class="rescue-card">

                        {{-- Display food image --}}
                        @if ($reservation->foodListing->image_path)
                            <img src="{{ asset('storage/' . $reservation->foodListing->image_path) }}" alt="{{ $reservation->foodListing->item_name }}">
                        @else
                            <div class="rescue-listing-image-placeholder">No image</div>
                        @endif
                        {{-- foodListing is from Reservation.php --}}

                        {{-- Display food details --}}
                        <div class="rescue-card-details">
                            
                            {{-- to read: starting from "$reservation", find its food listing, then find that food listing's vendor outlet--}}
                            <p class="food-vendor">{{ $reservation->foodListing->vendorOutlet->outlet_name }}</p> {{-- refer to database --}}
                            <h3>{{ $reservation->foodListing->item_name }}</h3> {{-- Food Item Name --}}
                            <p class="food-location">{{ $reservation->foodListing->vendorOutlet->outlet_location }}</p> {{-- Vendor Outlet Location --}}
                            <p class="rescue-pickup-time">Pick up before {{ $reservation->foodListing->expiry_time->format('g:i A') }}</p> {{-- Food Expiry Time --}}
                        </div>
                        <div class="rescue-code-box">
                            <span class="rescue-status rescue-status-active">Reserved</span>
                            <p>Show this pickup code</p>
                            <strong>{{ $reservation->verify_code }}</strong>
                        </div>
                    </article>
                @empty
                    <p class="dashboard-note">You do not have any active rescues yet.</p>
                @endforelse
            </div>

            @include('consumer.partials.rescue-pagination')

            @if ($recentExpiredReservations->count() > 0)
                <div class="rescue-section-heading rescue-expiry-heading">
                    <div>
                        <p class="eyebrow">Missed Pickup Expiries</p>
                        <h2>Recent Expiries</h2>
                    </div>
                    <span class="rescue-count rescue-expiry-count">Last 3 days</span>
                </div>

                <div class="rescue-list">
                    @foreach ($recentExpiredReservations as $reservation)
                        <article class="rescue-card rescue-card-expired">
                            @if ($reservation->foodListing->image_path)
                                <img src="{{ asset('storage/' . $reservation->foodListing->image_path) }}" alt="{{ $reservation->foodListing->item_name }}">
                            @else
                                <div class="rescue-listing-image-placeholder">No image</div>
                            @endif

                            <div class="rescue-card-details">
                                <p class="food-vendor">{{ $reservation->foodListing->vendorOutlet->outlet_name }}</p>
                                <h3>{{ $reservation->foodListing->item_name }}</h3>
                                <p class="food-location">{{ $reservation->foodListing->vendorOutlet->outlet_location }}</p>
                                <p class="rescue-pickup-time">Expired on {{ $reservation->foodListing->expiry_time->format('d F Y, g:i A') }}</p>
                            </div>
                            <div class="rescue-code-box">
                                <span class="rescue-status rescue-status-expired">Expired</span>
                                <p>Pickup no longer available</p>
                                <strong>RM {{ $reservation->foodListing->discounted_price }}</strong>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
