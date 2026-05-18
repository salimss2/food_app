<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Modules\Restaurants\Models\MealCategory; // Add this import

class Restaurant extends Model
{
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image_path ? \Illuminate\Support\Facades\Storage::disk('s3')->url($this->image_path) : null;
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