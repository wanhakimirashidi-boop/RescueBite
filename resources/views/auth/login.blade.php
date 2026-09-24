{{-- this file is placeholder contents for the MAIN auth.blade --}}
@extends('layouts.auth') {{-- auth.blade.php - uses the shared authentication layout --}}

{{-- @yield('title', 'RescueBite') in auth.blade = "define page title there. If a page doesn't have one, use 'RescueBite' as the default --}}
{{-- @section('title', 'Log in | RescueBite') in login.blade = "Pass this exact text to auth.blade to fill the 'title' placeholder --}}
@section('title', 'Log in | RescueBite')

{{-- @yield on auth.blade = leave blank space right there && "@section('content')" paste it there--}}
@section('content')
    <h1 class="auth-heading">Welcome back</h1>
    <p class="auth-introduction">Log in to discover affordable surplus food near you.</p>

    <!-- Session Status : checks if Laravel sent a temporary status message (like "Password reset link sent!") -->
    @if (session('status'))
        <p class="auth-status">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="auth-field">
            <label class="auth-label" for="email">Email address</label>
            <input class="auth-input" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            @if ($errors->has('email'))
                <p class="auth-error">{{ $errors->first('email') }}</p>
            @endif
        </div>

        <!-- Password -->
        <div class="auth-field">
            <label class="auth-label" for="password">Password</label>
            <input class="auth-input" id="password" type="password" name="password" required autocomplete="current-password">
            @if ($errors->has('password'))
                <p class="auth-error">{{ $errors->first('password') }}</p>
            @endif
        </div>

        <!-- Remember Me -->
        <div class="auth-options">
            <label for="remember_me" class="auth-checkbox-label">
                <input id="remember_me" type="checkbox" class="auth-checkbox" name="remember">
                <span>Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a class="auth-link" href="{{ route('password.request') }}">Forgot password?</a>
            @endif
        </div>

        <div class="auth-submit-row">
            <button class="button auth-submit" type="submit">Log in</button>
        </div>
    </form>

    <p class="auth-register-message">New to RescueBite? <a class="auth-link" href="{{ route('register') }}">Create an account</a></p>
@endsection
