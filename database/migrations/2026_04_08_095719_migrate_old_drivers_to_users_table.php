<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Modules\Auth\Models\DriverProfile;
use Spatie\Permission\Models\Role;

return new class extends Migration {
    public function up()
    {
        // 1. تأكد من وجود دور "Driver" أولاً
        $driverRole = Role::firstOrCreate(['name' => 'Driver']);

        // 2. جلب كل البيانات من جدول drivers القديم
        $oldDrivers = DB::table('drivers')->get();

        foreach ($oldDrivers as $oldDriver) {
            // التحقق مما إذا كان المستخدم موجوداً مسبقاً (لتجنب التكرار)
            $existingUser = User::where('phone', $oldDriver->phone)
                                ->orWhere('email', $oldDriver->email)
                                ->first();

            if (!$existingUser) {
                // 3. إنشاء المستخدم في الجدول الموحد
                $user = User::create([
                    'name'     => $oldDriver->name,
                    'phone'    => $oldDriver->phone,
                    'email'    => $oldDriver->email,
                    'password' => $oldDriver->password, // ننقل الهاش كما هو
                    'status'   => 'active',
                    'created_at' => $oldDriver->created_at,
                    'updated_at' => $oldDriver->updated_at,
                ]);

                // 4. إسناد الدور (Spatie)
                $user->assignRole($driverRole);

                // 5. إنشاء سجل في جدول driver_profiles الجديد
                DriverProfile::create([
                    'user_id'       => $user->id,
                    'id_number'     => $oldDriver->id_number,
                    'address'       => $oldDriver->address,
                    'avatar_url'    => $oldDriver->avatar_url,
                    'vehicle_model' => $oldDriver->vehicle_model,
                    'vehicle_plate' => $oldDriver->vehicle_plate,
                    'vehicle_vin'   => $oldDriver->vehicle_vin,
                    'created_at'    => $oldDriver->created_at,
                    'updated_at'    => $oldDriver->updated_at,
                ]);
            }
        }
    }

    public function down()
    {
        // اختياري: ماذا يحدث لو أردت التراجع؟ 
        // عادة في نقل البيانات لا نكتب شيئاً هنا أو نمسح ما تم نقله بحذر
    }
};