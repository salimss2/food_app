<?php

namespace Modules\Restaurants\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealOption extends Model
{
    use HasFactory;

    protected $table = 'meal_options';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'meal_id',
        'option_name',
        'additional_price',
        'name',
        'price',
    ];

    protected $appends = ['name', 'price'];

    protected $casts = [
        'additional_price' => 'float',
    ];

    /**
     * Accessor for name attribute (alias for option_name).
     */
    public function getNameAttribute()
    {
        return $this->attributes['name'] ?? $this->attributes['option_name'] ?? null;
    }

    /**
     * Accessor for price attribute (alias for additional_price).
     */
    public function getPriceAttribute()
    {
        $val = $this->attributes['price'] ?? $this->attributes['additional_price'] ?? 0;
        return (float) $val;
    }

    /**
     * Relationship: MealOption belongs to a Meal.
     */
    public function meal(): BelongsTo
    {
        return $this->belongsTo(Meal::class, 'meal_id');
    }
}
