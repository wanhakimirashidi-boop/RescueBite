{{-- Route web.php for UI display here --}}

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


            <div class="rescue-list">
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

                <article class="rescue-card rescue-card-history">
                    <img src="https://images.unsplash.com/photo-1565299585323-38d6b0865b47?auto=format&amp;fit=crop&amp;w=700&amp;q=80" alt="Personal pizza">
                    <div class="rescue-card-details">
                        <p class="food-vendor">Domino's</p>
                        <h3>Personal Pizza</h3>
                        <p class="food-location">Bangi Seksyen 3</p>
                        <p class="rescue-pickup-time">Collected on 16 September 2026</p>
                    </div>
                    <div class="rescue-code-box">
                        <span class="rescue-status rescue-status-claimed">Claimed</span>
                        <p>Pickup completed</p>
                        <strong>RM 8.90</strong>
                    </div>
                </article>
            </div>

            <p class="dashboard-note">These reservations are sample data. Search and pagination will work with real reservation records after database functionality is added.</p>
        </div>
    </section>
@endsection
