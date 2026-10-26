<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consumer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('food_listing_id')->constrained()->cascadeOnDelete();
            $table->string('verify_code')->unique();
            $table->string('status')->default('reserved');
            $table->timestamp('claimed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
