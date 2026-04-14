<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // 1. تنظيف الكاش الخاص بالمكتبة (خطوة مهمة لتجنب الأخطاء)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. تعريف الصلاحيات الأساسية
        $permissions = [
            'view',    // العرض
            'create',  // الإنشاء
            'edit',    // التعديل
            'delete',  // الحذف
            'respond'  // الرد (خاص بالدعم الفني والشكاوى)
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 3. تعريف الأدوار (Roles)
        $roles = [
            'System Admin',      // المدير العام
            'Operations Manager', // مدير العمليات
            'Financial Accountant', // المحاسب
            'Customer Support',   // الدعم الفني
            'Marketing Officer',  // مسؤول التسويق
            'Customer',           // الزبون
            'Driver',             // السائق
            'Restaurant Admin'    // صاحب المطعم
        ];

        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

            // 4. توزيع الصلاحيات بناءً على كل دور
            
            if ($roleName === 'System Admin') {
                // يملك كل شيء
                $role->syncPermissions($permissions);
            } 
            
            elseif ($roleName === 'Operations Manager') {
                // يملك العرض والإنشاء والتعديل (بدون حذف)
                $role->syncPermissions(['view', 'create', 'edit']);
            } 
            
            elseif ($roleName === 'Financial Accountant') {
                // يملك العرض والتعديل المالي فقط
                $role->syncPermissions(['view', 'edit']);
            } 
            
            elseif ($roleName === 'Customer Support') {
                // يملك العرض والرد فقط
                $role->syncPermissions(['view', 'respond']);
            } 
            
            elseif ($roleName === 'Marketing Officer') {
                // يملك العرض والإنشاء والتعديل للعروض
                $role->syncPermissions(['view', 'create', 'edit']);
            }
            
            // أدوار التطبيقات (الزبون والسائق وصاحب المطعم) غالباً نعطيهم صلاحية العرض كحد أدنى
            else {
                $role->syncPermissions(['view']);
            }
        }
        
        // نصيحة: يمكنك هنا إنشاء مستخدم تجريبي لكل دور للتأكد من نجاح العملية
        // $user = \App\Models\User::find(1);
        // $user->assignRole('System Admin');
    }
}