<?php

namespace Modules\Users\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('users::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('users::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('users::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
    }

    /**
     * Parse decimal coordinates (latitude, longitude) from text strings.
     * e.g., "N, 49.1231° E 14.5425°" or "30.0444, 31.2357"
     */
    public static function parseCoordinates(?string $locationStr): ?array
    {
        if (empty($locationStr)) {
            return null;
        }

        if (preg_match_all('/[-+]?[0-9]*\.?[0-9]+/', $locationStr, $matches)) {
            $numbers = $matches[0];
            if (count($numbers) >= 2) {
                $lat = (float) $numbers[0];
                $lng = (float) $numbers[1];

                if (preg_match('/S/i', $locationStr) && $lat > 0) {
                    $lat = -$lat;
                }
                if (preg_match('/W/i', $locationStr) && $lng > 0) {
                    $lng = -$lng;
                }

                if ($lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180) {
                    return ['latitude' => $lat, 'longitude' => $lng];
                }
            }
        }

        return null;
    }

    /**
     * تحديث موقع المستخدم (Latitude & Longitude)
     */
    public function updateLocation(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'location' => 'nullable|string',
        ]);

        $latitude = $request->filled('latitude') ? (float) $request->latitude : null;
        $longitude = $request->filled('longitude') ? (float) $request->longitude : null;
        $locationStr = $request->input('location');

        if (($latitude === null || $longitude === null) && !empty($locationStr)) {
            $parsed = self::parseCoordinates($locationStr);
            if ($parsed) {
                $latitude = $parsed['latitude'];
                $longitude = $parsed['longitude'];
            }
        }

        if ($latitude === null || $longitude === null) {
            return response()->json([
                'status' => false,
                'message' => 'يرجى تقديم إحداثيات موقع صالحة'
            ], 422);
        }

        $user = $request->user();

        $updateData = [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];

        if ($locationStr !== null) {
            $updateData['location'] = $locationStr;
        }

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $updateData
        );

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث الموقع في الملف الشخصي بنجاح',
            'data' => [
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]
        ]);
    }
}
