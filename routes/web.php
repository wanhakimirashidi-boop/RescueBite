<?php

use App\Http\Controllers\AdminController;
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

// Consumer routes - Food Listings section
Route::get('/food-listings/{foodListing}/reserve', [ConsumerController::class, 'showReservationConfirmation'])
    ->middleware(['auth', 'verified', 'role:consumer'])
    ->name('consumer.reservations.confirm');
// Consumer routes - Store reservation
Route::post('/food-listings/{foodListing}/reserve', [ConsumerController::class, 'storeReservation'])
    ->middleware(['auth', 'verified', 'role:consumer'])
    ->name('consumer.reservations.store');
// POST = make new things

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

// Vendor routes - Food Listings section
Route::get('/vendor/surplus/create', [VendorController::class, 'createFoodListing'])
    ->middleware(['auth', 'verified', 'role:vendor'])
    ->name('vendor.food-listings.create');
// Vendor routes - Store food listing
Route::post('/vendor/surplus', [VendorController::class, 'storeFoodListing'])
    ->middleware(['auth', 'verified', 'role:vendor'])
    ->name('vendor.food-listings.store');

// Vendor routes - Edit food listing
Route::get('/vendor/food-listings/{foodListing}/image/edit', [VendorController::class, 'editFoodListingImage'])
    ->middleware(['auth', 'verified', 'role:vendor'])
    ->name('vendor.food-listings.image.edit');
// Vendor routes - Update food listing image
Route::patch('/vendor/food-listings/{foodListing}/image', [VendorController::class, 'updateFoodListingImage'])      // PATCH = update existing things
    ->middleware(['auth', 'verified', 'role:vendor'])
    ->name('vendor.food-listings.image.update');
// Vendor routes - Delete food listing
Route::delete('/vendor/food-listings/{foodListing}', [VendorController::class, 'destroyFoodListing'])
    ->middleware(['auth', 'verified', 'role:vendor'])
    ->name('vendor.food-listings.destroy');

// Vendor routes - Verify code page
Route::get('/vendor/verify-code', [VendorController::class, 'showVerifyCode'])
    ->middleware(['auth', 'verified', 'role:vendor'])
    ->name('vendor.verify-code.create');

// Vendor routes - Verify code submission
Route::post('/vendor/verify-code', [VendorController::class, 'verifyCode'])
    ->middleware(['auth', 'verified', 'role:vendor'])
    ->name('vendor.verify-code.store');



// Admin routes - Dashboard (TOP CARDS) and analytics section (BOTTOM CARDS)
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('admin.dashboard');

// Admin routes - Users management section
Route::get('/admin/users', [AdminController::class, 'users'])
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('admin.users');

// Admin routes - Block or unblock users
Route::patch('/admin/users/{user}/toggle-block', [AdminController::class, 'toggleUserBlock'])   // PATCH = update existing things
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('admin.users.toggle-block');

// Admin routes - Grant vendor access to consumer users
Route::post('/admin/users/grant-vendor', [AdminController::class, 'grantVendorAccess'])     // POST = make new things
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('admin.users.grant-vendor');



// LARAVEL BREEZE - Profile routes (for user account management)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// LARAVEL BREEZE - Authentication routes (login, register, etc.)
require __DIR__.'/auth.php'; 
// routes\auth.php - shortcut to include all the authentication routes (login, register, logout, etc.) without writing all
