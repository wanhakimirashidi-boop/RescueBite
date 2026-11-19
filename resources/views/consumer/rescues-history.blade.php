{{-- Consumer Rescue History (RETURN VIEW) --}}

{{-- Rescue History page --}}

@extends('layouts.consumer')

@section('title', 'Rescue History | RescueBite')

@section('content') {{-- @yield in consumer.blade = leave blank space right there && dashboard.blade "@section('content')" paste it there --}}
    <section class="rescues-banner">
        <div class="container">
            <p class="eyebrow">Your saved meals</p>
            <h1>My Rescues</h1>
            <p>Review meals you have already collected from RescueBite vendors.</p>
        </div>
    </section>

    <section class="rescues-content" aria-labelledby="rescue-history-title">
        <div class="container">

            {{-- Navigation tabs for switching between active rescues and rescue history --}}
            @include('consumer.partials.rescue-list-navigation', ['currentList' => 'history'])
            {{-- temp variable $currentList = 'history' for rescue-list-navigation to decide which UI to display rescues.blade or rescue-history.blade --}}


            {{-- Rescue history items --}}
            <div class="rescue-list">
                @forelse ($reservations as $reservation)

                {{-- display image --}}
                    <article class="rescue-card rescue-card-history">
                        @if ($reservation->foodListing->image_path)
                            <img src="{{ asset('storage/' . $reservation->foodListing->image_path) }}" alt="{{ $reservation->foodListing->item_name }}">
                        @else
                            <div class="rescue-listing-image-placeholder">No image</div>
                        @endif

                        {{-- display details --}}
                        <div class="rescue-card-details">
                            <p class="food-vendor">{{ $reservation->foodListing->vendorOutlet->outlet_name }}</p>
                            <h3>{{ $reservation->foodListing->item_name }}</h3>
                            <p class="food-location">{{ $reservation->foodListing->vendorOutlet->outlet_location }}</p>
                            <p class="rescue-pickup-time">Collected on {{ $reservation->claimed_at->format('d F Y') }}</p>
                        </div>
                        <div class="rescue-code-box">
                            <span class="rescue-status rescue-status-claimed">Claimed</span>
                            <p>Pickup completed</p>
                            <strong>RM {{ $reservation->foodListing->discounted_price }}</strong>
                        </div>
                    </article>
                @empty
                    <p class="dashboard-note">You do not have any rescue history yet.</p>
                @endforelse
            </div>

            {{-- Paginations --}}
            @include('consumer.partials.rescue-pagination')
        </div>
    </section>
@endsection
