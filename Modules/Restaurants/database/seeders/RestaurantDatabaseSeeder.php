<?php

namespace Modules\Restaurant\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // تأكد من مسار موديل المستخدم
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RestaurantOwnerSeeder extends Seeder
{
    public function run(): void
    {
        // 1. إنشاء أو تحديث صاحب المطعم
        $user = User::updateOrCreate(
            ['email' => 'owner@test.com'], // للبحث
            [
                'name' => 'أحمد محمد - صاحب مطعم',
                'password' => Hash::make('password123'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // 2. ربط المستخدم برتبة صاحب مطعم (إذا كنت تستخدم Spatie Roles)
        // إذا كنت لا تستخدمها، يمكنك تعطيل هذا السطر
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('Restaurant Owner');
        }

        // 3. إنشاء أو تحديث بيانات المطعم
        // افترضنا أن جدول المطاعم اسمه restaurants
        DB::table('restaurants')->updateOrCreate(
            ['owner_id' => $user->id], // للبحث عن طريق المالك
            [
                'name' => 'مطعم حضرموت الدولي',
                'location' => 'المكلا - فوه',
                'category' => 'وجبات شعبية',
                'status' => 'open',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info("✅ تم إنشاء حساب المالك (owner@test.com) والمطعم بنجاح!");
    }
}