{{-- Consumer Layout - Shared template with dashboard.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"> {{-- get Language and change the format to en-US --}}
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Consumer Dashboard | RescueBite')</title>
    <link rel="stylesheet" href="{{ asset('css/guest-home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/consumer-dashboard.css') }}">
</head>
<body class="consumer-page">
    <header class="consumer-header">
        <div class="container consumer-navigation">
            <a class="brand" href="{{ route('dashboard') }}" aria-label="RescueBite dashboard">
                <span class="brand-mark" aria-hidden="true">R</span>
                <span>Rescue<span>Bite</span></span>
            </a>

            <nav class="consumer-actions" aria-label="Consumer navigation">
                {{-- "explode" divide " " (space) into seperate characters and [0] to only display the first divided char --}}
                <span class="consumer-greeting">Hi, {{ explode(' ', auth()->user()->name)[0] }}</span>
                <a class="consumer-nav-button" href="{{ route('consumer.rescues') }}">My Rescues</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="consumer-nav-button" type="submit">Logout</button>
                </form>
            </nav>
        </div>
    </header>

    @yield('content') {{-- @yield = leave blank space right here && dashboard.blade "@section('content')" paste it there--}}
</body>
</html>
