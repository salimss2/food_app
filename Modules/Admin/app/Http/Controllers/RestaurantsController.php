<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Modules\Restaurants\Models\Restaurant;

class RestaurantsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Restaurant::query()->with(['owner', 'owner.roles']);

        // Stats
        $totalRestaurants = Restaurant::count();
        $activeRestaurants = Restaurant::where('status', 'open')->count();
        $inactiveRestaurants = Restaurant::where('status', 'closed')->count();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhereHas('owner', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $restaurants = $query->latest()->paginate(10);

        return view('admin::restaurants', compact(
            'restaurants',
            'totalRestaurants',
            'activeRestaurants',
            'inactiveRestaurants'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name'     => 'required|string|max:255',
                'category' => 'required|string',
                'location' => 'required|string',
                'owner_name'  => 'required|string|max:255',
                'owner_email' => 'required|email|unique:users,email',
                'owner_phone' => 'required|string',
                'password'    => 'required|string|min:8',
                'logo'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            ob_clean();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors()
            ], 422);
        }

        try {
            return DB::transaction(function () use ($request) {
                // 1. Create User
                $user = User::create([
                    'name'     => $request->owner_name,
                    'email'    => $request->owner_email,
                    'phone'    => $request->owner_phone,
                    'password' => Hash::make($request->password),
                    'status'   => 'Active',
                ]);

                // Assign Role
                if (method_exists($user, 'assignRole')) {
                    $user->assignRole('Restaurant Admin');
                }

                // 2. Handle Logo
$logoPath = null;
if ($request->hasFile('logo')) {
    $file = $request->file('logo');
    $filename = time() . '_' . $file->getClientOriginalName();
    
    // التعديل هنا: حددنا القرص 'public' صراحة لضمان ذهاب الصورة للمكان الصحيح
    $file->storeAs('restaurants/logos', $filename, 'public');
    
    $logoPath = 'restaurants/logos/' . $filename;
}

                // 3. Create Restaurant
                $restaurant = Restaurant::create([
                    'name'     => $request->name,
                    'category' => $request->category,
                    'location' => $request->location,
                    'owner_id' => $user->id,
                    'user_id'  => $user->id,
                    'logo'     => $logoPath,
                    'status'   => 'open',
                    'account_status' => 'Active',
                ]);

                $restaurant->load('owner');

                if (ob_get_level() > 0) ob_clean();
                return response()->json([
                    'success' => true,
                    'message' => 'Restaurant and Manager created successfully.',
                    'data'    => $restaurant->append('logo_url')
                ]);
            });
        } catch (\Exception $e) {
            if (ob_get_level() > 0) ob_clean();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create restaurant: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $restaurant = Restaurant::with('owner')->findOrFail($id);
        ob_clean();
        return response()->json([
            'success' => true,
            'data'    => [
                'id'       => $restaurant->id,
                'name'     => $restaurant->name,
                'category' => $restaurant->category,
                'location' => $restaurant->location,
                'status'   => $restaurant->status,
                'account_status' => $restaurant->account_status,
                'logo_url' => $restaurant->logo_url,
                'owner'    => [
                    'name'  => $restaurant->owner->name ?? 'N/A',
                    'email' => $restaurant->owner->email ?? 'N/A',
                    'phone' => $restaurant->owner->phone ?? 'N/A',
                ]
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $user = $restaurant->owner;

        try {
            $request->validate([
                'name'     => 'required|string|max:255',
                'category' => 'required|string',
                'location' => 'required|string',
                'owner_name'  => 'required|string|max:255',
                'owner_email' => 'required|email|unique:users,email,' . ($user->id ?? 0),
                'owner_phone' => 'nullable|string',
                'logo'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            ob_clean();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors()
            ], 422);
        }

        try {
            DB::transaction(function () use ($request, $restaurant, $user) {
                // Update User
                if ($user) {
                    $user->update([
                        'name'  => $request->owner_name,
                        'email' => $request->owner_email,
                        'phone' => $request->owner_phone,
                    ]);
                    if ($request->filled('password')) {
                        $user->update(['password' => Hash::make($request->password)]);
                    }
                }

                // Handle Logo Update
if ($request->hasFile('logo')) {
    // Delete old logo (حذف الصورة القديمة بشكل صحيح)
    if ($restaurant->logo) {
        $oldLogoPath = str_contains($restaurant->logo, '/') ? $restaurant->logo : 'restaurants/logos/' . $restaurant->logo;
        // استخدام قرص public للحذف
        \Illuminate\Support\Facades\Storage::disk('public')->delete($oldLogoPath);
    }
    
    $file = $request->file('logo');
    $filename = time() . '_' . $file->getClientOriginalName();
    
    // التخزين في المسار الصحيح
    $file->storeAs('restaurants/logos', $filename, 'public');
    
    // إسناد المسار الجديد للمطعم
    $restaurant->logo = 'restaurants/logos/' . $filename;
}

                // Update Restaurant
                $restaurant->update([
                    'name'     => $request->name,
                    'category' => $request->category,
                    'location' => $request->location,
                    'status'   => $request->status ?? $restaurant->status,
                    'account_status' => $request->account_status ?? $restaurant->account_status,
                ]);
            });

            $restaurant->load('owner');
            if (ob_get_level() > 0) ob_clean();
            return response()->json([
                'success' => true,
                'message' => 'Restaurant updated successfully.',
                'data'    => $restaurant->append('logo_url')
            ]);
        } catch (\Exception $e) {
            if (ob_get_level() > 0) ob_clean();
            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $user = $restaurant->owner;

        try {
            DB::transaction(function () use ($restaurant, $user) {
                // Delete logo file
                if ($restaurant->logo) {
                    Storage::delete('public/restaurants/logos/' . $restaurant->logo);
                }

                $restaurant->delete();

                // Delete user if they ONLY manage this restaurant
                if ($user && Restaurant::where('owner_id', $user->id)->count() === 0) {
                    $user->delete();
                }
            });

            ob_clean();
            return response()->json([
                'success' => true,
                'message' => 'Restaurant and associated manager deleted successfully.'
            ]);
        } catch (\Exception $e) {
            ob_clean();
            return response()->json([
                'success' => false,
                'message' => 'Deletion failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle Block/Unblock for a restaurant and its manager.
     */
    public function toggleBlock($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $user = $restaurant->owner;

        try {
            DB::transaction(function() use ($restaurant, $user) {
                $newStatus = ($restaurant->account_status === 'Blocked') ? 'Active' : 'Blocked';
                
                $restaurant->update(['account_status' => $newStatus]);
                
                if ($user) {
                    $user->update(['status' => $newStatus]);
                }
            });

            ob_clean();
            return response()->json([
                'success' => true,
                'message' => 'Restaurant status updated to ' . $restaurant->account_status,
                'new_status' => $restaurant->account_status
            ]);
        } catch (\Exception $e) {
            ob_clean();
            return response()->json([
                'success' => false,
                'message' => 'Toggle block failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle Open/Closed state via AJAX.
     */
    public function toggleState($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        
        try {
            $newState = ($restaurant->status === 'open') ? 'closed' : 'open';
            $restaurant->update(['status' => $newState]);

            ob_clean();
            return response()->json([
                'success' => true,
                'message' => 'Restaurant is now ' . $newState,
                'is_open' => ($newState === 'open')
            ]);
        } catch (\Exception $e) {
            ob_clean();
            return response()->json([
                'success' => false,
                'message' => 'Toggle state failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
