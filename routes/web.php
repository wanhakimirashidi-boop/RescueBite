<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Welcome page (for guest)
Route::get('/', function () {
    return view('welcome');
});

// START OF LARAVEL BREEZE
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php'; // routes\auth.php - shortcut to include all the authentication routes (login, register, logout, etc.) without writing all
// END OF LARAVEL BREEZE