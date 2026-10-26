<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VendorOutlet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'outlet_name',
        'outlet_location',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function foodListings(): HasMany
    {
        return $this->hasMany(FoodListing::class);
    }
}
