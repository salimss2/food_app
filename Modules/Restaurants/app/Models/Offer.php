<?php

namespace Modules\Restaurants\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Offer extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'combo_price' => 'float',
    ];

    protected $appends = ['image_url'];

    public function getImageAttribute($value)
    {
        if (!$value) {
            return asset('assets/default-offer.png');
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

    public function meals(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Meal::class, 'meal_offer')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function restaurant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}
