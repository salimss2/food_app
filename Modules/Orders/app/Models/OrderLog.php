<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;

class OrderLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'status',
        'description',
    ];

    /**
     * Get the order that owns the log.
     */
    public function order()
    {
        return $this->belongsTo(\Modules\Orders\Models\Order::class);
    }
}
