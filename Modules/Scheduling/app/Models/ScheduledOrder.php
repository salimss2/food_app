<?php

namespace Modules\Scheduling\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class ScheduledOrder extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'scheduled_orders';

    /**
     * Mass-assignable attributes.
     */
    protected $fillable = [
        'user_id',
        'restaurant_id',
        'order_number',
        'items_count',
        'total_amount',
        'items_content',
        'scheduled_at',
        'status',
    ];

    /**
     * Attribute casts.
     */
    protected $casts = [
        'scheduled_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'items_count'  => 'integer',
        'items_content' => 'array',
    ];

    /**
     * The customer who placed the scheduled order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The restaurant associated with the scheduled order.
     */
    public function restaurant()
    {
        return $this->belongsTo(\Modules\Restaurants\Models\Restaurant::class, 'restaurant_id');
    }
}
