{{-- SHARED TEMPLATE FOR PUBLIC DOCTYPE for all pages : "@include" --}}

<!DOCTYPE html>                 {{-- app()->getLocale() = checks Laravel settings to see what language (EN or en_US)--}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
 {{-- lang="{{ str_replace() = format those language with "-" not "_"--}}
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'RescueBite')</title>
    <link rel="stylesheet" href="{{ asset('css/guest-home.css') }}">
</head>
<body>
    @include('partials.public-header')

    <main>
        @yield('content')
    </main>

    @include('partials.public-footer')
</body>
</html>
