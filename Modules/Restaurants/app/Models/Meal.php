<?php

namespace Modules\Restaurants\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// use Modules\Restaurants\Database\Factories\MealFactory;

class Meal extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'restaurant_id',
        'menu_id',
        'meal_category_id',
        'name',
        'price',
        'discount_price',
        'description',
        'image',
        'available',
        'discount_type',
        'discount_value',
        'discount_start',
        'discount_end',
    ];

    protected $casts = [
        'available' => 'boolean',
        'discount_start' => 'datetime',
        'discount_end' => 'datetime',
    ];

    protected $appends = ['image_url', 'price_after_discount', 'image_full_url'];

    public function getImageAttribute($value)
    {
        if (!$value) {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        return \Illuminate\Support\Facades\Storage::url($value);
    }

    public function getImageUrlAttribute()
    {
        return $this->image;
    }

    protected function imageFullUrl(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn() => $this->image,
        );
    }

    public function getPriceAfterDiscountAttribute()
    {
        $now = now();
        $hasActiveDiscount = $this->discount_type &&
            $this->discount_value !== null &&
            ($this->discount_start === null || $now >= $this->discount_start) &&
            ($this->discount_end === null || $now <= $this->discount_end);

        if ($hasActiveDiscount) {
            if ($this->discount_type === 'percentage') {
                $discountAmount = ($this->price * $this->discount_value) / 100;
                return max(0, (float) ($this->price - $discountAmount));
            } elseif ($this->discount_type === 'fixed') {
                return max(0, (float) ($this->price - $this->discount_value));
            }
        }

        return (float) $this->price;
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MealCategory::class, 'meal_category_id');
    }

    public function offers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Offer::class, 'meal_offer')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
