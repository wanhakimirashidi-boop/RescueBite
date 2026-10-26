{{-- Create Food Listing page --}}

@extends('layouts.vendor')

@section('title', 'Add Surplus | RescueBite Vendor Panel')

@section('content')
    <section class="vendor-hero">
        <div class="container">
            <p class="eyebrow">Vendor workspace</p>
            <h1>Add <span>Surplus</span></h1>
            <p>List food that is available for rescue before your outlet closes.</p>
        </div>
    </section>

    <section class="vendor-content">
        <div class="container">
            <div class="vendor-form-heading">
                <div>
                    <p class="eyebrow">New food listing</p>
                    <h2>Food details</h2>
                </div>
                <a class="vendor-back-link" href="{{ route('vendor.dashboard') }}">Back to inventory</a>
            </div>

            <form class="vendor-form" method="POST" action="{{ route('vendor.food-listings.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="vendor-form-grid">
                    <div class="vendor-field">
                        <label for="item_name">Item name</label>
                        <input id="item_name" type="text" name="item_name" value="{{ old('item_name') }}" required>
                        @if ($errors->has('item_name'))
                            <p class="vendor-field-error">{{ $errors->first('item_name') }}</p>
                        @endif
                    </div>

                    <div class="vendor-field">
                        <label for="category">Category</label>
                        <select id="category" name="category" required>
                            <option value="">Select category</option>
                            <option value="Food">Food</option>
                            <option value="Pastries">Pastries</option>
                            <option value="Snacks">Snacks</option>
                            <option value="Drinks">Drinks</option>
                        </select>
                        @if ($errors->has('category'))
                            <p class="vendor-field-error">{{ $errors->first('category') }}</p>
                        @endif
                    </div>

                    <div class="vendor-field">
                        <label for="quantity">Quantity</label>
                        <input id="quantity" type="number" name="quantity" min="1" value="{{ old('quantity') }}" required>
                        @if ($errors->has('quantity'))
                            <p class="vendor-field-error">{{ $errors->first('quantity') }}</p>
                        @endif
                    </div>

                    <div class="vendor-field">
                        <label for="expiry_time">Expiry time</label>
                        <input id="expiry_time" type="datetime-local" name="expiry_time" value="{{ old('expiry_time') }}" required>
                        @if ($errors->has('expiry_time'))
                            <p class="vendor-field-error">{{ $errors->first('expiry_time') }}</p>
                        @endif
                    </div>

                    <div class="vendor-field">
                        <label for="discounted_price">Discounted price (RM)</label>
                        <input id="discounted_price" type="number" name="discounted_price" min="0" step="0.01" value="{{ old('discounted_price') }}" required>
                        @if ($errors->has('discounted_price'))
                            <p class="vendor-field-error">{{ $errors->first('discounted_price') }}</p>
                        @endif
                    </div>

                    <div class="vendor-field">
                        <label for="original_price">Original price (RM)</label>
                        <input id="original_price" type="number" name="original_price" min="0" step="0.01" value="{{ old('original_price') }}" required>
                        @if ($errors->has('original_price'))
                            <p class="vendor-field-error">{{ $errors->first('original_price') }}</p>
                        @endif
                    </div>
                </div>

                <div class="vendor-field vendor-image-field">
                    <label for="image">Food image</label>
                    <input class="vendor-image-input" id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp" required>
                    @if ($errors->has('image'))
                        <p class="vendor-field-error">{{ $errors->first('image') }}</p>
                    @endif
                    <img class="vendor-image-preview" src="" alt="Selected food image preview" hidden>
                </div>

                <button class="vendor-submit-button" type="submit">Add food listing</button>
            </form>

            @include('vendor.partials.image-preview-script')
        </div>
    </section>
@endsection
