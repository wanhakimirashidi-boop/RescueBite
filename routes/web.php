<?php

use App\Http\Controllers\ConsumerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

// Guest routes - Home page
Route::get('/', [HomeController::class, 'index']);



// LARAVEL BREEZE 
// Consumer routes - Dashboard section
Route::get('/dashboard', [ConsumerController::class, 'dashboard'])
    ->middleware(['auth', 'verified', 'role:consumer'])
    ->name('dashboard');

// Consumer routes - My Rescues section
Route::get('/my-rescues', [ConsumerController::class, 'rescues'])
    ->middleware(['auth', 'verified', 'role:consumer'])
    ->name('consumer.rescues');

// Consumer routes - My Rescues History section
Route::get('/my-rescues/history', [ConsumerController::class, 'rescueHistory'])
    ->middleware(['auth', 'verified', 'role:consumer'])
    ->name('consumer.rescues.history');


    
// Vendor routes - Dashboard section
Route::get('/vendor/dashboard', [VendorController::class, 'dashboard'])
    ->middleware(['auth', 'verified', 'role:vendor'])
    ->name('vendor.dashboard');

Route::get('/vendor/surplus/create', [VendorController::class, 'createFoodListing'])
    ->middleware(['auth', 'verified', 'role:vendor'])
    ->name('vendor.food-listings.create');

Route::post('/vendor/surplus', [VendorController::class, 'storeFoodListing'])
    ->middleware(['auth', 'verified', 'role:vendor'])
    ->name('vendor.food-listings.store');

Route::get('/vendor/food-listings/{foodListing}/image/edit', [VendorController::class, 'editFoodListingImage'])
    ->middleware(['auth', 'verified', 'role:vendor'])
    ->name('vendor.food-listings.image.edit');

Route::patch('/vendor/food-listings/{foodListing}/image', [VendorController::class, 'updateFoodListingImage'])
    ->middleware(['auth', 'verified', 'role:vendor'])
    ->name('vendor.food-listings.image.update');

Route::delete('/vendor/food-listings/{foodListing}', [VendorController::class, 'destroyFoodListing'])
    ->middleware(['auth', 'verified', 'role:vendor'])
    ->name('vendor.food-listings.destroy');



// LARAVEL BREEZE - Profile routes (for user account management)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// LARAVEL BREEZE - Authentication routes (login, register, etc.)
require __DIR__.'/auth.php'; 
// routes\auth.php - shortcut to include all the authentication routes (login, register, logout, etc.) without writing all
