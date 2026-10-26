<?php

namespace Database\Seeders;

use App\Models\FoodListing;
use App\Models\User;
use App\Models\VendorOutlet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $consumer = User::updateOrCreate([
            'email' => 'consumer@example.com',
        ], [
            'name' => 'Consumer',
            'role' => 'consumer',
            'password' => Hash::make('password'),
        ]);

        $vendor = User::updateOrCreate([
            'email' => 'vendor@example.com',
        ], [
            'name' => 'Vendor',
            'role' => 'vendor',
            'password' => Hash::make('password'),
        ]);

        $admin = User::updateOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $vendorOutlet = VendorOutlet::updateOrCreate([
            'user_id' => $vendor->id,
        ], [
            'outlet_name' => 'Ros Cinta,',
            'outlet_location' => 'UPTEN',
        ]);

        FoodListing::updateOrCreate([
            'vendor_outlet_id' => $vendorOutlet->id,
            'item_name' => 'Chicken Wrap Set',
        ], [
            'category' => 'Food',
            'quantity' => 2,
            'discounted_price' => 6.50,
            'original_price' => 12.00,
            'expiry_time' => now()->addHours(3),
            'image_url' => 'https://images.unsplash.com/photo-1528735602780-2552fd46c7af?auto=format&fit=crop&w=700&q=80',
            'status' => 'active',
        ]);

        FoodListing::updateOrCreate([
            'vendor_outlet_id' => $vendorOutlet->id,
            'item_name' => 'Pastry Box',
        ], [
            'category' => 'Pastries',
            'quantity' => 5,
            'discounted_price' => 5.00,
            'original_price' => 10.00,
            'expiry_time' => now()->addHours(4),
            'image_url' => 'https://images.unsplash.com/photo-1515003197210-e0cd71810b5f?auto=format&fit=crop&w=700&q=80',
            'status' => 'active',
        ]);
    }
}
