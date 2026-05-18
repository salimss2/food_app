<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Settlement extends Model
{
    use HasFactory;

    protected $fillable = [
        'settlement_number',
        'driver_id',
        'admin_id',
        'total_driver_earnings',
        'total_cash_collected',
        'net_settlement_amount',
    ];

    /**
     * العلاقة: التسوية تنتمي لسائق واحد.
     */
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * العلاقة: التسوية تنتمي لمسؤول واحد (آدمن).
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * العلاقة: التسوية لها العديد من الطلبات.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
