<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminOffer extends Model
{
    use HasFactory;

    protected $table = 'admin_offers';

    protected $fillable = [
        'title',
        'description',
        'type',
        'click_action',
        'banner_image',
        'discount_percentage',
        'original_price',
        'offer_price',
        'restaurant_id',
        'meal_id',
        'expiry_date',
        'start_date',
        'status',
        'coupon_code',
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'original_price' => 'decimal:2',
        'offer_price' => 'decimal:2',
        'expiry_date' => 'date',
        'start_date' => 'date',
    ];

    protected $appends = ['image_url'];

    public function restaurant()
    {
        return $this->belongsTo(\App\Models\Restaurant::class, 'restaurant_id');
    }

    public function meal()
    {
        return $this->belongsTo(\Modules\Restaurants\Models\Meal::class, 'meal_id');
    }

    public function getImageUrlAttribute()
    {
        if (!$this->banner_image) {
            return asset('assets/default-offer.png');
        }
        if (str_starts_with($this->banner_image, 'http://') || str_starts_with($this->banner_image, 'https://')) {
            return $this->banner_image;
        }
        return \Illuminate\Support\Facades\Storage::url($this->banner_image);
    }
}
