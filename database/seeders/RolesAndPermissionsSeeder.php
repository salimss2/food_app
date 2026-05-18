<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // 1. تنظيف الكاش الخاص بالمكتبة
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. تنظيف الصلاحيات القديمة الخاطئة بأمان تام
        $oldPermissions = ['view', 'create', 'edit', 'delete', 'respond'];
        Permission::whereIn('name', $oldPermissions)->delete();

        // 3. تعريف الصلاحيات المركبة (Action_Module) لموقع الويب
        $permissions = [
            // المستخدمين
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            // المطاعم
            'view_restaurants',
            'create_restaurants',
            'edit_restaurants',
            'delete_restaurants',
            // الطلبات
            'view_orders',
            'edit_orders',
            'manage_order_status',
            // المالية
            'view_financials',
            'manage_payments',
            'manage_commissions',
            // الشكاوى
            'view_complaints',
            'respond_complaints',
            // الإعدادات (أخطر صلاحية)
            'manage_settings',
            'manage_roles'
        ];

        // إنشاء الصلاحيات
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 4. تعريف أدوار موقع الويب (Dashboard) وتوزيع الصلاحيات

        // --- المدير العام ---
        $systemAdmin = Role::firstOrCreate(['name' => 'System Admin', 'guard_name' => 'web']);
        $systemAdmin->syncPermissions(Permission::where('guard_name', 'web')->get()); // يأخذ كل شيء

        // --- مدير العمليات ---
        $operationsManager = Role::firstOrCreate(['name' => 'Operations Manager', 'guard_name' => 'web']);
        $operationsManager->syncPermissions([
            'view_users',
            'create_users',
            'edit_users',
            'view_restaurants',
            'create_restaurants',
            'edit_restaurants',
            'view_orders',
            'edit_orders',
            'manage_order_status'
        ]);

        // --- المحاسب ---
        $financialAccountant = Role::firstOrCreate(['name' => 'Financial Accountant', 'guard_name' => 'web']);
        $financialAccountant->syncPermissions([
            'view_orders',
            'view_financials',
            'manage_payments',
            'manage_commissions'
        ]);

        // --- الدعم الفني ---
        $customerSupport = Role::firstOrCreate(['name' => 'Customer Support', 'guard_name' => 'web']);
        $customerSupport->syncPermissions([
            'view_users',
            'view_orders',
            'view_complaints',
            'respond_complaints'
        ]);

        // --- محلل البيانات (الذي نسيته) ---
        $dataAnalyst = Role::firstOrCreate(['name' => 'Data Analyst', 'guard_name' => 'web']);
        $dataAnalyst->syncPermissions([
            // صلاحيات قراءة فقط
            'view_users',
            'view_restaurants',
            'view_orders',
            'view_financials',
            'view_complaints'
        ]);

        // 5. تعريف أدوار تطبيقات الجوال والمطاعم (Flutter / Restaurant Owners)
        // ننشئها لكلا الجاردين (web و sanctum) لتجنب أي تعارض في الصلاحيات أو الجلسات
        $appRoles = [
            'Customer',
            'customer',
            'Driver',
            'driver',
            'Restaurant Admin',
            'restaurant admin',
            'Restaurant Owner',
            'restaurant owner'
        ];
        foreach ($appRoles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'sanctum']);
        }

        // 6. مسح الكاش فوراً لتطبيق التغييرات الجديدة
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}