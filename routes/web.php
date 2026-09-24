<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Welcome page (for guest)
Route::get('/', function () {
    return view('welcome');
});

// LARAVEL BREEZE - Dashboard route (for authenticated users)
Route::get('/dashboard', function () {
    return view('consumer.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Consumer routes - My Rescues section
Route::get('/my-rescues', function () {
    return view('consumer.rescues');
})->middleware(['auth', 'verified'])->name('consumer.rescues');

// Consumer routes - My Rescues History section
Route::get('/my-rescues/history', function () {
    return view('consumer.rescues-history');
})->middleware(['auth', 'verified'])->name('consumer.rescues.history');

// LARAVEL BREEZE - Profile routes (for user account management)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// LARAVEL BREEZE - Authentication routes (login, register, etc.)
require __DIR__.'/auth.php'; 
// routes\auth.php - shortcut to include all the authentication routes (login, register, logout, etc.) without writing all