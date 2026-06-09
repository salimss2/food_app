<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Modules\Restaurants\Models\Restaurant;

class RestaurantSettlement extends Model
{
    use HasFactory;

    protected $fillable = [
        'settlement_number',
        'restaurant_id',
        'admin_id',
        'gross_revenue',
        'system_cut',
        'net_payable',
    ];

    /**
     * Relationship: A settlement belongs to one restaurant.
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    /**
     * Relationship: A settlement belongs to one admin user.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Relationship: A settlement contains many orders.
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'restaurant_settlement_id');
    }
}
