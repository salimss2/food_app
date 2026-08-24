<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DistanceSlab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeliveryCalculationController extends Controller
{
    /**
     * Calculate delivery fee based on coordinates.
     * POST /api/delivery/calculate-fee
     */
    public function calculate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'restaurant_lat' => 'required|numeric|between:-90,90',
            'restaurant_lng' => 'required|numeric|between:-180,180',
            'customer_lat' => 'required|numeric|between:-90,90',
            'customer_lng' => 'required|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        // 1. Calculate Distance using Haversine Formula
        $distance = $this->haversineDistance(
            $request->restaurant_lat,
            $request->restaurant_lng,
            $request->customer_lat,
            $request->customer_lng
        );

        // 2. Find the matching Slab
        // We look for a slab where min_distance <= calculated_distance < max_distance
        $slab = DistanceSlab::where('min_distance', '<=', $distance)
            ->where('max_distance', '>', $distance)
            ->first();

        // 3. Handle Coverage Edge Case
        if (!$slab) {
            return response()->json([
                'status' => false,
                'message' => 'Out of delivery coverage area (خارج نطاق التوصيل)',
                'distance_km' => round($distance, 2)
            ], 400);
        }

        // 4. Return Financial Data
        return response()->json([
            'status' => true,
            'distance_km' => round($distance, 2),
            'delivery_fee' => (float) $slab->total_fee,
            'driver_share' => (float) $slab->driver_share,
            'platform_share' => (float) $slab->platform_share,
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
