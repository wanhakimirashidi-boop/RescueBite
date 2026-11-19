{{-- Rescue List Navigation - Active Rescues & Rescue History --}}

{{-- Navigation tabs for switching between active rescues and rescue history --}}
<div class="rescue-list-toolbar">
    <div>
        {{-- if user already clicked Active Rescues --}}
        @if ($currentList === 'active')
            <p class="eyebrow">Ready for pickup</p>
            <h2 id="active-rescues-title">Active rescues</h2>

        {{-- if user already clicked Rescue History --}}
        @else
            <p class="eyebrow">Past activity</p>
            <h2 id="rescue-history-title">Rescue history</h2>
        @endif
    </div>

    {{-- Search bar --}}
    @if ($currentList === 'active')
        <form class="rescue-search" method="GET" action="{{ route('consumer.rescues') }}">
    @else
        <form class="rescue-search" method="GET" action="{{ route('consumer.rescues.history') }}">
    @endif
            <label class="screen-reader-only" for="rescue-search">Search rescues</label>
            <input id="rescue-search" name="search" type="search" value="{{ $searchTerm }}" placeholder="Search meal or outlet">
            <button type="submit">Search</button>
        </form>
</div>

{{-- Navigation tabs for switching between active rescues and rescue history --}}
<nav class="rescue-list-tabs" aria-label="My Rescues lists">

    {{-- if user already clicked Active Resuces --}}
    @if ($currentList === 'active')
        <a class="rescue-list-tab rescue-list-tab-active" href="{{ route('consumer.rescues') }}">Active Rescues</a>
        <a class="rescue-list-tab" href="{{ route('consumer.rescues.history') }}">Rescue History</a>

    {{-- if user already clicked Rescue History --}}
    @else
        <a class="rescue-list-tab" href="{{ route('consumer.rescues') }}">Active Rescues</a>
        <a class="rescue-list-tab rescue-list-tab-active" href="{{ route('consumer.rescues.history') }}">Rescue History</a>
    @endif
</nav>

<a class="consumer-back-button rescue-back-button" href="{{ route('dashboard') }}">&larr; Back to Browse Food</a>
