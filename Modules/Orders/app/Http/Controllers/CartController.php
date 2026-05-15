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

        $items = CartItem::with(['meal.restaurant'])
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
            'meal_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'price' => 'nullable|numeric'
        ]);

        $user = $request->user();

        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        $price = $request->input('price', 1500);
        $quantity = $request->input('quantity');
        $subtotal = $price * $quantity;

        /** @var CartItem|null $cartItem */
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('meal_id', $request->meal_id)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->subtotal += $subtotal;
            $cartItem->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'meal_id' => $request->meal_id,
                'quantity' => $quantity,
                'subtotal' => $subtotal
            ]);
        }

        $cart->total = CartItem::where('cart_id', $cart->id)->sum('subtotal');
        $cart->save();

        return response()->json([
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