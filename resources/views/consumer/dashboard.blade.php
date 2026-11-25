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

    <section class="vendor-filter">
        <div class="container">
            <nav class="filter-list" aria-label="Filter food by vendor outlet">
                @if ($selectedVendorOutletId)
                    <a class="filter-button" href="{{ route('dashboard', ['category' => $selectedCategory]) }}">All outlets</a>
                @else
                    <a class="filter-button filter-button-active" href="{{ route('dashboard', ['category' => $selectedCategory]) }}">All outlets</a>
                @endif

                @foreach ($primaryVendorOutlets as $vendorOutlet)
                    @if ($selectedVendorOutletId == $vendorOutlet->id)
                        <a class="filter-button filter-button-active" href="{{ route('dashboard', ['outlet' => $vendorOutlet->id, 'category' => $selectedCategory]) }}">{{ $vendorOutlet->outlet_name }}</a>
                    @else
                        <a class="filter-button" href="{{ route('dashboard', ['outlet' => $vendorOutlet->id, 'category' => $selectedCategory]) }}">{{ $vendorOutlet->outlet_name }}</a>
                    @endif
                @endforeach

                @if ($additionalVendorOutlets->count() > 0)
                    @if ($selectedAdditionalVendorOutlet)
                        <details class="vendor-filter-more" open>
                    @else
                        <details class="vendor-filter-more">
                    @endif
                            <summary class="filter-button vendor-filter-more-button">More outlets</summary>
                            <div class="vendor-filter-more-panel">
                                @foreach ($additionalVendorOutlets as $vendorOutlet)
                                    @if ($selectedVendorOutletId == $vendorOutlet->id)
                                        <a class="filter-button filter-button-active" href="{{ route('dashboard', ['outlet' => $vendorOutlet->id, 'category' => $selectedCategory]) }}">{{ $vendorOutlet->outlet_name }}</a>
                                    @else
                                        <a class="filter-button" href="{{ route('dashboard', ['outlet' => $vendorOutlet->id, 'category' => $selectedCategory]) }}">{{ $vendorOutlet->outlet_name }}</a>
                                    @endif
                                @endforeach
                            </div>
                        </details>
                @endif
            </nav>
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

            <nav class="category-list" aria-label="Filter food by category">
                @if ($selectedCategory)
                    <a class="category-button" href="{{ route('dashboard', ['outlet' => $selectedVendorOutletId]) }}">All categories</a>
                @else
                    <a class="category-button category-button-active" href="{{ route('dashboard', ['outlet' => $selectedVendorOutletId]) }}">All categories</a>
                @endif

                @foreach ($categories as $category)
                    @if ($selectedCategory === $category)
                        <a class="category-button category-button-active" href="{{ route('dashboard', ['outlet' => $selectedVendorOutletId, 'category' => $category]) }}">{{ $category }}</a>
                    @else
                        <a class="category-button" href="{{ route('dashboard', ['outlet' => $selectedVendorOutletId, 'category' => $category]) }}">{{ $category }}</a>
                    @endif
                @endforeach
            </nav>

            @if (session('error'))
                <p class="consumer-error-message">{{ session('error') }}</p>
            @endif

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
                            <a class="button consumer-reserve-button" href="{{ route('consumer.reservations.confirm', $foodListing) }}">Reserve</a>
                        </div>
                    </article>
                @empty
                    <p class="dashboard-note">There are no active food listings right now. Please check again later.</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection
