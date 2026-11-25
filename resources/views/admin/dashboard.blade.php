{{-- Admin Analytics Dashboard (RETURN VIEW) --}}

@extends('layouts.admin')

@section('title', 'Analytics | RescueBite Admin Panel')

@section('content')
    @include('admin.partials.panel-overview', ['currentTab' => 'analytics'])

    <section class="admin-tab-section">
        <div class="container">
            <div class="admin-analytics-grid">
                <section class="admin-card" aria-labelledby="vendor-breakdown-title">
                    <div class="admin-card-header">
                        <h2 id="vendor-breakdown-title">Vendor Breakdown</h2>
                        <p>Total Listing Per Vendor</p>
                    </div>

                    <div class="admin-table-wrapper">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Vendor</th>
                                    <th>Total Listed</th>
                                    <th>In Stock</th>
                                    <th>Sold Out</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($vendorBreakdown as $vendorRow)
                                    <tr>
                                        <td>{{ $vendorRow['outletName'] }}</td>
                                        <td class="admin-cell-purple">{{ $vendorRow['totalListed'] }}</td>
                                        <td class="admin-cell-green">{{ $vendorRow['inStockCount'] }}</td>
                                        <td class="admin-cell-red">{{ $vendorRow['soldOutCount'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="admin-empty-cell">No vendor outlets registered yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="admin-card" aria-labelledby="category-breakdown-title">
                    <div class="admin-card-header">
                        <h2 id="category-breakdown-title">Category Breakdown</h2>
                        <p>Food Count By Category</p>
                    </div>

                    <div class="admin-category-grid">
                        @forelse ($categoryBreakdown as $categoryItem)
                            <article class="admin-category-tile {{ $categoryItem['styleClass'] }}">
                                <span class="admin-category-name">{{ strtoupper($categoryItem['category']) }}</span>
                                <div class="admin-category-numbers">
                                    <strong>{{ $categoryItem['listingCount'] }}</strong>
                                    <span>{{ $categoryItem['percentage'] }}%</span>
                                </div>
                            </article>
                        @empty
                            <p class="admin-empty-note">No food listings added yet.</p>
                        @endforelse
                    </div>
                </section>
            </div>

            <section class="admin-card admin-waste-card" aria-labelledby="waste-analytics-title">
                <div class="admin-card-header">
                    <h2 id="waste-analytics-title">Waste &amp; Rescue Analytics</h2>
                    <p>Claimed, Unclaimed, and Expired Overview</p>
                </div>

                <div class="admin-waste-grid">
                    <article class="admin-waste-metric">
                        <span>Claimed Rescues</span>
                        <strong class="admin-cell-green">{{ $wasteAnalytics['claimedCount'] }}</strong>
                    </article>
                    <article class="admin-waste-metric">
                        <span>Unclaimed (Reserved)</span>
                        <strong class="admin-cell-amber">{{ $wasteAnalytics['unclaimedCount'] }}</strong>
                    </article>
                    <article class="admin-waste-metric">
                        <span>Expired Reservations</span>
                        <strong class="admin-cell-red">{{ $wasteAnalytics['expiredReservationCount'] }}</strong>
                    </article>
                    <article class="admin-waste-metric">
                        <span>Expired Listings (Qty Left)</span>
                        <strong class="admin-cell-red">{{ $wasteAnalytics['expiredListingCount'] }} ({{ $wasteAnalytics['expiredQuantityLeft'] }})</strong>
                    </article>
                    <article class="admin-waste-metric">
                        <span>Pickup Completion Rate</span>
                        <strong class="admin-cell-purple">{{ $wasteAnalytics['rescueRate'] }}%</strong>
                    </article>
                </div>
            </section>

            <section class="admin-card admin-reservations-card" aria-labelledby="recent-reservations-title">
                <div class="admin-card-header">
                    <h2 id="recent-reservations-title">Recent Reservations</h2>
                    <p>Latest consumer pickup activity</p>
                </div>

                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Item</th>
                                <th>Outlet</th>
                                <th>Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentReservations as $reservation)
                                <tr>
                                    <td class="admin-cell-green">{{ $reservation->verify_code }}</td>
                                    <td>
                                        @if ($reservation->foodListing)
                                            {{ $reservation->foodListing->item_name }}
                                        @else
                                            Listing removed
                                        @endif
                                    </td>
                                    <td>
                                        @if ($reservation->foodListing && $reservation->foodListing->vendorOutlet)
                                            {{ $reservation->foodListing->vendorOutlet->outlet_name }}
                                        @else
                                            Unknown outlet
                                        @endif
                                    </td>
                                    <td class="admin-cell-green">
                                        @if ($reservation->foodListing)
                                            RM {{ number_format($reservation->foodListing->discounted_price, 2) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($reservation->status === 'claimed')
                                            <span class="admin-status-badge admin-status-claimed">Claimed</span>
                                        @elseif ($reservation->status === 'expired')
                                            <span class="admin-status-badge admin-status-expired">Expired</span>
                                        @else
                                            <span class="admin-status-badge admin-status-active">Active</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="admin-empty-cell">No reservations recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </section>
@endsection
