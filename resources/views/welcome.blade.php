@extends('layouts.public') {{-- public.blade.php - uses the shared public layout --}}

@section('title', 'RescueBite | Good Food, Less Waste')

{{-- @yield on public.blade = leave blank space right there && "@section('content')" paste it there--}}
@section('content')
        <section class="hero">
            <div class="container hero-content">
                <p class="eyebrow">Every meal deserves a second chance</p>
                <h1>Good for You<br><span>Good for Earth.</span></h1>
                <p class="hero-copy">Rescue quality surplus food from nearby cafes, food courts, and favourite brands before the day ends.</p>
                <a class="button" href="{{ route('register') }}">Start rescuing food</a>
            </div>
        </section>

        <section class="how-it-works" aria-labelledby="how-it-works-title">
            <div class="container">
                <div class="section-heading">
                    <p class="eyebrow">Simple, local, meaningful</p>
                    <h2 id="how-it-works-title">Save a meal in three steps</h2>
                </div>

                <div class="steps">
                    <article class="step">
                        <span class="step-number">01</span>
                        <h3>Browse nearby food</h3>
                        <p>Find discounted surplus meals listed by local vendors before closing time.</p>
                    </article>
                    <article class="step">
                        <span class="step-number">02</span>
                        <h3>Reserve at a lower price</h3>
                        <p>Choose a meal you like and reserve it through your RescueBite account.</p>
                    </article>
                    <article class="step">
                        <span class="step-number">03</span>
                        <h3>Pick up and enjoy</h3>
                        <p>Show your pickup code at the outlet and help keep great food out of the bin.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="featured-meals" aria-labelledby="featured-meals-title">
            <div class="container">
                <div class="section-heading section-heading-row">
                    <div>
                        <p class="eyebrow">Available today</p>
                        <h2 id="featured-meals-title">Rescue a meal near you</h2>
                    </div>
                    <a class="text-link" href="{{ route('login') }}">View all meals</a>
                </div>

                <div class="food-grid">
                    <article class="food-card">
                        <img src="https://images.unsplash.com/photo-1528735602780-2552fd46c7af?auto=format&amp;fit=crop&amp;w=700&amp;q=80" alt="A chicken wrap meal">
                        <div class="food-card-content">
                            <div class="food-card-topline"><span>Tealive</span><span>Until 8:30 PM</span></div>
                            <h3>Chicken Wrap Set</h3>
                            <p class="location">Sri Kembangan</p>
                            <div class="price-row"><strong>RM 6.50</strong><span>RM 12.00</span></div>
                            <a class="button button-card" href="{{ route('register') }}">Reserve now</a>
                        </div>
                    </article>

                    <article class="food-card">
                        <img src="https://images.unsplash.com/photo-1550547660-d9450f859349?auto=format&amp;fit=crop&amp;w=700&amp;q=80" alt="A fresh burger with vegetables">
                        <div class="food-card-content">
                            <div class="food-card-topline"><span>Subway</span><span>Until 9:00 PM</span></div>
                            <h3>Beef Cheese Sub</h3>
                            <p class="location">EVO Mall</p>
                            <div class="price-row"><strong>RM 6.00</strong><span>RM 11.50</span></div>
                            <a class="button button-card" href="{{ route('register') }}">Reserve now</a>
                        </div>
                    </article>

                    <article class="food-card">
                        <img src="https://images.unsplash.com/photo-1565299585323-38d6b0865b47?auto=format&amp;fit=crop&amp;w=700&amp;q=80" alt="A pizza topped with vegetables">
                        <div class="food-card-content">
                            <div class="food-card-topline"><span>Domino's</span><span>Until 8:00 PM</span></div>
                            <h3>Personal Pizza</h3>
                            <p class="location">Bangi Seksyen 3</p>
                            <div class="price-row"><strong>RM 8.90</strong><span>RM 16.00</span></div>
                            <a class="button button-card" href="{{ route('register') }}">Reserve now</a>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="student-banner" aria-labelledby="student-title">
            <div class="container student-content">
                <div>
                    <p class="eyebrow">UNITEN community</p>
                    <h2 id="student-title">Uniten Students can do more with less.</h2>
                    <p>Register to access student-focused meals and future campus perks.</p>
                </div>
                <a class="button button-light" href="{{ route('register') }}">Join RescueBite</a>
            </div>
        </section>
@endsection
