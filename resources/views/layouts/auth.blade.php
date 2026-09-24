{{-- SHARED TEMPLATE FOR AUTHENTICATION PAGES - Login Page and Registration Page --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'RescueBite')</title>
    <link rel="stylesheet" href="{{ asset('css/guest-home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="auth-page">
    @include('partials.public-header')

    <main class="auth-main">
        <div class="auth-card">
            @yield('content')  {{-- @yield = leave blank space right here && login.blade "@section('content')" paste it there--}}
        </div>
    </main>
</body>
</html>
