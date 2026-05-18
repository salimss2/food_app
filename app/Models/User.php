<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
// use Spatie\Permission\Models\Role;
// use App\Traits\HasRoles; // استدعاء Trait
use Spatie\Permission\Traits\HasRoles;
use Modules\Users\Entities\DriverProfile;
use Illuminate\Database\Eloquent\SoftDeletes; // استيراد الخاصية
use Modules\Users\Models\Profile;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    use SoftDeletes;

    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'profile_picture',
        'status',
        'fcm_token',
        'otp_code',
        'otp_expires_at',
    ];

    protected $appends = ['image_url', 'profile_picture_full_url'];

    public function getImageUrlAttribute()
    {
        return $this->image_path ? \Illuminate\Support\Facades\Storage::disk('s3')->url($this->image_path) : null;
    }

    protected function profilePictureFullUrl(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->profile_picture ? \Illuminate\Support\Facades\Storage::disk('s3')->url($this->profile_picture) : null,
        );
    }



    public function driverOrders()
    {
        return $this->hasMany(\Modules\Orders\Models\Order::class, 'driver_id');
    }

    public function orders()
    {
        return $this->hasMany(\Modules\Orders\Models\Order::class, 'user_id');
    }

    //     public function restaurant() {
//     return $this->hasOne(Restaurant::class);
// }

    // 1. علاقة البروفايل العام (لكل المستخدمين)
    // public function profile() {
    //     return $this->hasOne(Profile::class);
    // }

    // 2. علاقة بيانات السائق (تظهر فقط إذا كان المستخدم موصل)
    public function driverProfile()
    {
        return $this->hasOne(\Modules\Auth\Models\DriverProfile::class, 'user_id');
    }

    public function availability()
    {
        return $this->hasOne(\App\Models\DriverAvailability::class, 'driver_id');
    }

    // 3. علاقة المطعم (تظهر فقط إذا كان المستخدم صاحب مطعم)
    public function restaurant()
    {
        return $this->hasOne(\Modules\Restaurants\Models\Restaurant::class, 'owner_id');
    }

    public function favorites()
    {
        return $this->hasMany(\Modules\Users\Models\Favorite::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    // دالة حذف بيانات المرتبطة
    protected static function booted()
    {
        static::deleting(function ($user) {
            // حذف الأدوار والصلاحيات المرتبطة قبل حذف المستخدم
            $user->roles()->detach();
            $user->permissions()->detach();

            // وإذا أردت حذف البروفايل أيضاً:
            if ($user->driverProfile) {
                $user->driverProfile()->delete();
            }
        });
    }

}







