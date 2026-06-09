<?php

namespace Modules\Restaurants\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealVariant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'meal_id',
        'name',
        'price',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'float',
    ];

    /**
     * Relation: A variant belongs to a single meal.
     */
    public function meal(): BelongsTo
    {
        return $this->belongsTo(Meal::class);
    }
}
