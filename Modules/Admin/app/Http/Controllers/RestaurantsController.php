<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Modules\Restaurants\Models\Restaurant;
use Illuminate\Routing\Controllers\HasMiddleware; // أضف هذا
use Illuminate\Routing\Controllers\Middleware;

class RestaurantsController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_restaurants', only: ['index', 'show']),
            new Middleware('permission:create_restaurants', only: ['create', 'store']),
            new Middleware('permission:edit_restaurants', only: ['edit', 'update', 'toggleBlock', 'toggleState']),
            new Middleware('permission:delete_restaurants', only: ['destroy']),
        ];
    }
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
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhereHas('owner', function ($uq) use ($search) {
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
                'name' => 'required|string|max:255',
                'category' => 'required|string',
                'location' => 'required|string',
                'owner_name' => 'required|string|max:255',
                'owner_email' => 'required|email|unique:users,email',
                'owner_phone' => 'required|string',
                'password' => 'required|string|min:8',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            ob_clean();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        try {
            return DB::transaction(function () use ($request) {
                // 1. Create User
                $user = User::create([
                    'name' => $request->owner_name,
                    'email' => $request->owner_email,
                    'phone' => $request->owner_phone,
                    'password' => Hash::make($request->password),
                    'status' => 'Active',
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

                    $file->storeAs('restaurants/logos', $filename, 's3');

                    $logoPath = 'restaurants/logos/' . $filename;
                }

                // 3. Create Restaurant
                $restaurant = Restaurant::create([
                    'name' => $request->name,
                    'category' => $request->category,
                    'location' => $request->location,
                    'owner_id' => $user->id,
                    'user_id' => $user->id,
                    'logo' => $logoPath,
                    'status' => 'open',
                    'account_status' => 'Active',
                ]);

                $restaurant->load('owner');

                if (ob_get_level() > 0)
                    ob_clean();
                return response()->json([
                    'success' => true,
                    'message' => 'Restaurant and Manager created successfully.',
                    'data' => $restaurant->append('logo_url')
                ]);
            });
        } catch (\Exception $e) {
            if (ob_get_level() > 0)
                ob_clean();
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
        // جلب المطعم مع بيانات المالك والوجبات والأقسام
        $restaurant = Restaurant::with(['owner', 'meals', 'mealCategories'])->findOrFail($id);

        // --- Performance Metrics Calculation ---

        // Orders & Revenue
        $ordersQuery = \Modules\Orders\Models\Order::where('restaurant_id', $id);
        $totalOrders = (clone $ordersQuery)->count();
        $totalRevenue = (clone $ordersQuery)->where('payment_status', 'completed')->sum('total');

        // Estimated Net Profit (85% to restaurant)
        $netProfit = $totalRevenue * 0.85;

        // Top Selling Item
        $topMealData = \Modules\Orders\Models\OrderItem::whereHas('order', function ($q) use ($id) {
            $q->where('restaurant_id', $id);
        })
            ->select('meal_id', \DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('meal_id')
            ->orderBy('total_qty', 'desc')
            ->first();

        $topMealName = $topMealData ? (\Modules\Restaurants\Models\Meal::find($topMealData->meal_id)->name ?? 'N/A') : 'N/A';

        // Summary Statistics
        $metrics = [
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue,
            'net_profit' => $netProfit,
            'top_meal_name' => $topMealName,
            'active_meals' => $restaurant->meals()->where('available', true)->count(),
            'categories_count' => $restaurant->mealCategories()->count(),
            'avg_rating' => $restaurant->rating ?? 0.0,
            'reviews_count' => 0,
        ];

        // --- Performance Metrics Tab Data ---

        // 1. Daily Revenue (Last 7 Days)
        $dailyRevenue = \Modules\Orders\Models\Order::where('restaurant_id', $id)
            ->where('payment_status', 'completed')
            ->where('created_at', '>=', now()->subDays(6))
            ->selectRaw('DATE(created_at) as date, SUM(total) as revenue')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->pluck('revenue', 'date')
            ->toArray();

        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dayName = now()->subDays($i)->format('D');
            $chartData[$dayName] = $dailyRevenue[$date] ?? 0;
        }

        // 2. Order Status Mix
        $statusCounts = (clone $ordersQuery)->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $completedCount = $statusCounts['delivered'] ?? 0;
        $cancelledCount = $statusCounts['canceled'] ?? 0;
        $statusMixTotal = $totalOrders > 0 ? $totalOrders : 1;

        // 3. Top Selling Items (Top 5)
        $topMealsData = \Modules\Orders\Models\OrderItem::whereHas('order', function ($q) use ($id) {
            $q->where('restaurant_id', $id);
        })
            ->select('meal_id', \DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('meal_id')
            ->orderBy('total_qty', 'desc')
            ->take(5)
            ->get();

        $topMeals = [];
        foreach ($topMealsData as $item) {
            $meal = \Modules\Restaurants\Models\Meal::find($item->meal_id);
            if ($meal) {
                $topMeals[] = [
                    'name' => $meal->name,
                    'qty' => $item->total_qty
                ];
            }
        }

        $metrics = array_merge($metrics, [
            'completed_percentage' => round(($completedCount / $statusMixTotal) * 100),
            'cancelled_percentage' => round(($cancelledCount / $statusMixTotal) * 100),
            'top_meals' => $topMeals,
            'chart_data' => $chartData,
            'max_revenue' => count($chartData) > 0 ? max($chartData) : 1,
        ]);

        $latestOrders = \Modules\Orders\Models\Order::where('restaurant_id', $id)
            ->with('user')
            ->latest()
            ->take(10)
            ->get();

        // 5. Active Drivers (for Logistics & Map)
        $drivers = \App\Models\User::whereIn('id', function ($query) use ($id) {
            $query->select('driver_id')
                ->from('orders')
                ->where('restaurant_id', $id)
                ->whereIn('status', ['preparing', 'picked_up', 'out_for_delivery'])
                ->whereNotNull('driver_id');
        })
            ->with('driverProfile')
            ->get()
            ->map(function ($user) {
                return [
                    'name' => $user->name,
                    'lat' => $user->driverProfile->latitude ?? null,
                    'lng' => $user->driverProfile->longitude ?? null,
                ];
            })
            ->filter(fn($d) => $d['lat'] && $d['lng'])
            ->values();

        return view('admin::restaurant-details', compact('restaurant', 'metrics', 'latestOrders', 'drivers'));
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
                'name' => 'required|string|max:255',
                'category' => 'required|string',
                'location' => 'required|string',
                'owner_name' => 'required|string|max:255',
                'owner_email' => 'required|email|unique:users,email,' . ($user->id ?? 0),
                'owner_phone' => 'nullable|string',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            ob_clean();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        try {
            DB::transaction(function () use ($request, $restaurant, $user) {
                // Update User
                if ($user) {
                    $user->update([
                        'name' => $request->owner_name,
                        'email' => $request->owner_email,
                        'phone' => $request->owner_phone,
                    ]);
                    if ($request->filled('password')) {
                        $user->update(['password' => Hash::make($request->password)]);
                    }
                }

                // Handle Logo Update
                if ($request->hasFile('logo')) {
                    // Delete old logo
                    if ($restaurant->logo) {
                        $oldLogoPath = str_contains($restaurant->logo, '/') ? $restaurant->logo : 'restaurants/logos/' . $restaurant->logo;
                        if ($oldLogoPath) {
                            \Illuminate\Support\Facades\Storage::disk('s3')->delete($oldLogoPath);
                        }
                    }

                    $file = $request->file('logo');
                    $filename = time() . '_' . $file->getClientOriginalName();

                    $file->storeAs('restaurants/logos', $filename, 's3');

                    // إسناد المسار الجديد للمطعم
                    $restaurant->logo = 'restaurants/logos/' . $filename;
                }

                // Update Restaurant
                $restaurant->update([
                    'name' => $request->name,
                    'category' => $request->category,
                    'location' => $request->location,
                    'status' => $request->status ?? $restaurant->status,
                    'account_status' => $request->account_status ?? $restaurant->account_status,
                ]);
            });

            $restaurant->load('owner');
            if (ob_get_level() > 0)
                ob_clean();
            return response()->json([
                'success' => true,
                'message' => 'Restaurant updated successfully.',
                'data' => $restaurant->append('logo_url')
            ]);
        } catch (\Exception $e) {
            if (ob_get_level() > 0)
                ob_clean();
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
                    Storage::disk('s3')->delete($restaurant->logo);
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
            DB::transaction(function () use ($restaurant, $user) {
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

    /**
     * Store a new meal for a restaurant.
     */
    public function storeMeal(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:meal_categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $meal = \Modules\Restaurants\Models\Meal::create([
                    'restaurant_id' => $request->restaurant_id,
                    'meal_category_id' => $request->category_id,
                    'name' => $request->name,
                    'price' => $request->price,
                    'description' => $request->description,
                    'is_active' => true,
                ]);

                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->storeAs('meals', $filename, 's3');
                    $meal->update(['image' => 'meals/' . $filename]);
                }

                return back()->with('success', __('Meal added successfully.'));
            });
        } catch (\Exception $e) {
            return back()->with('error', __('Failed to add meal: ') . $e->getMessage());
        }
    }

    /**
     * Store a new category for a restaurant.
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $category = \Modules\Restaurants\Models\MealCategory::create([
                    'restaurant_id' => $request->restaurant_id,
                    'name' => $request->name,
                ]);

                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->storeAs('categories', $filename, 's3');
                    $category->update(['image' => 'categories/' . $filename]);
                }

                return back()->with('success', __('Category added successfully.'));
            });
        } catch (\Exception $e) {
            return back()->with('error', __('Failed to add category: ') . $e->getMessage());
        }
    }

    /**
     * Update an existing meal.
     */
    public function updateMeal(Request $request, $id)
    {
        $meal = \Modules\Restaurants\Models\Meal::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:meal_categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        try {
            DB::transaction(function () use ($request, $meal) {
                $meal->update([
                    'meal_category_id' => $request->category_id,
                    'name' => $request->name,
                    'price' => $request->price,
                    'description' => $request->description,
                ]);

                if ($request->hasFile('image')) {
                    // Delete old image if exists
                    if ($meal->image) {
                        \Illuminate\Support\Facades\Storage::disk('s3')->delete($meal->image);
                    }
                    $file = $request->file('image');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->storeAs('meals', $filename, 'public');
                    $meal->update(['image' => 'meals/' . $filename]);
                }
            });

            return back()->with('success', __('Meal updated successfully.'));
        } catch (\Exception $e) {
            return back()->with('error', __('Failed to update meal: ') . $e->getMessage());
        }
    }

    /**
     * Delete a meal.
     */
    public function destroyMeal($id)
    {
        $meal = \Modules\Restaurants\Models\Meal::findOrFail($id);

        try {
            DB::transaction(function () use ($meal) {
                if ($meal->image) {
                    \Illuminate\Support\Facades\Storage::disk('s3')->delete($meal->image);
                }
                $meal->delete();
            });

            return back()->with('success', __('Meal deleted successfully.'));
        } catch (\Exception $e) {
            return back()->with('error', __('Failed to delete meal: ') . $e->getMessage());
        }
    }

    /**
     * Toggle meal availability.
     */
    public function toggleMealAvailability($id)
    {
        try {
            $meal = \Modules\Restaurants\Models\Meal::findOrFail($id);
            $meal->available = !$meal->available;
            $meal->save();

            return response()->json([
                'status' => true,
                'available' => (int) $meal->available,
                'message' => __('Status updated successfully.')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
