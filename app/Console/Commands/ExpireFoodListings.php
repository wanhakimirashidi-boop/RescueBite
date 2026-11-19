<?php

namespace App\Console\Commands;

use App\Services\FoodListingExpiryService;
use Illuminate\Console\Command;


// bridge bridge between FoodListingExpiryService.php and console.php to run the scheduled command to 
class ExpireFoodListings extends Command
{
    protected $signature = 'food-listings:expire';

    protected $description = 'Marks food listings and unclaimed reservations past their expiry time as expired';

    // runs FoodListingExpiryService.php when Laravel starts the scheduled command
    public function handle(FoodListingExpiryService $foodListingExpiryService)
    {
        // call expirePastDueRecords() method from FoodListingExpiryService.php to mark expired listings and reservations
        $expiredRecords = $foodListingExpiryService->expirePastDueRecords();

        $this->info($expiredRecords['expiredFoodListingCount'] . ' food listing(s) expired.');
        $this->info($expiredRecords['expiredReservationCount'] . ' reservation(s) expired.');

        return Command::SUCCESS;
    }
}