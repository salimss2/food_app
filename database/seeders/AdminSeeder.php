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
            ],
            [
                'name' => 'Admin Test 2',
                'email' => 'admin2@example.com',
            ],
        ];

        foreach ($admins as $adminData) {
            $user = User::updateOrCreate(
                ['email' => $adminData['email']],
                [
                    'name' => $adminData['name'],
                    'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
                    'status' => 'active',
                ]
            );

            // ربط الدور بالطريقة الصحيحة
            $user->assignRole('System Admin');
        }
    }
}