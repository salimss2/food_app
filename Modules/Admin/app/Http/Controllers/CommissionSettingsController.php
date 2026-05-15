<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DistanceSlab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommissionSettingsController extends Controller
{
    /**
     * Display the settings page with distance slabs.
     */
    public function index()
    {
        $slabs = DistanceSlab::orderBy('min_distance', 'asc')->get();
        return view('admin::commissions-settings', compact('slabs'));
    }

    /**
     * Store a new distance slab.
     */
    public function store(Request $request)
    {
        $this->validateSlab($request);

        DistanceSlab::create($request->all());

        return back()->with('success', 'Distance slab created successfully (تم إضافة الشريحة بنجاح)');
    }

    /**
     * Update an existing distance slab.
     */
    public function update(Request $request, $id)
    {
        $this->validateSlab($request);

        $slab = DistanceSlab::findOrFail($id);
        $slab->update($request->all());

        return back()->with('success', 'Distance slab updated successfully (تم تحديث الشريحة بنجاح)');
    }

    /**
     * Remove a distance slab.
     */
    public function destroy($id)
    {
        $slab = DistanceSlab::findOrFail($id);
        $slab->delete();

        return back()->with('success', 'Distance slab deleted successfully (تم حذف الشريحة)');
    }

    /**
     * Internal validation logic for Slabs.
     */
    protected function validateSlab(Request $request)
    {
        $request->validate([
            'min_distance' => 'required|numeric|min:0',
            'max_distance' => 'required|numeric|gt:min_distance',
            'total_fee' => 'required|numeric|min:0',
            'driver_share' => 'required|numeric|min:0',
            'platform_share' => 'required|numeric|min:0',
        ]);

        // Financial Integrity Rule: Total Fee = Driver Share + Platform Share
        $calculatedTotal = (float) $request->driver_share + (float) $request->platform_share;

        if (abs((float) $request->total_fee - $calculatedTotal) > 0.01) {
            // We use back()->withErrors() to return to the form with the error message
            $error = 'Financial mismatch: Total Fee ($' . $request->total_fee . ') must equal Driver Share + Platform Share ($' . $calculatedTotal . ').';
            abort(back()->withErrors(['total_fee' => $error])->throwResponse());
        }
    }
}
