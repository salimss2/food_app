<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'discount',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'expires_at' => 'date',
        'status' => 'boolean',
        'discount' => 'float',
    ];

    /**
     * Scope to only include active and non-expired coupons.
     */
    public function scopeValid($query)
    {
        return $query->where('status', true)
            ->where('expires_at', '>=', now()->startOfDay());
    }
}
