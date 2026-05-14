<?php

namespace Modules\Restaurants\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Modules\Restaurants\Http\Resources\RestaurantResource;

/**
 * ProfileController
 *
 * Manages the restaurant owner's personal profile and restaurant open/closed status.
 *
 * Routes (all protected by auth:sanctum):
 *   GET   /api/v1/profile                          — view profile + restaurant info
 *   POST  /api/v1/profile/update                   — update name, phone, profile_picture ONLY
 *   PATCH /api/v1/profile/toggle-restaurant-status — toggle open/closed
 */
class ProfileController extends Controller
{
    /**
     * GET /api/v1/profile
     *
     * Returns the authenticated owner's profile details AND associated
     * restaurant info (name, is_open status) for the Flutter app header.
     *
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        $user = Auth::user();
        $user->load('restaurant');

        ob_clean();
        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $user->id,
                'user_name' => $user->name,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'profile_picture' => $user->profile_picture
                    ? asset('storage/' . $user->profile_picture)
                    : null,
                'restaurant_name' => $user->restaurant ? $user->restaurant->name : null,
                'status' => $user->restaurant ? $user->restaurant->status : null,
                'restaurant' => $user->restaurant
                    ? new RestaurantResource($user->restaurant)
                    : null,
            ],
        ]);
    }


    /**
     * POST /api/v1/profile/update
     *
     * STRICTLY updates only 3 fields: name, phone, profile_picture.
     * Requests attempting to update email or restaurant_name are silently ignored.
     * Supports multipart/form-data for image upload.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        if ($validator->fails()) {
            ob_clean();
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // --- Update only the 3 allowed user fields ---
        $userData = [
            'name' => $request->name,
            'phone' => $request->phone,
            // email and restaurant_name are intentionally excluded.
        ];

        // --- Handle profile picture upload ---
        if ($request->hasFile('profile_picture')) {
            // Delete old picture from storage to avoid orphaned files.
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $file = $request->file('profile_picture');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('users/profiles', $filename, 'public');
            $userData['profile_picture'] = 'users/profiles/' . $filename;
        }

        $user->update($userData);
        $user->refresh();

        ob_clean();
        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'profile_picture' => $user->profile_picture
                    ? asset('storage/' . $user->profile_picture)
                    : null,
            ],
        ]);
    }

    /**
     * PATCH /api/v1/profile/toggle-restaurant-status
     *
     * Toggles the restaurant's status between 'open' and 'closed'.
     * Returns the updated RestaurantResource so Flutter can update its RxBool.
     *
     * @return JsonResponse
     */
    public function toggleStatus(): JsonResponse
    {
        $user = Auth::user();
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            return response()->json([
                'status' => false,
                'message' => 'No restaurant associated with this account.',
            ], 404);
        }

        $newStatus = ($restaurant->status === 'open') ? 'closed' : 'open';
        $restaurant->update(['status' => $newStatus]);

        Log::info("Restaurant status toggled", [
            'owner_id' => $user->id,
            'restaurant_id' => $restaurant->id,
            'new_status' => $newStatus,
        ]);

        ob_clean();
        return response()->json([
            'status' => true,
            'message' => "Restaurant is now {$newStatus}.",
            'data' => new RestaurantResource($restaurant->fresh()),
        ]);
    }
}
