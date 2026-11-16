@extends('layouts.consumer')

@section('title', 'Confirm Reservation | RescueBite')

@section('content')

{{-- CONFIRMATION PAGE when reserve food --}}
    <section class="rescues-banner">
        <div class="container">
            <p class="eyebrow">One more step</p>
            <h1>Confirm your reservation</h1>
            <p>Review the food and pickup details before reserving.</p>
        </div>
    </section>

    <section class="reservation-confirmation-content">
        <div class="container">
            <article class="reservation-confirmation-card">

                {{-- display food image --}}
                @if ($foodListing->image_path)
                    <img src="{{ asset('storage/' . $foodListing->image_path) }}" alt="{{ $foodListing->item_name }}">
                @else
                    <div class="consumer-listing-image-placeholder">No image</div>
                @endif

                <div class="reservation-confirmation-details">
                    <p class="food-vendor">{{ $foodListing->vendorOutlet->outlet_name }}</p>
                    <h2>{{ $foodListing->item_name }}</h2>
                    <p class="food-location">{{ $foodListing->vendorOutlet->outlet_location }}</p>
                    <p class="reservation-confirmation-pickup">Pick up before {{ $foodListing->expiry_time->format('g:i A') }}</p>
                    <p class="reservation-confirmation-quantity">{{ $foodListing->quantity }} available</p>
                    <p class="reservation-confirmation-price">Price: RM {{ $foodListing->discounted_price }}</p>

                    <div class="reservation-confirmation-actions">
                        <a class="consumer-back-button" href="{{ route('dashboard') }}">&larr; Go Back</a>  {{-- &larr = left-pointing arrow --}}
                        <form method="POST" action="{{ route('consumer.reservations.store', $foodListing) }}">
                            @csrf
                            <button class="button consumer-confirm-button" type="submit">Confirm Reservation</button>
                        </form>
                    </div>
                </div>
            </article>
        </div>
    </section>
@endsection
