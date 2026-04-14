<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // إنشاء الدور
        $role = Role::firstOrCreate([
            'name' => 'System Admin',
            'guard_name' => 'web'
        ]);

        $admins = [
            [
                'name' => 'Admin Test 1',
                'email' => 'admin1@example.com',
                'password' => bcrypt('12345678'),
            ],
            [
                'name' => 'Admin Test 2',
                'email' => 'admin2@example.com',
                'password' => bcrypt('12345678'),
            ],
        ];

        foreach ($admins as $adminData) {

            $user = User::firstOrCreate(
                ['email' => $adminData['email']],
                $adminData
            );

            // ربط الدور بالطريقة الصحيحة
            if (!$user->hasRole('System Admin')) {
                $user->assignRole('System Admin');
            }
        }
    }
}