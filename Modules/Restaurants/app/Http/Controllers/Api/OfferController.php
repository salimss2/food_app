<?php

namespace Modules\Restaurants\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Modules\Restaurants\Models\Offer;

class OfferController extends Controller
{
    /**
     * Display a listing of offers.
     */
    public function index(Request $request)
    {
        // Check if the request is for restaurant owner's offers
        if ($request->is('*/restaurant/offers*') || $request->is('restaurant/offers*')) {
            $user = Auth::user();
            if (!$user || !$user->restaurant) {
                ob_clean();
                return response()->json(['status' => false, 'message' => 'No restaurant found'], 404);
            }

            $offers = Offer::where('restaurant_id', $user->restaurant->id)
                ->with(['meals'])
                ->latest()
                ->get();

            ob_clean();
            return response()->json([
                'status' => true,
                'data' => $offers,
            ]);
        }

        // Public customer active offers
        $now = now();
        $offers = Offer::with(['restaurant', 'meals'])
            ->where(function ($query) use ($now) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $now);
            })
            ->latest()
            ->get();

        $formattedOffers = $offers->map(function ($offer) {
            return [
                'id' => (int) $offer->id,
                'title' => $offer->title,
                'description' => $offer->description ?? '',
                'combo_price' => (float) $offer->combo_price,
                'image' => $offer->image_url,
                'restaurant' => $offer->restaurant ? [
                    'id' => (int) $offer->restaurant->id,
                    'name' => $offer->restaurant->name,
                    'logo' => $offer->restaurant->logo ? asset('storage/' . $offer->restaurant->logo) : asset('assets/default-logo.png'),
                ] : null,
                'meals' => $offer->meals->map(function ($meal) {
                    return [
                        'id' => (int) $meal->id,
                        'name' => $meal->name,
                        'image' => $meal->image_url,
                        'quantity' => (int) ($meal->pivot->quantity ?? 1),
                    ];
                })->toArray(),
            ];
        });

        if (ob_get_length()) {
            ob_clean();
        }

        return response()->json([
            'status' => true,
            'data' => $formattedOffers,
        ]);
    }

    /**
     * Store a newly created offer.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->restaurant) {
            ob_clean();
            return response()->json(['status' => false, 'message' => 'No restaurant found'], 404);
        }

        $input = $request->all();
        if (isset($input['meals']) && is_string($input['meals'])) {
            $decoded = json_decode($input['meals'], true);
            if (is_array($decoded)) {
                $input['meals'] = $decoded;
            }
        }

        $validator = Validator::make($input, [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'combo_price' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'meals' => 'required|array',
            'meals.*.meal_id' => 'required|exists:meals,id',
            'meals.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            ob_clean();
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $offerData = [
            'title' => $input['title'],
            'description' => $input['description'] ?? null,
            'combo_price' => $input['combo_price'],
            'start_date' => $input['start_date'],
            'end_date' => $input['end_date'],
            'restaurant_id' => $user->restaurant->id,
        ];

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('restaurants/offers', $filename, 's3');
            $offerData['image'] = 'restaurants/offers/' . $filename;
        }

        $offer = DB::transaction(function () use ($offerData, $input) {
            $newOffer = Offer::create($offerData);

            $syncData = [];
            foreach ($input['meals'] as $mealItem) {
                $syncData[$mealItem['meal_id']] = ['quantity' => $mealItem['quantity']];
            }

            $newOffer->meals()->sync($syncData);

            return $newOffer;
        });

        ob_clean();
        return response()->json([
            'status' => true,
            'message' => 'Combo offer created successfully',
            'data' => $offer->load('meals'),
        ]);
    }

    /**
     * Display the specified offer.
     */
    public function show($id)
    {
        $user = Auth::user();
        if (!$user->restaurant) {
            ob_clean();
            return response()->json(['status' => false, 'message' => 'No restaurant found'], 404);
        }

        $offer = Offer::where('restaurant_id', $user->restaurant->id)
            ->with(['meals'])
            ->findOrFail($id);

        ob_clean();
        return response()->json([
            'status' => true,
            'data' => $offer,
        ]);
    }

    /**
     * Update the specified offer.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->restaurant) {
            ob_clean();
            return response()->json(['status' => false, 'message' => 'No restaurant found'], 404);
        }

        $offer = Offer::where('restaurant_id', $user->restaurant->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'combo_price' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'meals' => 'required|array',
            'meals.*.meal_id' => 'required|exists:meals,id',
            'meals.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            ob_clean();
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $offerData = $request->only(['title', 'description', 'combo_price', 'start_date', 'end_date']);

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($offer->image) {
                Storage::disk('s3')->delete($offer->image);
            }

            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('restaurants/offers', $filename, 's3');
            $offerData['image'] = 'restaurants/offers/' . $filename;
        }

        DB::transaction(function () use ($offer, $offerData, $request) {
            $offer->update($offerData);

            $syncData = [];
            foreach ($request->meals as $mealItem) {
                $syncData[$mealItem['meal_id']] = ['quantity' => $mealItem['quantity']];
            }

            $offer->meals()->sync($syncData);
        });

        ob_clean();
        return response()->json([
            'status' => true,
            'message' => 'Combo offer updated successfully',
            'data' => $offer->load('meals'),
        ]);
    }

    /**
     * Remove the specified offer.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user->restaurant) {
            ob_clean();
            return response()->json(['status' => false, 'message' => 'No restaurant found'], 404);
        }

        $offer = Offer::where('restaurant_id', $user->restaurant->id)->findOrFail($id);

        if ($offer->image) {
            Storage::disk('s3')->delete($offer->image);
        }

        $offer->delete();

        ob_clean();
        return response()->json([
            'status' => true,
            'message' => 'Combo offer deleted successfully',
        ]);
    }
}
