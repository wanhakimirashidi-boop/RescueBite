{{-- SHARED TEMPLATE FOR PUBLIC HEADER "@include" --}}
{{-- THIS PUBLIC-HEADER & FOOTER ONLY USED IN GUEST PAGES ONLY --}}

<header class="site-header">
    <div class="container navigation">

        {{-- makes the logo clickable to homepage || url('/') refer to web.php line 7 which is homepage --}}
        <a class="brand" href="{{ url('/') }}" aria-label="RescueBite home">
            <span class="brand-mark" aria-hidden="true">R</span>
            <span>Rescue<span>Bite</span></span>
        </a>

        <nav class="nav-actions" aria-label="Account navigation">
            @if (Route::has('login'))
                <a class="login-link" href="{{ route('login') }}">Log in</a>
            @endif

            @if (Route::has('register'))
                <a class="button button-small" href="{{ route('register') }}">Sign up</a>
            @endif
        </nav>
    </div>
</header>
