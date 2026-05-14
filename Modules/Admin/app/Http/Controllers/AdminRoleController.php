<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class AdminRoleController extends Controller
{
    /**
     * Fetch all roles, permissions, and available system permissions for the UI.
     */
    public function apiIndex()
    {
        // 1. تعريف الأدوار المطلوب إخفاؤها
        $excludedRoles = ['Customer', 'Driver', 'Restaurant Admin'];

        // 2. جلب الأدوار (باستثناء أدوار التطبيقات) وتهيئتها للواجهة
        $roles = Role::whereNotIn('name', $excludedRoles)
            ->with('permissions')
            ->get()
            ->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'desc' => $role->name === 'System Admin' ? "Full system access." : "Custom access level.",
                    'usersCount' => DB::table('model_has_roles')->where('role_id', $role->id)->count(),
                    'isSystem' => in_array($role->name, ['System Admin', 'Super Admin']),
                    'permissions' => $role->permissions->pluck('name') // Flatten to array of names
                ];
            });

        $permissions = Permission::all()->pluck('name');

        return response()->json([
            'success' => true,
            'roles' => $roles,
            'all_permissions' => $permissions
        ]);
    }

    /**
     * Create a new custom role.
     */
    public function apiStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name'
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully.',
            'role' => $role
        ]);
    }

    /**
     * Update an existing role.
     */
    public function apiUpdate(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        if (in_array($role->name, ['System Admin', 'Super Admin'])) {
            return response()->json(['success' => false, 'message' => 'Cannot edit system roles.'], 403);
        }

        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $id
        ]);

        $role->update(['name' => $request->name]);

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully.'
        ]);
    }

    /**
     * Delete an existing role.
     */
    public function apiDestroy($id)
    {
        $role = Role::findOrFail($id);

        if (in_array($role->name, ['System Admin', 'Super Admin'])) {
            return response()->json(['success' => false, 'message' => 'Cannot delete system roles.'], 403);
        }

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully.'
        ]);
    }

    /**
     * Sync permissions for a specific role.
     */
    public function syncPermissions(Request $request, $id)
    {
        $request->validate([
            'permissions' => 'array'
        ]);

        $role = Role::findOrFail($id);

        if (in_array($role->name, ['System Admin', 'Super Admin'])) {
            return response()->json(['success' => false, 'message' => 'Cannot modify Super Admin permissions directly.'], 403);
        }

        $role->syncPermissions($request->permissions ?? []);

        return response()->json([
            'success' => true,
            'message' => 'Permissions synchronized successfully.'
        ]);
    }
}
