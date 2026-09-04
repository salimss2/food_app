<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Admin\Models\AdminOffer;

class OfferController extends Controller
{
    /**
     * Get all active promotional offer banners.
     */
    public function banners()
    {
        $offers = AdminOffer::with(['restaurant', 'meal'])
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', now()->toDateString());
            })
            ->latest()
            ->get();

        $data = $offers->map(function ($offer) {
            $bannerUrl = null;
            if ($offer->banner_image) {
                if (str_starts_with($offer->banner_image, 'http://') || str_starts_with($offer->banner_image, 'https://')) {
                    $bannerUrl = $offer->banner_image;
                } else {
                    $bannerUrl = asset('storage/' . ltrim($offer->banner_image, '/'));
                }
            }

            return [
                'id'                  => (int) $offer->id,
                'title'               => $offer->title ?? '',
                'description'         => $offer->description,
                'banner_image'        => $bannerUrl,
                'type'                => $offer->type ?? 'banner',
                'click_action'        => $offer->click_action ?? 'restaurant',
                'restaurant_id'       => $offer->restaurant_id ? (int) $offer->restaurant_id : null,
                'meal_id'             => $offer->meal_id ? (int) $offer->meal_id : null,
                'original_price'      => $offer->original_price !== null ? (float) $offer->original_price : null,
                'offer_price'         => $offer->offer_price !== null ? (float) $offer->offer_price : null,
                'discount_percentage' => $offer->discount_percentage !== null ? (float) $offer->discount_percentage : null,
                'coupon_code'         => $offer->coupon_code ?? null,
                'status'              => $offer->status,
                'restaurant'          => $offer->restaurant ? [
                    'id'   => $offer->restaurant->id,
                    'name' => $offer->restaurant->name,
                    'logo' => $offer->restaurant->logo ? (str_starts_with($offer->restaurant->logo, 'http') ? $offer->restaurant->logo : asset('storage/' . ltrim($offer->restaurant->logo, '/'))) : null,
                ] : null,
                'meal'                => $offer->meal ? [
                    'id'    => $offer->meal->id,
                    'name'  => $offer->meal->name,
                    'price' => (float) $offer->meal->price,
                ] : null,
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'Active banners retrieved successfully',
            'data'    => $data,
        ], 200);
    }

    /**
     * Get active promotional offers for a specific restaurant.
     */
    public function restaurantOffers($restaurant)
    {
        $restaurantId = is_numeric($restaurant) ? (int) $restaurant : ($restaurant->id ?? null);

        $offers = AdminOffer::with(['restaurant', 'meal'])
            ->where('status', 'active')
            ->where(function ($query) use ($restaurantId) {
                $query->where('restaurant_id', $restaurantId)
                      ->orWhereNull('restaurant_id');
            })
            ->where(function ($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', now()->toDateString());
            })
            ->latest()
            ->get();

        $data = $offers->map(function ($offer) {
            $bannerUrl = null;
            if ($offer->banner_image) {
                if (str_starts_with($offer->banner_image, 'http://') || str_starts_with($offer->banner_image, 'https://')) {
                    $bannerUrl = $offer->banner_image;
                } else {
                    $bannerUrl = asset('storage/' . ltrim($offer->banner_image, '/'));
                }
            }

            return [
                'id'                  => (int) $offer->id,
                'title'               => $offer->title ?? '',
                'description'         => $offer->description,
                'banner_image'        => $bannerUrl,
                'type'                => $offer->type ?? 'banner',
                'click_action'        => $offer->click_action ?? 'restaurant',
                'restaurant_id'       => $offer->restaurant_id ? (int) $offer->restaurant_id : null,
                'meal_id'             => $offer->meal_id ? (int) $offer->meal_id : null,
                'original_price'      => $offer->original_price !== null ? (float) $offer->original_price : null,
                'offer_price'         => $offer->offer_price !== null ? (float) $offer->offer_price : null,
                'discount_percentage' => $offer->discount_percentage !== null ? (float) $offer->discount_percentage : null,
                'coupon_code'         => $offer->coupon_code ?? null,
                'status'              => $offer->status,
                'restaurant'          => $offer->restaurant ? [
                    'id'   => $offer->restaurant->id,
                    'name' => $offer->restaurant->name,
                    'logo' => $offer->restaurant->logo ? (str_starts_with($offer->restaurant->logo, 'http') ? $offer->restaurant->logo : asset('storage/' . ltrim($offer->restaurant->logo, '/'))) : null,
                ] : null,
                'meal'                => $offer->meal ? [
                    'id'    => $offer->meal->id,
                    'name'  => $offer->meal->name,
                    'price' => (float) $offer->meal->price,
                ] : null,
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'Active restaurant offers retrieved successfully',
            'data'    => $data,
        ], 200);
    }
}
