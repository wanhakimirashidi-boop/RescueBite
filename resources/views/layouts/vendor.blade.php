{{-- Vendor Dashboard Shared Template --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Vendor Panel | RescueBite')</title>
    <link rel="stylesheet" href="{{ asset('css/guest-home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vendor-panel.css') }}">
</head>
<body class="vendor-page">
    <header class="vendor-header">
        <div class="container vendor-navigation">
            <a class="brand" href="{{ route('vendor.dashboard') }}" aria-label="RescueBite vendor panel">
                <span class="brand-mark" aria-hidden="true">R</span>
                <span>Rescue<span>Bite</span></span>
            </a>

            <nav class="vendor-actions" aria-label="Vendor navigation">
                <span class="vendor-greeting">Hi, {{ auth()->user()->name }}</span>
                <span class="vendor-panel-label">Vendor Panel</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="vendor-logout-button" type="submit">Logout</button>
                </form>
            </nav>
        </div>
    </header>

    @yield('content')
</body>
</html>
