<?php
namespace Modules\Users\Entities; // تأكد من الـ namespace حسب هيكلتك

use Illuminate\Database\Eloquent\Model;
use App\Models\User; // أو Modules\Users\Entities\User إذا كان موديل المستخدم داخل الموديول
use Illuminate\Database\Eloquent\SoftDeletes; // استيراد الخاصية
class DriverProfile extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id', 'id_number', 'address', 'avatar_url', 
        'vehicle_model', 'vehicle_plate', 'vehicle_vin'
    ];

    // علاقة الموصل بالمستخدم (كل بروفايل يتبع لمستخدم واحد)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}