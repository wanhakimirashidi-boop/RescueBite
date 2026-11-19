<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Runs the expiry command EVERY MINUTE so unavailable food is marked as expired
Schedule::command('food-listings:expire')->everyMinute();
// food-listings:expire is the command that runs the ExpireFoodListings.php, 
// which calls the FoodListingExpiryService.php to mark expired listings and reservations