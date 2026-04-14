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
        'status',
    ];


    
    // public function orders() {
    // return $this->hasMany(Order::class); // تأكد أن جدول الطلبات موجود

//     public function restaurant() {
//     return $this->hasOne(Restaurant::class);
// }

// 1. علاقة البروفايل العام (لكل المستخدمين)
    // public function profile() {
    //     return $this->hasOne(Profile::class);
    // }

    // 2. علاقة بيانات السائق (تظهر فقط إذا كان المستخدم موصل)
    public function driverProfile() {
        return $this->hasOne(\Modules\Auth\Models\DriverProfile::class, 'user_id');
    }
    
    public function availability() {
        return $this->hasOne(\App\Models\DriverAvailability::class, 'driver_id');
    }

    // 3. علاقة المطعم (تظهر فقط إذا كان المستخدم صاحب مطعم)
    // public function restaurant() {
    //     return $this->hasOne(Restaurant::class, 'user_id');
    // }

public function owner()
{
    return $this->belongsTo(User::class, 'user_id');
}
public function profile()
{
    return $this->hasOne(Profile::class);
}

public function restaurant()
{
    return $this->hasOne(Restaurant::class, 'owner_id');
}

// دالة حذف بيانات المرتبطة
protected static function booted()
{
    static::deleting(function ($user) {
        // حذف الأدوار والصلاحيات المرتبطة قبل حذف المستخدم
        $user->roles()->detach();
        $user->permissions()->detach();
        
        // وإذا أردت حذف البروفايل أيضاً:
        if($user->driverProfile) {
            $user->driverProfile()->delete();
        }
    });
}

}






// class User extends Authenticatable
// {
//    use HasApiTokens, HasFactory, Notifiable, HasRoles;

//     /**
//      * The attributes that are mass assignable.
//      *
//      * @var list<string>
//      */
//     protected $fillable = [
//         'name',
//         'email',
//         'password',
//     ];

//     protected $guard_name = 'web';
//     /**
//      * The attributes that should be hidden for serialization.
//      *
//      * @var list<string>
//      */
//     protected $hidden = [
//         'password',
//         'remember_token',
//     ];

//     /**
//      * Get the attributes that should be cast.
//      *
//      * @return array<string, string>
//      */
//     protected function casts(): array
//     {
//         return [
//             'email_verified_at' => 'datetime',
//             'password' => 'hashed',
//         ];
//     }

//     public function roles()
//     {
//         // تأكد أنك كتبت مسار موديل الـ Role الصحيح هنا
//         return $this->belongsToMany(Role::class, 'user_roles');
        
//     }
    
//     /**
//      * الدالة التي سألت عنها: للتحقق من امتلاك دور معين
//      */
//     // public function hasRole($roleName)
//     // {
//     //     return $this->roles()->where('name', $roleName)->exists();
//     // }

// }
