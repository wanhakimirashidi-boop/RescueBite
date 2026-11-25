{{-- Admin Panel Shared Layout --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel | RescueBite')</title>
    <link rel="icon" type="image/png" href="{{ asset('rescuebite.png') }}">
    <link rel="stylesheet" href="{{ asset('css/guest-home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-panel.css') }}">
</head>
<body class="admin-page">
    <header class="admin-header">
        <div class="container admin-navigation">
            <a class="brand" href="{{ route('admin.dashboard') }}" aria-label="RescueBite admin panel">
                <img src="{{ asset('rescuebite.png') }}" alt="RescueBite" class="brand-mark">
                <span>Rescue<span>Bite</span></span>
            </a>

            <nav class="admin-actions" aria-label="Admin navigation">
                <span class="admin-greeting">Hi, {{ explode(' ', auth()->user()->name)[0] }}!</span>
                <span class="admin-panel-label">Admin Panel</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="admin-logout-button" type="submit">Logout</button>
                </form>
            </nav>
        </div>
    </header>

    @yield('content')
</body>
</html>
