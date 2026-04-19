<?php

namespace Modules\Restaurants\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the authenticated user's profile.
     */
    public function show()
    {
        $user = Auth::user();
        $user->load('restaurant');

        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'restaurant_name' => $user->restaurant ? $user->restaurant->name : '',
            'logo_url' => ($user->restaurant && $user->restaurant->logo) 
                ? asset('storage/' . $user->restaurant->logo) 
                : asset('assets/default-logo.png'),
        ];

        ob_clean();
        return response()->json([
            'status' => true,
            'data' => $data,
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

        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'restaurant_name' => $user->restaurant ? $user->restaurant->name : '',
            'logo_url' => ($user->restaurant && $user->restaurant->logo) 
                ? asset('storage/' . $user->restaurant->logo) 
                : asset('assets/default-logo.png'),
        ];

        ob_clean();
        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'data' => $data,
        ]);
    }
}
