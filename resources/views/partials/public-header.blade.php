{{-- THIS IS FOR PUBLIC HEADER "@include" --}}

<header class="site-header">
    <div class="container navigation">
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
