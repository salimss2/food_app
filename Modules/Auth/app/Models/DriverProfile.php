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
        'rating',
        'rating_count',
    ];

    protected $appends = ['avatar_full_url'];

    protected function avatarFullUrl(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function () {
                if (!$this->avatar_url) {
                    return null;
                }
                if (str_starts_with($this->avatar_url, 'http://') || str_starts_with($this->avatar_url, 'https://')) {
                    $storageBase = asset('storage/');
                    if (str_starts_with($this->avatar_url, $storageBase)) {
                        $path = str_replace($storageBase, '', $this->avatar_url);
                        $path = ltrim($path, '/');
                        return \Illuminate\Support\Facades\Storage::url($path);
                    }
                    return $this->avatar_url;
                }
                return \Illuminate\Support\Facades\Storage::url($this->avatar_url);
            }
        );
    }

    // علاقة عكسية: هذا البروفايل يخص مستخدماً واحداً
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}