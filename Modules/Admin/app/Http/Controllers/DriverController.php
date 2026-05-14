<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DriverAvailability;
use Modules\Auth\Models\DriverProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controllers\HasMiddleware; // أضف هذا
use Illuminate\Routing\Controllers\Middleware;


class DriverController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_drivers', only: ['index', 'show']),
            new Middleware('permission:edit_drivers', only: ['create', 'store', 'edit', 'update', 'destroy', 'toggleAvailability']),
        ];
    }
    /**
     * Display the drivers index page.
     * Fetches users with the 'Driver' role and their driverProfile relationship.
     */
    public function index()
    {
        $baseQuery = User::role('Driver');

        $totalDrivers = (clone $baseQuery)->count();
        $onlineCount = (clone $baseQuery)->whereHas('availability', fn($q) => $q->where('is_online', 1))->count();
        $offlineCount = (clone $baseQuery)->whereHas('availability', fn($q) => $q->where('is_online', 0))->count();

        $activeCount = (clone $baseQuery)->where('status', 'Active')->count();
        $inactiveCount = (clone $baseQuery)->where('status', 'Inactive')->count();
        $blockedCount = (clone $baseQuery)->where('status', 'Blocked')->count();

        $drivers = $baseQuery->with(['driverProfile', 'availability'])
            ->latest()
            ->paginate(10);

        return view('admin::drivers', compact(
            'drivers',
            'totalDrivers',
            'onlineCount',
            'offlineCount',
            'activeCount',
            'inactiveCount',
            'blockedCount'
        ));
    }

    /**
     * Store a newly created driver.
     * a) Creates the User record.
     * b) Assigns the 'Driver' role via Spatie.
     * c) Creates the driver_profiles record linked by user_id.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8',
            'status' => 'required|in:Active,Blocked,Inactive',
            'id_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'vehicle_model' => 'nullable|string|max:100',
            'vehicle_plate' => 'nullable|string|max:50',
            'vehicle_vin' => 'nullable|string|max:50',
            'is_online' => 'nullable|boolean',
        ]);

        // a) Create User record
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'status' => $request->status,
        ]);

        // b) Assign Driver role
        $user->assignRole('Driver');

        // c) Create driver_profiles record
        DriverProfile::create([
            'user_id' => $user->id,
            'id_number' => $request->id_number,
            'address' => $request->address,
            'vehicle_model' => $request->vehicle_model,
            'vehicle_plate' => $request->vehicle_plate,
            'vehicle_vin' => $request->vehicle_vin,
        ]);

        // d) Create driver_availability record
        $isOnline = $request->has('is_online') ? 1 : 0;
        DriverAvailability::create([
            'driver_id' => $user->id,
            'is_online' => $isOnline,
            'availability' => 'idle',                      // ENUM: idle, delivering, break
            'status' => $isOnline ? 'available' : 'unavailable', // ENUM: available, unavailable
        ]);

        Log::info('[DriverController] New Driver creation: ' . $user->name . ' (ID: ' . $user->id . ', is_online: ' . $isOnline . ')');

        return redirect()->route('admin.drivers.index')
            ->with('success', 'Driver "' . $user->name . '" created successfully!');
    }

    /**
     * Update the driver — syncs data in both users and driver_profiles tables.
     */
    public function update(Request $request, $id)
    {
        $user = User::role('Driver')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required|string|max:20',
            'password' => 'nullable|string|min:8',
            'status' => 'required|in:Active,Blocked,Inactive',
            'id_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'vehicle_model' => 'nullable|string|max:100',
            'vehicle_plate' => 'nullable|string|max:50',
            'vehicle_vin' => 'nullable|string|max:50',
        ]);

        // Sync users table
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->status = $request->status;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        // Sync driver_profiles table (create if missing)
        $user->driverProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'id_number' => $request->id_number,
                'address' => $request->address,
                'vehicle_model' => $request->vehicle_model,
                'vehicle_plate' => $request->vehicle_plate,
                'vehicle_vin' => $request->vehicle_vin,
            ]
        );

        return redirect()->route('admin.drivers.index')
            ->with('success', 'Driver "' . $user->name . '" updated successfully!');
    }

    /**
     * Show full details for a single driver — used for the dedicated Driver Details page.
     */
    public function show($id)
    {
        $driver = User::role('Driver')
            ->with('driverProfile')
            ->findOrFail($id);

        return view('admin::driver-details', compact('driver'));
    }

    /**
     * Soft-delete the driver account.
     */
    public function destroy($id)
    {
        $user = User::role('Driver')->findOrFail($id);
        $user->delete();

        return redirect()->route('admin.drivers.index')
            ->with('success', 'Driver removed successfully!');
    }

    /**
     * Toggle the driver's online/offline availability.
     */
    public function toggleAvailability(Request $request, $id)
    {
        ob_clean(); // Clear any accidental output buffers or whitespace

        \Log::info('Toggling for Driver ID: ' . $id);

        try {
            $user = User::role('Driver')->findOrFail($id);

            // Find the current record to determine new status
            $availability = DriverAvailability::where('driver_id', $user->id)->first();
            $newStatus = $availability ? !$availability->is_online : true;

            // Force update or create with explicit mapping
            $availability = DriverAvailability::updateOrCreate(
                ['driver_id' => $user->id],
                [
                    'is_online' => $newStatus,
                    'availability' => 'idle',
                    'status' => 'unavailable',
                ]
            );

            if (!$availability) {
                throw new \Exception('Failed to save availability to database');
            }

            // Refresh from DB to make 100% sure we are reading the current state
            $availability->refresh();

            \Log::info('Availability updated in DB for Driver ID ' . $id . '. PK: ' . $availability->id . '. Connection: ' . $availability->getConnectionName() . '. New status: ' . ($availability->is_online ? '1' : '0'));

            return response()->json([
                'success' => true,
                'is_online' => (bool) $availability->is_online,
                'db_row_id' => $availability->id,
                'driver_id_used' => $availability->driver_id,
                'table' => $availability->getTable(),
                'connection' => $availability->getConnectionName()
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Toggle failed for Driver ID ' . $id . ': ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}