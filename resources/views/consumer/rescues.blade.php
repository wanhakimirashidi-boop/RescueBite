{{-- My Rescues Page --}}

@extends('layouts.consumer')

@section('title', 'My Rescues | RescueBite')

@section('content')
    <section class="rescues-banner">
        <div class="container">
            <p class="eyebrow">Your saved meals</p>
            <h1>My Rescues</h1>
            <p>Keep your pickup code ready when you arrive at the outlet.</p>
        </div>
    </section>

    {{-- temporary hard coded --}}
    <section class="rescues-content" aria-labelledby="active-rescues-title">
        <div class="container">
            <div class="rescue-section-heading">
                <div>
                    <p class="eyebrow">Ready for pickup</p>
                    <h2 id="active-rescues-title">Active rescues</h2>
                </div>
                <span class="rescue-count">1 active</span>
            </div>

            <article class="rescue-card">
                <img src="https://images.unsplash.com/photo-1528735602780-2552fd46c7af?auto=format&amp;fit=crop&amp;w=700&amp;q=80" alt="Chicken wrap set">
                <div class="rescue-card-details">
                    <p class="food-vendor">Tealive</p>
                    <h3>Chicken Wrap Set</h3>
                    <p class="food-location">Sri Kembangan</p>
                    <p class="rescue-pickup-time">Pick up before 8:30 PM today</p>
                </div>
                <div class="rescue-code-box">
                    <span class="rescue-status rescue-status-active">Reserved</span>
                    <p>Show this pickup code</p>
                    <strong>RB-8B0T46</strong>
                </div>
            </article>

            <div class="rescue-section-heading rescue-history-heading">
                <div>
                    <p class="eyebrow">Past activity</p>
                    <h2>Rescue history</h2>
                </div>
            </div>

            <article class="rescue-card rescue-card-history">
                <img src="https://images.unsplash.com/photo-1550547660-d9450f859349?auto=format&amp;fit=crop&amp;w=700&amp;q=80" alt="Beef cheese sandwich">
                <div class="rescue-card-details">
                    <p class="food-vendor">Subway</p>
                    <h3>Beef Cheese Sub</h3>
                    <p class="food-location">EVO Mall</p>
                    <p class="rescue-pickup-time">Collected on 18 September 2026</p>
                </div>
                <div class="rescue-code-box">
                    <span class="rescue-status rescue-status-claimed">Claimed</span>
                    <p>Pickup completed</p>
                    <strong>RM 6.00</strong>
                </div>
            </article>

            <p class="dashboard-note">These reservations are sample data. Your real rescue history will appear here after database functionality is added.</p>
        </div>
    </section>
@endsection
