<?php

namespace Modules\Restaurants\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Restaurants\Http\Resources\RestaurantResource;

class ProfileController extends Controller
{
    /**
     * Display the authenticated user's profile.
     */
    public function show()
    {
        $user = Auth::user();
        $user->load('restaurant');

        ob_clean();
        return response()->json([
            'status' => true,
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'restaurant' => new RestaurantResource($user->restaurant),
            ],
        ]);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => 'required|string|max:20',
            'restaurant_name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            ob_clean();
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Update User info
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        // Update Restaurant info
        if ($user->restaurant) {
            $restaurantData = [
                'name' => $request->restaurant_name,
            ];

            if ($request->hasFile('logo')) {
                // Delete old logo if it exists
                if ($user->restaurant->logo && Storage::disk('public')->exists($user->restaurant->logo)) {
                    Storage::disk('public')->delete($user->restaurant->logo);
                }

                $file = $request->file('logo');
                $path = Storage::disk('public')->put('restaurants/logos', $file);
                $restaurantData['logo'] = $path;
            }

            $user->restaurant->update($restaurantData);
        }

        // Fetch fresh data
        $user->refresh();
        $user->load('restaurant');

        ob_clean();
        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'restaurant' => new RestaurantResource($user->restaurant),
            ],
        ]);
    }

    /**
     * Toggle the status of the restaurant (open/closed).
     */
    public function toggleStatus()
    {
        $user = Auth::user();
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            return response()->json([
                'status' => false,
                'message' => 'Restaurant not found',
            ], 404);
        }

        // Toggle status between 'open' and 'closed'
        $newStatus = ($restaurant->status === 'open') ? 'closed' : 'open';
        $restaurant->update(['status' => $newStatus]);

        ob_clean();
        return response()->json([
            'status' => true,
            'message' => 'Restaurant status toggled successfully',
            'data' => new RestaurantResource($restaurant),
        ]);
    }
}
