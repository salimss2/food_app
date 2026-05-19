<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controllers\HasMiddleware; // إضافة هذا
use Illuminate\Routing\Controllers\Middleware;
class UserController extends Controller implements HasMiddleware // إضافة implements
{
    // استبدل الـ __construct القديم بهذه الدالة:
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_users', only: ['index', 'show']),
            new Middleware('permission:create_users', only: ['create', 'store']),
            new Middleware('permission:edit_users', only: ['edit', 'update']),
            new Middleware('permission:delete_users', only: ['destroy']),
        ];
    }
    // public function index()
    // {
    //     // استرجاع كل المستخدمين
    //     $users = User::with('roles')->paginate(10); // مع Pagination
    //     return view('admin::user management.index', compact('users'));
    // }

    //     public function index()
// {
//     $users = User::with('roles')->paginate(10);
//     $roles = \Spatie\Permission\Models\Role::all(); // إرسال كل الأدوار
//     return view('admin::user management.index', compact('users', 'roles'));
// }

    public function index(Request $request)
    {
        // Exclude Drivers and Restaurant Admins from this view
        $query = User::with('roles')->whereDoesntHave('roles', function ($q) {
            $q->whereIn('name', ['Driver', 'Restaurant Admin']);
        });

        // Tabs Filter: Active, Blocked, Archived
        if ($request->filled('tab')) {
            if ($request->tab === 'Archived') {
                $query->onlyTrashed();
            } else {
                $query->where('status', $request->tab);
            }
        } else {
            // Optional fallback for legacy status filtering if needed
            if ($request->filled('status') && $request->status != 'all') {
                $query->where('status', $request->status);
            }
        }

        // Role Filter Dropdown
        if ($request->filled('role_filter') && $request->role_filter !== 'all') {
            $roleFilter = $request->role_filter;
            $query->whereHas('roles', function ($q) use ($roleFilter) {
                $q->where('name', $roleFilter);
            });
        }

        // Backend Search Support
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%");
            });
        }

        $users = $query->latest()->paginate(10)->appends($request->all());

        // Modal Roles (excluding Driver, Restaurant Admin)
        $roles = \Spatie\Permission\Models\Role::whereNotIn('name', [
            'Driver',
            'Restaurant Admin'
        ])->get();

        // Filter Dropdown Roles (excluding Driver, Restaurant Admin)
        $filterRoles = \Spatie\Permission\Models\Role::whereNotIn('name', [
            'Driver',
            'Restaurant Admin'
        ])->get();

        return view('admin::users', compact('users', 'roles', 'filterRoles'));
    }

    public function toggleBlock(User $user)
    {
        $user->status = strtolower($user->status) === 'active' ? 'Blocked' : 'Active';
        $user->save();

        $message = $user->status === 'Active' ? 'تم التفعيل بنجاح' : 'تم الحظر بنجاح';
        return back()->with('success', $message);
    }

    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id);

        // ── Customer-specific stats ──────────────────────────
        $totalOrders = 0;
        $totalSpent = 0.00;
        $pendingOrders = 0;
        $accountAge = $user->created_at->diffForHumans();

        // If you have an Order model, uncomment and adapt:
        // if ($user->hasRole('Customer')) {
        //     $orders        = $user->orders()->get();
        //     $totalOrders   = $orders->count();
        //     $totalSpent    = $orders->sum('total_amount');
        //     $pendingOrders = $orders->where('status', 'pending')->count();
        // }

        return view('admin::user-details', compact(
            'user',
            'totalOrders',
            'totalSpent',
            'pendingOrders',
            'accountAge'
        ));
    }


    // عرض نموذج إضافة مستخدم جديد
    public function create()
    {
        // جلب كل الأدوار المتاحة
        $roles = Role::all();
        return view('admin::user management.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|string|min:8',
            'phone' => 'nullable|string',
            'role' => 'required|string',
            'status' => 'required|in:Active,Blocked,Inactive',
        ]);

        $password = $request->filled('password') ? $request->password : \Illuminate\Support\Str::random(10);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($password),
            'phone' => $request->phone,
            'status' => $request->status,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')->with('success', 'User "' . $user->name . '" created successfully!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|exists:roles,name',
            'status' => 'required|in:Active,Blocked,Inactive'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->status = $request->status;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        if ($request->filled('role')) {
            $user->syncRoles([$request->role]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');
    }
}