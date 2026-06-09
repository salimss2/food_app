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
        'discount_percentage',
        'restaurant_id',
        'expiry_date',
        'status',
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    public function restaurant()
    {
        return $this->belongsTo(\App\Models\Restaurant::class, 'restaurant_id');
    }
}
