<?php

namespace Modules\Delivery\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class DriverStatus extends Model
{
    use HasFactory;

    /**
     * اسم الجدول المرتبط بهذا الموديل (تم تغييره ليتوافق مع الـ Migration).
     */
    protected $table = 'driver_availability';

    /**
     * الحقول القابلة للتعبئة.
     * تم تغيير user_id إلى driver_id ليطابق قاعدة البيانات.
     * وأضفنا الحقول الجديدة (status و availability).
     */
    protected $fillable = [
        'driver_id',
        'is_online',
        'status',        // من الـ migration الأساسي
        'availability',  // من الـ migration الخاص بالتعديل
        'last_updated'
    ];

    /**
     * تحويل الحقول إلى أنواع بيانات محددة.
     */
    protected $casts = [
        'is_online' => 'boolean',
        'last_updated' => 'datetime',
    ];

    /**
     * علاقة "ينتمي إلى": الحالة تنتمي لمستخدم (سائق).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'driver_id'); // تأكدنا من تحديد الـ Foreign Key
    }
}