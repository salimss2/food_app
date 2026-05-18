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
use Modules\Restaurants\Http\Requests\UpdateRestaurantLocationRequest;

/**
 * ProfileController
 *
 * Manages the restaurant owner's personal profile and restaurant open/closed status.
 *
 * Routes (all protected by auth:sanctum):
 *   GET   /api/v1/profile                          — view profile + restaurant info
 *   POST  /api/v1/profile/update                   — update name, phone ONLY (image moved to restaurant)
 *   POST  /api/v1/restaurant/update                — update restaurant name, location, logo, desc, phone
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

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'profile_picture' => $user->profile_picture
                    ? asset('storage/' . $user->profile_picture)
                    : null,
                'restaurant' => $user->restaurant
                    ? new RestaurantResource($user->restaurant)
                    : null,
            ],
        ]);
    }


    /**
     * POST /api/v1/profile/update
     *
     * Updates user's personal details: name, phone.
     * Note: Profile picture logic has been moved to Restaurant Logo as per new flow.
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
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
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

        return response()->json([
            'status' => true,
            'message' => "Restaurant is now {$newStatus}.",
            'data' => new RestaurantResource($restaurant->fresh()),
        ]);
    }

    /**
     * POST /api/v1/restaurant/update
     *
     * Updates the authenticated owner's restaurant information including logo.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function updateRestaurant(Request $request): JsonResponse
    {
        $user = Auth::user();
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            return response()->json([
                'status' => false,
                'message' => 'No restaurant associated with this account.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $restaurant->name = $request->name;
        $restaurant->location = $request->location;
        $restaurant->description = $request->description;
        $restaurant->phone = $request->phone;

        // Handle Logo Upload
        if ($request->hasFile('logo')) {
            // Delete old logo from storage
            if ($restaurant->logo && Storage::disk('public')->exists($restaurant->logo)) {
                Storage::disk('public')->delete($restaurant->logo);
            }

            $file = $request->file('logo');
            $filename = time() . '_logo_' . $user->id . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('restaurants/logos', $filename, 'public');
            $restaurant->logo = $path;
        }

        $restaurant->save();

        return response()->json([
            'status' => true,
            'message' => 'Restaurant information updated successfully.',
            'data' => new RestaurantResource($restaurant->fresh()),
        ]);
    }

    /**
     * POST /api/v1/restaurant/update-location
     *
     * Updates the authenticated owner's restaurant geographical coordinates.
     *
     * @param  UpdateRestaurantLocationRequest  $request
     * @return JsonResponse
     */
    public function updateLocation(UpdateRestaurantLocationRequest $request): JsonResponse
    {
        $user = Auth::user();
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            return response()->json([
                'status' => false,
                'message' => 'No restaurant associated with this account.',
            ], 404);
        }

        $restaurant->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        Log::info("Restaurant location updated", [
            'owner_id' => $user->id,
            'restaurant_id' => $restaurant->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Restaurant location updated successfully.',
            'data' => [
                'latitude' => (float) $restaurant->latitude,
                'longitude' => (float) $restaurant->longitude,
            ],
        ]);
    }
}
