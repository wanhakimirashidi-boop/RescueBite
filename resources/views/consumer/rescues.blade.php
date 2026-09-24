{{-- Route web.php for UI display here --}}

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

            <div class="rescue-list">
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

                <article class="rescue-card">
                    <img src="https://images.unsplash.com/photo-1515003197210-e0cd71810b5f?auto=format&amp;fit=crop&amp;w=700&amp;q=80" alt="Fresh pastry box">
                    <div class="rescue-card-details">
                        <p class="food-vendor">Family Mart</p>
                        <h3>Pastry Box</h3>
                        <p class="food-location">IOI City Mall</p>
                        <p class="rescue-pickup-time">Pick up before 9:30 PM today</p>
                    </div>
                    <div class="rescue-code-box">
                        <span class="rescue-status rescue-status-active">Reserved</span>
                        <p>Show this pickup code</p>
                        <strong>RB-3C1N92</strong>
                    </div>
                </article>
            </div>
        </div>
    </section>
@endsection
