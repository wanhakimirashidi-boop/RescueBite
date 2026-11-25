{{-- Shared pagination controls for Admin tables (Vendor Breakdown, Recent Reservations, etc.) --}}

@if ($records->hasPages())
    <div class="admin-pagination-bar">
        <span class="admin-pagination-info">
            Page {{ $records->currentPage() }} of {{ $records->lastPage() }}
        </span>

        <nav class="admin-pagination" aria-label="{{ $paginationLabel }}">
            @if ($records->onFirstPage())
                <span class="admin-pagination-button admin-pagination-disabled">Previous</span>
            @else
                <a class="admin-pagination-button" href="{{ $records->previousPageUrl() }}">Previous</a>
            @endif

            @for ($pageNumber = 1; $pageNumber <= $records->lastPage(); $pageNumber++)
                @if ($pageNumber === $records->currentPage())
                    <span class="admin-pagination-page admin-pagination-current">{{ $pageNumber }}</span>
                @else
                    <a class="admin-pagination-page" href="{{ $records->url($pageNumber) }}">{{ $pageNumber }}</a>
                @endif
            @endfor

            @if ($records->hasMorePages())
                <a class="admin-pagination-button" href="{{ $records->nextPageUrl() }}">Next</a>
            @else
                <span class="admin-pagination-button admin-pagination-disabled">Next</span>
            @endif
        </nav>
    </div>
@endif
