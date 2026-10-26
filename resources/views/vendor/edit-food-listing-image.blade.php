{{-- Edit uploaded Food Image --}}

@extends('layouts.vendor')

@section('title', 'Edit Food Image | RescueBite Vendor Panel')

@section('content')
    <section class="vendor-hero">
        <div class="container">
            <p class="eyebrow">Vendor workspace</p>
            <h1>Edit <span>Image</span></h1>
            <p>Replace the photo for {{ $foodListing->item_name }}.</p>
        </div>
    </section>

    <section class="vendor-content">
        <div class="container">
            <div class="vendor-form-heading">
                <div>
                    <p class="eyebrow">{{ $foodListing->item_name }}</p>
                    <h2>Replace food image</h2>
                </div>
                <a class="vendor-back-link" href="{{ route('vendor.dashboard') }}">Back to inventory</a>
            </div>

            <form class="vendor-form" method="POST" action="{{ route('vendor.food-listings.image.update', $foodListing) }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                @if ($foodListing->image_path)
                    <img class="vendor-current-image" src="{{ asset('storage/' . $foodListing->image_path) }}" alt="Current image for {{ $foodListing->item_name }}">
                @endif

                <div class="vendor-field vendor-image-field">
                    <label for="image">New food image</label>
                    <input class="vendor-image-input" id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp" required>
                    @if ($errors->has('image'))
                        <p class="vendor-field-error">{{ $errors->first('image') }}</p>
                    @endif
                    <img class="vendor-image-preview" src="" alt="Selected food image preview" hidden>
                </div>

                <button class="vendor-submit-button" type="submit">Update image</button>
            </form>

            @include('vendor.partials.image-preview-script')
        </div>
    </section>
@endsection
