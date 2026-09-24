{{-- this file is placeholder contents for the MAIN auth.blade --}}
@extends('layouts.auth')

{{-- @yield('title', 'RescueBite') in auth.blade = "define page title there. If a page doesn't have one, use 'RescueBite' as the default --}}
{{-- @section('title', 'Log in | RescueBite') in login.blade = "Pass this exact text to auth.blade to fill the 'title' placeholder --}}
@section('title', 'Create an account | RescueBite')

{{-- @yield on auth.blade = leave blank space right there && "@section('content')" paste it there--}}
@section('content')
    <h1 class="auth-heading">Join RescueBite</h1>
    <p class="auth-introduction">Create an account to reserve surplus food and make every meal count.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="auth-field">
            <label class="auth-label" for="name">Full name</label>
            <input class="auth-input" id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
            @if ($errors->has('name'))
                <p class="auth-error">{{ $errors->first('name') }}</p>
            @endif
        </div>

        <!-- Email Address -->
        <div class="auth-field">
            <label class="auth-label" for="email">Email address</label>
            <input class="auth-input" id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
            @if ($errors->has('email'))
                <p class="auth-error">{{ $errors->first('email') }}</p>
            @endif
        </div>

        <!-- Password -->
        <div class="auth-field">
            <label class="auth-label" for="password">Password</label>
            <input class="auth-input" id="password" type="password" name="password" required autocomplete="new-password">
            @if ($errors->has('password'))
                <p class="auth-error">{{ $errors->first('password') }}</p>
            @endif
        </div>

        <!-- Confirm Password -->
        <div class="auth-field">
            <label class="auth-label" for="password_confirmation">Confirm password</label>
            <input class="auth-input" id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
            @if ($errors->has('password_confirmation'))
                <p class="auth-error">{{ $errors->first('password_confirmation') }}</p>
            @endif
        </div>

        <div class="auth-submit-row">
            <button class="button auth-submit" type="submit">Create account</button>
        </div>
    </form>

    <p class="auth-register-message">Already have an account? <a class="auth-link" href="{{ route('login') }}">Log in</a></p>
@endsection
