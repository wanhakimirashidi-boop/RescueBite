<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FoodListing extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_outlet_id',
        'item_name',
        'category',
        'quantity',
        'discounted_price',
        'original_price',
        'expiry_time',
        'image_path',
        'status',
    ];

    protected $casts = [
        'discounted_price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'expiry_time' => 'datetime',
    ];

    public function vendorOutlet(): BelongsTo
    {
        return $this->belongsTo(VendorOutlet::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

}
