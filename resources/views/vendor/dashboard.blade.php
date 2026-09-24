{{-- Vendor Dashboard (RETURN VIEW) --}}

{{-- Shared template with vendor.blade.php --}}

@extends('layouts.vendor')

@section('title', 'Inventory | RescueBite Vendor Panel')

@section('content')
    <section class="vendor-hero">
        <div class="container">
            <p class="eyebrow">Vendor workspace</p>
            <h1>Staff <span>Panel</span></h1>
            <p>Manage today&apos;s surplus food and prepare orders for collection.</p>
        </div>
    </section>

    <section class="vendor-content" aria-labelledby="inventory-title">
        <div class="container">
            <div class="vendor-statistics">
                <article class="vendor-statistic">
                    <span>My listings</span>
                    <strong>3</strong>
                </article>
                <article class="vendor-statistic">
                    <span>Total reserved</span>
                    <strong>3</strong>
                </article>
                <article class="vendor-statistic">
                    <span>Claimed</span>
                    <strong>1</strong>
                </article>
            </div>

            <nav class="vendor-tabs" aria-label="Vendor panel sections">
                <button class="vendor-tab vendor-tab-active" type="button">Inventory</button>
                <button class="vendor-tab" type="button">Add Surplus</button>
                <button class="vendor-tab" type="button">Verify Code</button>
            </nav>

            <div class="vendor-list-heading">
                <div>
                    <p class="eyebrow">Today&apos;s surplus</p>
                    <h2 id="inventory-title">My listings</h2>
                </div>
                <button class="vendor-add-button" type="button">Add surplus</button>
            </div>

            <div class="vendor-inventory-list">
                <article class="vendor-listing-card">
                    <img src="https://images.unsplash.com/photo-1515003197210-e0cd71810b5f?auto=format&amp;fit=crop&amp;w=700&amp;q=80" alt="Fresh pastry box">
                    <div class="vendor-listing-details">
                        <div class="vendor-listing-title">
                            <div>
                                <h3>Pastry Box</h3>
                                <p>Family Mart, IOI City Mall</p>
                            </div>
                            <span class="vendor-status vendor-status-live">Live</span>
                        </div>
                        <div class="vendor-listing-information">
                            <span>Quantity: 5</span>
                            <span>Reserved: 2</span>
                            <span>Expires: 9:30 PM</span>
                        </div>
                    </div>
                    <div class="vendor-listing-price">
                        <strong>RM 5.00</strong>
                        <span>RM 10.00</span>
                    </div>
                </article>

                <article class="vendor-listing-card">
                    <img src="https://images.unsplash.com/photo-1528735602780-2552fd46c7af?auto=format&amp;fit=crop&amp;w=700&amp;q=80" alt="Chicken wrap set">
                    <div class="vendor-listing-details">
                        <div class="vendor-listing-title">
                            <div>
                                <h3>Chicken Wrap Set</h3>
                                <p>Tealive, Sri Kembangan</p>
                            </div>
                            <span class="vendor-status vendor-status-live">Live</span>
                        </div>
                        <div class="vendor-listing-information">
                            <span>Quantity: 2</span>
                            <span>Reserved: 1</span>
                            <span>Expires: 8:30 PM</span>
                        </div>
                    </div>
                    <div class="vendor-listing-price">
                        <strong>RM 6.50</strong>
                        <span>RM 12.00</span>
                    </div>
                </article>

                <article class="vendor-listing-card vendor-listing-expired">
                    <img src="https://images.unsplash.com/photo-1565299585323-38d6b0865b47?auto=format&amp;fit=crop&amp;w=700&amp;q=80" alt="Personal pizza">
                    <div class="vendor-listing-details">
                        <div class="vendor-listing-title">
                            <div>
                                <h3>Personal Pizza</h3>
                                <p>Domino&apos;s, Bangi Seksyen 3</p>
                            </div>
                            <span class="vendor-status vendor-status-expired">Expired</span>
                        </div>
                        <div class="vendor-listing-information">
                            <span>Quantity: 0</span>
                            <span>Reserved: 0</span>
                            <span>Expired: 7:00 PM</span>
                        </div>
                    </div>
                    <div class="vendor-listing-price">
                        <strong>RM 8.90</strong>
                        <span>RM 16.00</span>
                    </div>
                </article>
            </div>

            <p class="vendor-note">Listings are sample data. Adding surplus and verification will be built as the next vendor steps.</p>
        </div>
    </section>
@endsection
