<?php

namespace Modules\Restaurants\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Restaurants\Models\Offer;

class CustomerOfferController extends Controller
{
    /**
     * Get all active combo offers.
     */
    public function getActiveOffers()
    {
        $now = now();
        $offers = Offer::with(['meals.category', 'restaurant'])
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

        if (ob_get_length()) {
            ob_clean();
        }
        return response()->json([
            'status' => true,
            'data' => $offers,
        ]);
    }

    /**
     * Get all active offers formatted for the customer app home screen banners.
     */
    public function index()
    {
        $now = now();
        $offers = Offer::with(['restaurant'])
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
                    'logo' => $offer->restaurant->logo ? \Illuminate\Support\Facades\Storage::url(str_contains($offer->restaurant->logo, '/') ? $offer->restaurant->logo : 'restaurants/logos/' . $offer->restaurant->logo) : asset('assets/default-logo.png'),
                ] : null,
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
}
