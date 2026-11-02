@extends('layouts.vendor')

@section('title', 'Verify Code | RescueBite Vendor Panel')

@section('content')
    <section class="vendor-hero">
        <div class="container">
            <p class="eyebrow">Vendor workspace</p>
            <h1>Verify <span>Code</span></h1>
            <p>Enter the consumer pickup code to complete a food rescue.</p>
        </div>
    </section>

    <section class="vendor-content">
        <div class="container">
            <div class="vendor-form-heading">
                <div>
                    <p class="eyebrow">{{ $vendorOutlet->outlet_name }}</p>
                    <h2>Consumer verification</h2>
                </div>
                <a class="vendor-back-link" href="{{ route('vendor.dashboard') }}">Back to inventory</a>
            </div>

            @if (session('success'))
                <p class="vendor-success-message">{{ session('success') }}</p>
            @endif

            @if (session('error'))
                <p class="vendor-error-message">{{ session('error') }}</p>
            @endif

            <form class="vendor-form vendor-verify-form" method="POST" action="{{ route('vendor.verify-code.store') }}">
                @csrf

                <div class="vendor-field">
                    <label for="verify_code">Consumer verification code</label>
                    <input id="verify_code" type="text" name="verify_code" value="{{ old('verify_code') }}" placeholder="Example: RB-DEMO01" required>
                    @if ($errors->has('verify_code'))
                        <p class="vendor-field-error">{{ $errors->first('verify_code') }}</p>
                    @endif
                </div>

                <button class="vendor-submit-button" type="submit">Verify and claim</button>
            </form>

            <p class="vendor-note">For testing, use the seeded code <strong>RB-DEMO01</strong>.</p>
        </div>
    </section>
@endsection
