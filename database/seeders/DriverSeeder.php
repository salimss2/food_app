<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        // بناءً على هيكلة الموديولات، جرب هذا المسار الدقيق:
        $driverClass = "\Modules\Auth\Entities\Driver"; 

        // إذا لم ينجح، سنجرب المسار الذي ظهر في الخطأ مع التأكد من وجوده:
        if (!class_exists($driverClass)) {
            $driverClass = "\Modules\Auth\App\Models\Driver";
        }

        // إذا استمرت المشكلة، سنستخدم الـ DB Query Builder كحل أخير (يعمل 100%)
        if (!class_exists($driverClass)) {
            \Illuminate\Support\Facades\DB::table('drivers')->updateOrInsert(
                ['phone' => '777123456'],
                [
                    'name'          => 'صالح الموصل',
                    'email'         => 'saleh@delivery.com',
                    'password'      => Hash::make('123456'), 
                    'id_number'     => '1234567890',
                    'address'       => 'المكلا، فوة',
                    'vehicle_model' => 'Toyota Hilux',
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]
            );
            $this->command->info("تم الإدخال عبر الـ Query Builder بنجاح!");
            return;
        }

        $driverClass::updateOrCreate(
            ['phone' => '777123456'],
            [
                'name'     => 'صالح الموصل',
                'password' => Hash::make('123456'),
            ]
        );

        $this->command->info("تم إدخال بيانات الموصل بنجاح عبر الموديل!");
    }
}