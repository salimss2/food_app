<?php

namespace Modules\Orders\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Orders\Models\Cart;
use Modules\Orders\Models\CartItem;
use Modules\Orders\Http\Resources\CartResource;
use Modules\Orders\Http\Resources\CartItemResource;

class CartController extends Controller
{
    // 1. جلب محتويات السلة
    public function index(Request $request)
    {
        $user = $request->user();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        $cart->load(['items.meal.restaurant', 'items.offer.restaurant']);

        // Invalidate stale cart quotes / delivery cache
        \Illuminate\Support\Facades\Cache::forget("user_cart_delivery_{$user->id}");
        \Illuminate\Support\Facades\Cache::forget("cart_quote_{$user->id}");

        // Customer coordinates lookup
        $latitude = $request->filled('latitude') ? (float) $request->latitude : ($user->profile?->latitude !== null ? (float) $user->profile->latitude : null);
        $longitude = $request->filled('longitude') ? (float) $request->longitude : ($user->profile?->longitude !== null ? (float) $user->profile->longitude : null);

        if (($latitude === null || $longitude === null) && $user->profile?->location) {
            $parsed = \Modules\Users\Http\Controllers\ProfileController::parseCoordinates($user->profile->location);
            if ($parsed) {
                $latitude = $parsed['latitude'];
                $longitude = $parsed['longitude'];
            }
        }

        $restaurants = $cart->items->map(function ($item) {
            return $item->meal?->restaurant ?? $item->offer?->restaurant;
        })->filter()->unique('id');

        $restaurantCount = $restaurants->count();
        $maxDistance = 2.00;
        $deliveryFee = 10.00;

        if ($restaurantCount > 0) {
            $distances = [];
            foreach ($restaurants as $r) {
                $restLat = !is_null($r->latitude) ? (float) $r->latitude : null;
                $restLng = !is_null($r->longitude) ? (float) $r->longitude : null;

                if (!is_null($restLat) && !is_null($restLng) && !is_null($latitude) && !is_null($longitude)) {
                    $earthRadius = 6371;
                    $latFrom = deg2rad($latitude);
                    $lonFrom = deg2rad($longitude);
                    $latTo = deg2rad($restLat);
                    $lonTo = deg2rad($restLng);

                    $latDelta = $latTo - $latFrom;
                    $lonDelta = $lonTo - $lonFrom;

                    $a = sin($latDelta / 2) * sin($latDelta / 2) + cos($latFrom) * cos($latTo) * sin($lonDelta / 2) * sin($lonDelta / 2);
                    $c = 2 * atan2(sqrt(max(0, min(1, $a))), sqrt(max(0, min(1, 1 - $a))));
                    $distances[] = round($earthRadius * $c, 2);
                } else {
                    $distances[] = 2.00;
                }
            }

            $maxDistance = !empty($distances) ? max($distances) : 2.00;

            $slab = \App\Models\DistanceSlab::where('min_distance', '<=', $maxDistance)
                ->where('max_distance', '>=', $maxDistance)
                ->first() ?? \App\Models\DistanceSlab::orderBy('max_distance', 'desc')->first() ?? \App\Models\DistanceSlab::first();

            $baseFee = $slab ? (float) $slab->total_fee : 10.00;
            $extraStopFee = ($restaurantCount > 1) ? ($restaurantCount - 1) * 1000.00 : 0.00;
            $deliveryFee = $baseFee + $extraStopFee;
        }

        return response()->json([
            'status' => true,
            'cart' => new CartResource($cart),
            'items' => CartItemResource::collection($cart->items),
            'restaurant_count' => $restaurantCount,
            'delivery_fee' => (float) $deliveryFee,
            'delivery_distance' => (float) $maxDistance,
            'grand_total' => (float) ($cart->total + $deliveryFee),
        ]);
    }

    // 2. إضافة وجبة للسلة
    public function add(Request $request)
    {
        $request->validate([
            'meal_id' => 'nullable|required_without:offer_id|exists:meals,id',
            'offer_id' => 'nullable|required_without:meal_id|exists:offers,id',
            'quantity' => 'required|integer|min:1',
            'option_ids' => 'nullable|array',
            'option_ids.*' => 'integer|exists:meal_options,id',
            'options' => 'nullable|array',
            'customizations' => 'nullable|array',
        ]);

        $user = $request->user();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        $quantity = (int) $request->input('quantity');

        if ($request->filled('offer_id')) {
            $now = now();
            $offer = \Modules\Restaurants\Models\Offer::where('id', $request->offer_id)
                ->where(function ($query) use ($now) {
                    $query->whereNull('start_date')->orWhere('start_date', '<=', $now);
                })
                ->where(function ($query) use ($now) {
                    $query->whereNull('end_date')->orWhere('end_date', '>=', $now);
                })
                ->first();

            if (!$offer) {
                return response()->json([
                    'status' => false,
                    'message' => 'العرض غير متوفر أو منتهي الصلاحية'
                ], 422);
            }

            $price = (float) $offer->combo_price;
            $subtotal = $price * $quantity;

            /** @var CartItem|null $cartItem */
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('offer_id', $offer->id)
                ->first();

            if ($cartItem) {
                $cartItem->quantity += $quantity;
                $cartItem->subtotal += $subtotal;
                $cartItem->save();
            } else {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'meal_id' => null,
                    'offer_id' => $offer->id,
                    'customizations' => null,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal
                ]);
            }
        } else {
            $meal = \Modules\Restaurants\Models\Meal::find($request->meal_id);
            if (!$meal) {
                return response()->json([
                    'status' => false,
                    'message' => 'الوجبة غير متوفرة'
                ], 422);
            }

            $now = now();
            $hasActiveDiscount = $meal->discount_type &&
                $meal->discount_value !== null &&
                ($meal->discount_start === null || $now >= $meal->discount_start) &&
                ($meal->discount_end === null || $now <= $meal->discount_end);

            $basePrice = (float) $meal->price;

            if ($hasActiveDiscount) {
                if ($meal->discount_type === 'percentage') {
                    $discountAmount = ($meal->price * $meal->discount_value) / 100;
                    $basePrice = max(0, (float) ($meal->price - $discountAmount));
                } elseif ($meal->discount_type === 'fixed') {
                    $basePrice = max(0, (float) ($meal->price - $meal->discount_value));
                }
            }

            // --- Extract Selected Meal Options / Customizations ---
            $optionIds = [];
            if ($request->filled('option_ids') && is_array($request->input('option_ids'))) {
                $optionIds = $request->input('option_ids');
            } elseif ($request->filled('options') && is_array($request->input('options'))) {
                foreach ($request->input('options') as $opt) {
                    if (is_array($opt) && isset($opt['id'])) {
                        $optionIds[] = $opt['id'];
                    } elseif (is_numeric($opt)) {
                        $optionIds[] = $opt;
                    }
                }
            } elseif ($request->filled('customizations') && is_array($request->input('customizations'))) {
                foreach ($request->input('customizations') as $opt) {
                    if (is_array($opt) && isset($opt['id'])) {
                        $optionIds[] = $opt['id'];
                    } elseif (is_numeric($opt)) {
                        $optionIds[] = $opt;
                    }
                }
            }

            $selectedOptions = [];
            $optionsPriceSum = 0.00;

            if (!empty($optionIds)) {
                $optionsRecords = \Modules\Restaurants\Models\MealOption::whereIn('id', array_unique($optionIds))
                    ->where('meal_id', $meal->id)
                    ->get();

                foreach ($optionsRecords as $optRec) {
                    $optPrice = (float) ($optRec->additional_price ?? $optRec->price ?? 0);
                    $optName = $optRec->option_name ?? $optRec->name;
                    $optionsPriceSum += $optPrice;
                    $selectedOptions[] = [
                        'id' => $optRec->id,
                        'name' => $optName,
                        'option_name' => $optName,
                        'price' => $optPrice,
                        'additional_price' => $optPrice,
                    ];
                }
            }

            // Sort options deterministically for comparison
            usort($selectedOptions, function ($a, $b) {
                return $a['id'] <=> $b['id'];
            });

            // Calculate Unit Price and Subtotal
            // Unit Price = base_price + sum(options.additional_price)
            $unitPrice = $basePrice + $optionsPriceSum;
            $subtotal = $unitPrice * $quantity;

            // Search for existing item with exact same meal_id AND exact same customizations
            $existingCartItems = CartItem::where('cart_id', $cart->id)
                ->where('meal_id', $meal->id)
                ->get();

            $matchedCartItem = null;
            foreach ($existingCartItems as $item) {
                $itemCustomizations = $item->customizations ?? [];
                if (is_array($itemCustomizations)) {
                    usort($itemCustomizations, function ($a, $b) {
                        return ($a['id'] ?? 0) <=> ($b['id'] ?? 0);
                    });
                } else {
                    $itemCustomizations = [];
                }

                if ($itemCustomizations == $selectedOptions) {
                    $matchedCartItem = $item;
                    break;
                }
            }

            if ($matchedCartItem) {
                $matchedCartItem->quantity += $quantity;
                $matchedCartItem->subtotal += $subtotal;
                $matchedCartItem->save();
            } else {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'meal_id' => $meal->id,
                    'offer_id' => null,
                    'customizations' => !empty($selectedOptions) ? $selectedOptions : null,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal
                ]);
            }
        }

        $cart->total = CartItem::where('cart_id', $cart->id)->sum('subtotal');
        $cart->save();

        $cart->load(['items.meal.restaurant', 'items.offer.restaurant']);

        return response()->json([
            'status' => true,
            'message' => 'تمت الإضافة للسلة بنجاح! 🛒',
            'cart_total' => (float) $cart->total,
            'cart' => new CartResource($cart)
        ], 200);
    }

    // 3. تحديث كمية وجبة في السلة
    public function update(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        /** @var CartItem $cartItem */
        $cartItem = CartItem::findOrFail($id);
        $unitPrice = $cartItem->quantity > 0 ? ($cartItem->subtotal / $cartItem->quantity) : 0;

        $cartItem->quantity = (int) $request->quantity;
        $cartItem->subtotal = $unitPrice * $cartItem->quantity;
        $cartItem->save();

        /** @var Cart $cart */
        $cart = Cart::find($cartItem->cart_id);
        $cart->total = CartItem::where('cart_id', $cart->id)->sum('subtotal');
        $cart->save();

        $cart->load(['items.meal.restaurant', 'items.offer.restaurant']);

        return response()->json([
            'status' => true,
            'message' => 'تم التحديث بنجاح',
            'cart' => new CartResource($cart)
        ]);
    }

    // 4. حذف وجبة من السلة
    public function remove($id)
    {
        /** @var CartItem $cartItem */
        $cartItem = CartItem::findOrFail($id);
        $cart_id = $cartItem->cart_id;
        $cartItem->delete();

        /** @var Cart $cart */
        $cart = Cart::find($cart_id);
        $cart->total = CartItem::where('cart_id', $cart->id)->sum('subtotal');
        $cart->save();

        return response()->json(['status' => true, 'message' => 'تم الحذف']);
    }

    // 5. تفريغ السلة بالكامل
    public function clear(Request $request)
    {
        $user = $request->user();
        /** @var Cart|null $cart */
        $cart = Cart::where('user_id', $user->id)->first();

        if ($cart) {
            CartItem::where('cart_id', $cart->id)->delete();
            $cart->total = 0;
            $cart->save();
        }

        return response()->json(['status' => true, 'message' => 'تم تفريغ السلة بنجاح']);
    }
}