<?php

namespace Modules\Users\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User; // أو مسار مودل اليوزر في الموديول لديك

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'avatar',
        'address',
        'location',
        'preferences',
    ];

    /**
     * تحويل حقل الـ preferences من JSON إلى مصفوفة تلقائياً
     */
    protected $casts = [
        'preferences' => 'array',
    ];

    /**
     * علاقة البروفايل بالمستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}