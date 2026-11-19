{{-- Rescue Pagination - Used by Active Rescues and Rescue History --}}

@if ($reservations->hasPages())
    <nav class="rescue-pagination" aria-label="Rescue list pages">
        @if ($reservations->onFirstPage())
            <span class="rescue-pagination-button rescue-pagination-disabled">Previous</span>   {{-- disable button --}}
        @else
            <a class="rescue-pagination-button" href="{{ $reservations->previousPageUrl() }}">Previous</a>
        @endif

        {{-- Page numbers --}}
        @for ($pageNumber = 1; $pageNumber <= $reservations->lastPage(); $pageNumber++)
            @if ($pageNumber === $reservations->currentPage())
                <span class="rescue-pagination-page rescue-pagination-current">{{ $pageNumber }}</span>
            @else
                <a class="rescue-pagination-page" href="{{ $reservations->url($pageNumber) }}">{{ $pageNumber }}</a>
            @endif
        @endfor

        @if ($reservations->hasMorePages())
            <a class="rescue-pagination-button" href="{{ $reservations->nextPageUrl() }}">Next</a>
        @else
            <span class="rescue-pagination-button rescue-pagination-disabled">Next</span>
        @endif
    </nav>
@endif
