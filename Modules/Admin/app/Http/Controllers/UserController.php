<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
class UserController extends Controller
{
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
    // 1. جلب المستخدمين الذين يحملون رتبة Admin أو Customer فقط
    // استخدمنا role(['Admin', 'Customer']) لضمان عدم ظهور الموصلين أو أصحاب المطاعم
$query = User::with('roles')
    ->whereDoesntHave('roles', function ($q) {
        $q->whereIn('name', ['Driver', 'Restaurant Admin']);
    });

    // 2. فلترة بناءً على الحالة (Status)
    if ($request->filled('status') && $request->status != 'all') {
        $query->where('status', $request->status);
    }

    // 3. فلترة البحث (إذا كنت تستخدم نص البحث من الـ Input في الصفحة)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%$search%")
              ->orWhere('email', 'like', "%$search%")
              ->orWhere('phone', 'like', "%$search%");
        });
    }

    // 4. دعم الأرشيف (المحذوفين ناعماً)
    if ($request->has('view_deleted')) {
        $query->onlyTrashed();
    }

    // 5. الترقيم مع الحفاظ على الفلاتر
    $users = $query->latest()->paginate(10)->appends($request->all());

    // 6. جلب الأدوار المسموح إضافتها من هذه الصفحة فقط (Admin و Customer)
    // هذا سيجعل الـ Modal يعرض الخيارين المناسبين فقط
$roles = \Spatie\Permission\Models\Role::whereNotIn('name', [
        'Driver',
        'Restaurant Admin'
    ])->get();

    return view('admin::users', compact('users', 'roles'));
}

    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id);

        // ── Customer-specific stats ──────────────────────────
        $totalOrders   = 0;
        $totalSpent    = 0.00;
        $pendingOrders = 0;
        $accountAge    = $user->created_at->diffForHumans();

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

    // حفظ المستخدم الجديد
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|exists:roles,name',
            'status'   => 'required|in:Active,Blocked,Inactive',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'phone'    => $request->phone,
            'status'   => $request->status,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')->with('success', 'User "' . $user->name . '" created successfully!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
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