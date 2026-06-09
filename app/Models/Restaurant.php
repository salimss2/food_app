<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Modules\Restaurants\Models\MealCategory; // Add this import

class Restaurant extends Model
{
    protected $appends = ['image_url', 'logo_full_url'];

    public function getImageUrlAttribute()
    {
        return $this->image_path ? \Illuminate\Support\Facades\Storage::disk('s3')->url($this->image_path) : null;
    }

    protected function logoFullUrl(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn() => $this->logo ? \Illuminate\Support\Facades\Storage::disk('s3')->url($this->logo) : null,
        );
    }

    protected $fillable = [
        'name',
        'logo',
        'description',
        'address',
        'phone',
        'status',
        'rating'
    ];
    public function meal_categories()
    {
        return $this->hasMany(MealCategory::class);
    }
}