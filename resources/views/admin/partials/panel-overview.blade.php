{{-- Shared Hero, KPI Summary Cards, and Navigation Tabs for Admin Panel pages --}}

<section class="admin-hero">
    <div class="container admin-hero-inner">
        <a class="admin-back-pill" href="{{ url('/') }}">&larr; Back</a>
        <div class="admin-hero-title">
            <h1>Admin <span>Panel</span></h1>
            <p class="admin-hero-subtitle">ADMIN RESCUEBITE</p>
        </div>
    </div>
</section>

<section class="admin-overview">
    <div class="container">
        <div class="admin-statistics">
            <article class="admin-statistic">
                <span>Total Users</span>            {{-- %summaryStatistic is from AdminController.php (dashboard function)--}}
                <strong class="admin-statistic-white">{{ $summaryStatistics['totalUsers'] }}</strong>
                <small>{{ $summaryStatistics['consumerCount'] }} Consumers | {{ $summaryStatistics['vendorCount'] }} Vendors</small>
            </article>                               {{--totalUsers from AdminController.php -> "return [...]]" (getSummaryStatistics function)--}}
            <article class="admin-statistic">
                <span>Food Rescued</span>
                <strong class="admin-statistic-green">{{ $summaryStatistics['foodRescuedCount'] }}</strong>
                <small>(Items Claimed)</small>
            </article>
            <article class="admin-statistic">
                <span>Active Listing</span>
                <strong class="admin-statistic-amber">{{ $summaryStatistics['activeListingCount'] }}</strong>
            </article>
            <article class="admin-statistic">
                <span>Total Savings</span>
                <strong class="admin-statistic-pink">RM {{ number_format($summaryStatistics['totalSavings'], 2) }}</strong>
                <small>(Original - discount, claimed only)</small>
            </article>
        </div>

        <nav class="admin-tabs" aria-label="Admin panel sections">
            @if ($currentTab === 'analytics')
                <a class="admin-tab admin-tab-active" href="{{ route('admin.dashboard') }}">Analytics</a>
            @else
                <a class="admin-tab" href="{{ route('admin.dashboard') }}">Analytics</a>
            @endif

            <span class="admin-tab admin-tab-disabled" title="Coming in next step">Users</span>
            <span class="admin-tab admin-tab-disabled" title="Coming in next step">Reports</span>
        </nav>
    </div>
</section>
