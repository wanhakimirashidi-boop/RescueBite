{{-- Consumer Dashboard (RETURN VIEW) --}}

{{-- Consumer Dashboard - Shared template with consumer.blade.php --}}

@extends('layouts.consumer')

@section('title', 'Browse Food | RescueBite')

@section('content') {{-- @yield in consumer.blade = leave blank space right there && dashboard.blade "@section('content')" paste it there --}}
    <section class="consumer-hero">
        <div class="container consumer-hero-content">
            <p class="eyebrow">Rescue a meal today</p>
            <h1>Good for you.<br><span>Good for Earth.</span></h1>
            <p>Browse discounted surplus food from nearby outlets before it goes to waste.</p>
        </div>
    </section>

    <section class="vendor-filter" aria-label="Filter by vendor">
        <div class="container filter-list">
            <button class="filter-button filter-button-active" type="button">All</button>
            <button class="filter-button" type="button">Tealive</button>
            <button class="filter-button" type="button">Subway</button>
            <button class="filter-button" type="button">Domino's</button>
            <button class="filter-button" type="button">Kopitiam</button>
            <button class="filter-button" type="button">Family Mart</button>
        </div>
    </section>

    <section class="consumer-listings" aria-labelledby="available-food-title">
        <div class="container">
            <div class="listing-toolbar">
                <div>
                    <p class="eyebrow">Available now</p>
                    <h2 id="available-food-title">Food near you</h2>
                </div>
                <div class="listing-search">
                    <label class="screen-reader-only" for="food-search">Search food</label>
                    <input id="food-search" type="search" placeholder="Search food or outlet">
                </div>
            </div>

            <div class="category-list" aria-label="Filter by category">
                <button class="category-button category-button-active" type="button">All</button>
                <button class="category-button" type="button">Pastries</button>
                <button class="category-button" type="button">Food</button>
                <button class="category-button" type="button">Snacks</button>
            </div>

            <div class="consumer-food-grid">
                <article class="consumer-food-card">
                    <img src="https://images.unsplash.com/photo-1528735602780-2552fd46c7af?auto=format&amp;fit=crop&amp;w=700&amp;q=80" alt="Chicken wrap set">
                    <div class="consumer-food-content">
                        <p class="food-vendor">Tealive</p>
                        <h3>Chicken Wrap Set</h3>
                        <p class="food-location">Sri Kembangan</p>
                        <div class="food-details"><span>Pick up by 8:30 PM</span><span>2 left</span></div>
                        <div class="consumer-price"><strong>RM 6.50</strong><span>RM 12.00</span></div>
                        <button class="button consumer-reserve-button" type="button">Reserve</button>
                    </div>
                </article>

                <article class="consumer-food-card">
                    <img src="https://images.unsplash.com/photo-1550547660-d9450f859349?auto=format&amp;fit=crop&amp;w=700&amp;q=80" alt="Beef cheese sandwich">
                    <div class="consumer-food-content">
                        <p class="food-vendor">Subway</p>
                        <h3>Beef Cheese Sub</h3>
                        <p class="food-location">EVO Mall</p>
                        <div class="food-details"><span>Pick up by 9:00 PM</span><span>4 left</span></div>
                        <div class="consumer-price"><strong>RM 6.00</strong><span>RM 11.50</span></div>
                        <button class="button consumer-reserve-button" type="button">Reserve</button>
                    </div>
                </article>

                <article class="consumer-food-card">
                    <img src="https://images.unsplash.com/photo-1565299585323-38d6b0865b47?auto=format&amp;fit=crop&amp;w=700&amp;q=80" alt="Personal pizza">
                    <div class="consumer-food-content">
                        <p class="food-vendor">Domino's</p>
                        <h3>Personal Pizza</h3>
                        <p class="food-location">Bangi Seksyen 3</p>
                        <div class="food-details"><span>Pick up by 8:00 PM</span><span>3 left</span></div>
                        <div class="consumer-price"><strong>RM 8.90</strong><span>RM 16.00</span></div>
                        <button class="button consumer-reserve-button" type="button">Reserve</button>
                    </div>
                </article>

                <article class="consumer-food-card">
                    <img src="https://images.unsplash.com/photo-1515003197210-e0cd71810b5f?auto=format&amp;fit=crop&amp;w=700&amp;q=80" alt="Fresh pastries">
                    <div class="consumer-food-content">
                        <p class="food-vendor">Family Mart</p>
                        <h3>Pastry Box</h3>
                        <p class="food-location">IOI City Mall</p>
                        <div class="food-details"><span>Pick up by 9:30 PM</span><span>5 left</span></div>
                        <div class="consumer-price"><strong>RM 5.00</strong><span>RM 10.00</span></div>
                        <button class="button consumer-reserve-button" type="button">Reserve</button>
                    </div>
                </article>
            </div>

            <p class="dashboard-note">Reservation, search, and filters will become functional after the RescueBite database is added.</p>
        </div>
    </section>
@endsection
