<?php

namespace Modules\Payments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Payments\Database\Factories\PaymentFactory;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_id',
        'payment_method',
        'total_amount',
        'currency_type',
        'payment_status',
        'payment_date',
    ];

    public function order()
    {
        return $this->belongsTo(\Modules\Orders\Models\Order::class);
    }

    public function proof()
    {
        return $this->hasOne(PaymentProof::class);
    }
}
