<?php

namespace Modules\Orders\Http\Controllers; // 🔥 هذا هو التعديل الأهم

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // استدعاء المتحكم الرئيسي
use Modules\Orders\Models\Cart;
use Modules\Orders\Models\CartItem;

class CartController extends Controller
{
    // 1. جلب محتويات السلة
    public function index(Request $request)
    {
        $user = $request->user();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        $items = CartItem::with(['meal.restaurant', 'offer.restaurant'])
            ->where('cart_id', $cart->id)
            ->get();

        return response()->json([
            'status' => true,
            'cart' => $cart,
            'items' => $items
        ]);
    }

    // 2. إضافة وجبة للسلة
    public function add(Request $request)
    {
        $request->validate([
            'meal_id' => 'nullable|required_without:offer_id|exists:meals,id',
            'offer_id' => 'nullable|required_without:meal_id|exists:offers,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $user = $request->user();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        $quantity = (int) $request->input('quantity');
        $price = 0.00;

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

            /** @var CartItem|null $cartItem */
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('offer_id', $offer->id)
                ->first();

            $subtotal = $price * $quantity;

            if ($cartItem) {
                $cartItem->quantity += $quantity;
                $cartItem->subtotal += $subtotal;
                $cartItem->save();
            } else {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'meal_id' => null,
                    'offer_id' => $offer->id,
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

            $price = (float) $meal->price;

            if ($hasActiveDiscount) {
                if ($meal->discount_type === 'percentage') {
                    $discountAmount = ($meal->price * $meal->discount_value) / 100;
                    $price = max(0, (float) ($meal->price - $discountAmount));
                } elseif ($meal->discount_type === 'fixed') {
                    $price = max(0, (float) ($meal->price - $meal->discount_value));
                }
            }

            /** @var CartItem|null $cartItem */
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('meal_id', $meal->id)
                ->first();

            $subtotal = $price * $quantity;

            if ($cartItem) {
                $cartItem->quantity += $quantity;
                $cartItem->subtotal += $subtotal;
                $cartItem->save();
            } else {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'meal_id' => $meal->id,
                    'offer_id' => null,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal
                ]);
            }
        }

        $cart->total = CartItem::where('cart_id', $cart->id)->sum('subtotal');
        $cart->save();

        return response()->json([
            'status' => true,
            'message' => 'تمت الإضافة للسلة بنجاح! 🛒',
            'cart_total' => $cart->total
        ], 200);
    }

    // 3. تحديث كمية وجبة في السلة
    public function update(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        /** @var CartItem $cartItem */
        $cartItem = CartItem::findOrFail($id);
        $unitPrice = $cartItem->subtotal / $cartItem->quantity;

        $cartItem->quantity = $request->quantity;
        $cartItem->subtotal = $unitPrice * $request->quantity;
        $cartItem->save();

        /** @var Cart $cart */
        $cart = Cart::find($cartItem->cart_id);
        $cart->total = CartItem::where('cart_id', $cart->id)->sum('subtotal');
        $cart->save();

        return response()->json(['message' => 'تم التحديث']);
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

        return response()->json(['message' => 'تم الحذف']);
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

        return response()->json(['message' => 'تم تفريغ السلة بنجاح']);
    }
}