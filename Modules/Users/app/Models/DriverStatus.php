<?php

namespace Modules\Users\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User; // استيراد موديل المستخدم لضمان عمل العلاقة

class DriverStatus extends Model
{
    use HasFactory;
// تحديد اسم الجدول يدوياً للتأكد
    protected $table = 'driver_availability';

    // السماح بالحقول الجديدة
    protected $fillable = [
        'driver_id', 
        'is_online', 
        'availability',
        'status', // إذا كنت لا تزال تستخدمه
        'last_updated'
    ];

    // علاقة مع المستخدم
    public function driver()
    {
        return $this->belongsTo(\App\Models\User::class, 'driver_id');
    }
}