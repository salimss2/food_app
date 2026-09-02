<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DistanceSlab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeliveryCalculationController extends Controller
{
    public const EXTRA_STOP_FEE = 1000.00;

    /**
     * Calculate delivery fee based on coordinates.
     * POST /api/delivery/calculate-fee
     */
    public function calculate(Request $request)
    {
        // Normalize stringified restaurant_ids if needed (common in multipart / query parameters)
        $restaurantIds = $request->input('restaurant_ids');
        if (is_string($restaurantIds)) {
            $decoded = json_decode($restaurantIds, true);
            $restaurantIds = is_array($decoded) ? $decoded : array_map('trim', explode(',', $restaurantIds));
            $request->merge(['restaurant_ids' => $restaurantIds]);
        }

        $validator = Validator::make($request->all(), [
            'customer_lat' => 'required|numeric|between:-90,90',
            'customer_lng' => 'required|numeric|between:-180,180',
            'restaurant_ids' => 'nullable|array',
            'restaurant_ids.*' => 'integer|exists:restaurants,id',
            'restaurant_id' => 'nullable|integer|exists:restaurants,id',
            'restaurant_lat' => 'nullable|numeric|between:-90,90',
            'restaurant_lng' => 'nullable|numeric|between:-180,180',
            'restaurants' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $custLat = (float) $request->input('customer_lat');
        $custLng = (float) $request->input('customer_lng');

        $restaurantCoords = [];

        // 1. Check if restaurant_ids array is provided
        if (!empty($restaurantIds) && is_array($restaurantIds)) {
            $ids = array_unique(array_filter($restaurantIds));
            $restaurants = \App\Models\Restaurant::whereIn('id', $ids)->get();
            foreach ($restaurants as $r) {
                if (!is_null($r->latitude) && !is_null($r->longitude)) {
                    $restaurantCoords[] = [
                        'id' => $r->id,
                        'lat' => (float) $r->latitude,
                        'lng' => (float) $r->longitude,
                    ];
                }
            }
        } elseif ($request->filled('restaurants') && is_array($request->input('restaurants'))) {
            foreach ($request->input('restaurants') as $r) {
                if (is_array($r) && isset($r['lat'], $r['lng'])) {
                    $restaurantCoords[] = ['lat' => (float) $r['lat'], 'lng' => (float) $r['lng']];
                } elseif (is_array($r) && isset($r['latitude'], $r['longitude'])) {
                    $restaurantCoords[] = ['lat' => (float) $r['latitude'], 'lng' => (float) $r['longitude']];
                }
            }
        }

        // 2. Legacy single restaurant_id fallback
        if (empty($restaurantCoords) && $request->filled('restaurant_id')) {
            $r = \App\Models\Restaurant::find($request->input('restaurant_id'));
            if ($r && !is_null($r->latitude) && !is_null($r->longitude)) {
                $restaurantCoords[] = [
                    'id' => $r->id,
                    'lat' => (float) $r->latitude,
                    'lng' => (float) $r->longitude,
                ];
            }
        }

        // 3. Legacy restaurant_lat & restaurant_lng fallback
        if (empty($restaurantCoords) && $request->filled('restaurant_lat') && $request->filled('restaurant_lng')) {
            $restaurantCoords[] = [
                'lat' => (float) $request->input('restaurant_lat'),
                'lng' => (float) $request->input('restaurant_lng'),
            ];
        }

        $restaurantCount = max(1, count($restaurantCoords));
        $maxDistance = 2.0;

        if (!empty($restaurantCoords)) {
            $distances = [];
            foreach ($restaurantCoords as $coords) {
                $distances[] = $this->haversineDistance($coords['lat'], $coords['lng'], $custLat, $custLng);
            }
            $maxDistance = !empty($distances) ? max($distances) : 2.0;
        }

        // 4. Find matching slab for maximum distance
        $slab = DistanceSlab::where('min_distance', '<=', $maxDistance)
            ->where('max_distance', '>=', $maxDistance)
            ->first() ?? DistanceSlab::orderBy('max_distance', 'desc')->first() ?? DistanceSlab::first();

        // 5. Financial Data Calculation with Extra Stop Fee
        $baseDeliveryFee = $slab ? (float) $slab->total_fee : 10.00;
        $driverShare = $slab ? (float) $slab->driver_share : 7.00;
        $platformShare = $slab ? (float) $slab->platform_share : 3.00;

        $extraStopFee = ($restaurantCount > 1) ? ($restaurantCount - 1) * self::EXTRA_STOP_FEE : 0.00;
        $totalDeliveryFee = $baseDeliveryFee + $extraStopFee;

        return response()->json([
            'success' => true,
            'status' => true,
            'delivery_fee' => round($totalDeliveryFee, 2),
            'base_fee' => round($baseDeliveryFee, 2),
            'base_delivery_fee' => round($baseDeliveryFee, 2),
            'extra_stop_fee' => round($extraStopFee, 2),
            'farthest_distance_km' => round($maxDistance, 2),
            'distance_km' => round($maxDistance, 2),
            'restaurant_count' => $restaurantCount,
            'driver_share' => round($driverShare, 2),
            'platform_share' => round($platformShare, 2),
        ]);
    }

    /**
     * Haversine formula to calculate distance between two points in Kilometers.
     */
    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius of the earth in kilometers

        $lat1 = (float) $lat1;
        $lon1 = (float) $lon1;
        $lat2 = (float) $lat2;
        $lon2 = (float) $lon2;

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt(max(0, min(1, $a))), sqrt(max(0, min(1, 1 - $a))));

        return $earthRadius * $c;
    }
}
