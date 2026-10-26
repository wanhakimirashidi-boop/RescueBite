<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_outlet_id')->constrained()->cascadeOnDelete();
            $table->string('item_name');
            $table->string('category');
            $table->unsignedInteger('quantity');
            $table->decimal('discounted_price', 8, 2);
            $table->decimal('original_price', 8, 2);
            $table->dateTime('expiry_time');
            $table->string('image_url')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_listings');
    }
};
