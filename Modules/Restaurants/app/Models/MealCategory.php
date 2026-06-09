<?php

namespace Modules\Restaurants\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MealCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'restaurant_id',
        'name',
        'image',
    ];

    protected $appends = ['image_url', 'image_full_url'];

    public function getImageAttribute($value)
    {
        if (!$value) {
            return asset('assets/default-category.png');
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

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }


    public function meals()
    {
        // تأكد من عمل import لمودل Meal في أعلى الملف إذا لزم الأمر
        return $this->hasMany(Meal::class, 'meal_category_id');
    }

}
