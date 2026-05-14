<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model; // تغيير من Authenticatable إلى Model
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User; // أو مسار موديل User عندك

class DriverProfile extends Model
{
    use HasFactory;

    protected $table = 'driver_profiles'; // التأكد من مطابقة اسم الجدول الجديد

    protected $fillable = [
        'user_id',      // أهم حقل للربط مع جدول users
        'address',
        'id_number',
        'avatar_url',     // 🔥 تم إضافة الحقل هنا للسماح بحفظ رابط الصورة
        'user_id',
        'latitude',
        'longitude',
        'vehicle_model',
        'vehicle_plate',
        'vehicle_vin',
    ];

    // علاقة عكسية: هذا البروفايل يخص مستخدماً واحداً
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}