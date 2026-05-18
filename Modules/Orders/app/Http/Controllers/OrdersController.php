<?php

namespace Modules\Orders\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Orders\Models\Cart;
use Modules\Orders\Models\CartItem;
use Modules\Orders\Models\Order;
use Modules\Orders\Models\OrderItem;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Orders\Events\OrderCreated;
use Modules\Orders\Events\OrderBroadcasted;
use Modules\Orders\Events\NewOrderEvent;
use Modules\Orders\Events\NewOrderAvailable;
use Modules\Orders\Http\Resources\OrderResource;
use App\Models\DistanceSlab;
use App\Models\Restaurant;

class OrdersController extends Controller
{
    // =========================================================================
    // GET /api/v1/orders  —  قائمة طلبات المستخدم الحالي
    // =========================================================================
    public function index(Request $request)
    {
        $orders = Order::with(['items.meal', 'items.offer', 'restaurant', 'user.profile'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => OrderResource::collection($orders),
        ]);
    }

    // =========================================================================
    // POST /api/v1/orders  —  إنشاء طلب جديد (Checkout)
    // =========================================================================
    // public function store(Request $request)
    // {
    //     $user = $request->user();
    //     $paymentMethod = $request->input('payment_method', 'cod');

    //     // --- 1. Validation ---
    //     $request->validate([
    //         'payment_method' => ['nullable', 'string'],
    //         'scheduled_at' => ['nullable', 'date'],
    //     ]);

    //     $scheduledAt = $request->input('scheduled_at');

    //     Log::info("Checkout request received", [
    //         'user_id' => $user->id,
    //         'scheduled_at' => $scheduledAt,
    //         'has_scheduled_at_filled' => $request->filled('scheduled_at')
    //     ]);

    //     // --- 1. جلب سلة المستخدم مع عناصرها ---
    //     /** @var Cart|null $cart */
    //     $cart = Cart::where('user_id', $user->id)->first();
    //     $items = $cart ? CartItem::with('meal')->where('cart_id', $cart->id)->get() : collect();

    //     // --- 2. التحقق من أن السلة ليست فارغة ---
    //     if (!$cart || $items->isEmpty()) {
    //         return response()->json([
    //             'message' => 'السلة فارغة',
    //         ], 400);
    //     }

    //     // --- 3. المنطق المشترك: تجميع العناصر حسب المطعم ---
    //     $groupedItems = $items->groupBy(function ($item) {
    //         return $item->meal->restaurant_id;
    //     });

    //     // =====================================================================
    //     // CASE A: طلب مجدول (Scheduled Order)
    //     // =====================================================================
    //     if ($request->filled('scheduled_at')) {
    //         Log::info("Processing as Scheduled Order", ['scheduled_at' => $scheduledAt]);

    //         $scheduledOrders = DB::transaction(function () use ($user, $cart, $groupedItems, $scheduledAt) {
    //             $results = [];

    //             foreach ($groupedItems as $restaurantId => $restaurantItems) {
    //                 $restaurantTotal = $restaurantItems->sum('subtotal');
    //                 $restaurantItemsCount = $restaurantItems->sum('quantity');

    //                 // تجهيز لقطة (Snapshot) للعناصر لحفظها في JSON
    //                 $itemsSnapshot = $restaurantItems->map(function ($item) {
    //                     return [
    //                         'meal_id' => $item->meal_id,
    //                         'quantity' => $item->quantity,
    //                         'subtotal' => (float) $item->subtotal,
    //                         'meal_name' => $item->meal->name ?? 'Unknown',
    //                     ];
    //                 })->toArray();

    //                 // إنشاء الطلب المجدول
    //                 do {
    //                     $orderNumber = (string) random_int(100000, 999999);
    //                 } while (\Modules\Scheduling\Models\ScheduledOrder::where('order_number', $orderNumber)->exists());

    //                 Log::info("Creating ScheduledOrder entry", [
    //                     'user_id' => $user->id,
    //                     'restaurant_id' => $restaurantId,
    //                     'order_number' => $orderNumber
    //                 ]);

    //                 $scheduledOrder = \Modules\Scheduling\Models\ScheduledOrder::create([
    //                     'user_id' => $user->id,
    //                     'restaurant_id' => $restaurantId,
    //                     'order_number' => $orderNumber,
    //                     'items_count' => $restaurantItemsCount,
    //                     'total_amount' => $restaurantTotal,
    //                     'items_content' => $itemsSnapshot,
    //                     'scheduled_at' => $scheduledAt,
    //                     'status' => 'scheduled',
    //                 ]);

    //                 $results[] = $scheduledOrder;
    //             }

    //             // تفريغ السلة
    //             CartItem::where('cart_id', $cart->id)->delete();
    //             $cart->update(['total' => 0]);

    //             return $results;
    //         });

    //         Log::info("Scheduled orders created successfully", ['count' => count($scheduledOrders)]);

    //         return response()->json([
    //             'message' => 'تمت جدولة الطلبات بنجاح! ستتم معالجتها في الوقت المحدد. 📅',
    //             'scheduled_orders' => $scheduledOrders,
    //         ], 201);
    //     }

    //     // --- 1.2 Handle Receipt Image (if provided) ---
    //     $receiptPath = null;
    //     if ($request->hasFile('receipt_image')) {
    //         $file = $request->file('receipt_image');
    //         $filename = time() . '_' . $file->getClientOriginalName();
    //         $file->move(public_path('receipts'), $filename);
    //         $receiptPath = 'receipts/' . $filename;
    //     }

    //     // =====================================================================
    //     // CASE B: طلب فوري (Immediate Order)
    //     // =====================================================================
    //     $groupId = Str::uuid()->toString();

    //     $createdOrders = DB::transaction(function () use ($user, $cart, $groupedItems, $paymentMethod, $groupId, $receiptPath) {
    //         $orders = [];

    //         foreach ($groupedItems as $restaurantId => $restaurantItems) {
    //             $restaurantTotal = $restaurantItems->sum('subtotal');

    //             // Initialize status and payment_status based on payment method
    //             $status = 'pending_driver_acceptance';
    //             $paymentStatus = 'pending_collection';

    //             if ($paymentMethod === 'bank_transfer') {
    //                 $status = 'pending_admin_approval';
    //                 $paymentStatus = 'pending_verification';
    //             }

    //             // Generate unique order number
    //             do {
    //                 $orderNumber = 'ORD-' . strtoupper(Str::random(10));
    //             } while (Order::where('order_number', $orderNumber)->exists());

    //             // أ. إنشاء سجل الطلب الفرعي للمطعم
    //             /** @var Order $order */
    //             $order = Order::create([
    //                 'order_number' => $orderNumber,
    //                 'group_id' => $groupId,
    //                 'user_id' => $user->id,
    //                 'restaurant_id' => $restaurantId,
    //                 'driver_id' => null,
    //                 'payment_method' => $paymentMethod,
    //                 'total' => $restaurantTotal,
    //                 'status' => $status,
    //                 'payment_status' => $paymentStatus,
    //                 'receipt_image' => $receiptPath,
    //             ]);

    //             // ب. إنشاء عنصر طلب لكل عنصر في السلة
    //             foreach ($restaurantItems as $item) {
    //                 OrderItem::create([
    //                     'order_id' => $order->id,
    //                     'meal_id' => $item->meal_id,
    //                     'quantity' => $item->quantity,
    //                     'subtotal' => $item->subtotal,
    //                 ]);
    //             }

    //             $orders[] = $order->load('items.meal', 'user');
    //         }

    //         // ج. تفريغ السلة بعد إتمام الطلب
    //         CartItem::where('cart_id', $cart->id)->delete();
    //         $cart->update(['total' => 0]);

    //         return collect($orders);
    //     });

    //     // Fire events after transaction commit
    //     try {
    //         foreach ($createdOrders as $order) {
    //             // Only broadcast to restaurants/drivers if it's COD (skips admin approval)
    //             if ($order->payment_method === 'cod') {
    //                 event(new OrderBroadcasted($order));

    //                 // Fire NewOrderEvent to the restaurant owner's private channel.
    //                 // The owner listens on: private-restaurant.{owner_id}
    //                 $ownerId = $order->restaurant?->owner_id;
    //                 if ($ownerId) {
    //                     event(new NewOrderEvent($order, $ownerId));
    //                 }
    //             }
    //             // Always fire OrderCreated for internal logging or user notifications
    //             event(new OrderCreated($order));
    //         }
    //     } catch (\Exception $e) {
    //         Log::error("Broadcasting failed for order creation: " . $e->getMessage());
    //     }

    //     return response()->json([
    //         'message' => 'تم إنشاء الطلبات بنجاح! 🎉',
    //         'orders' => OrderResource::collection($createdOrders),
    //         'group_id' => $groupId,
    //     ], 201);
    // }

    // =========================================================================
    // POST /api/v1/orders  —  إنشاء طلب جديد (Checkout)
    // =========================================================================
    public function store(Request $request)
    {
        $user = $request->user();

        // +++ [إضافة جديدة] 1. التحقق من وجود الموقع في الملف الشخصي للزبون قبل أي شيء +++
        $profile = $user->profile;
        if (!$profile || is_null($profile->latitude) || is_null($profile->longitude)) {
            return response()->json([
                'status' => false,
                'message' => 'عذراً، يرجى تحديث موقعك الحالي قبل إتمام الطلب'
            ], 422);
        }

        // تجميد الإحداثيات لاستخدامها لاحقاً في الطلب
        $latitude = $profile->latitude;
        $longitude = $profile->longitude;
        // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

        $paymentMethod = $request->input('payment_method', 'cod');

        // --- 1. Validation ---
        $request->validate([
            'payment_method' => ['nullable', 'string'],
            'scheduled_at' => ['nullable', 'date'],
            'coupon_code' => ['nullable', 'string'],
            'offer_id' => ['nullable', 'integer', 'exists:offers,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'items' => ['nullable', 'array', 'min:1'],
            'items.*.meal_id' => ['required_without:items.*.offer_id', 'nullable', 'integer', 'exists:meals,id'],
            'items.*.offer_id' => ['required_without:items.*.meal_id', 'nullable', 'integer', 'exists:offers,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $scheduledAt = $request->input('scheduled_at');

        // --- [إضافة جديدة] التحقق من كود الخصم والجدولة ---
        $couponCode = $request->input('coupon_code');
        $coupon = null;

        if ($couponCode) {
            if ($request->filled('scheduled_at')) {
                return response()->json([
                    'status' => false,
                    'message' => 'عذراً، لا يمكن استخدام كود الخصم مع الطلبات المجدولة'
                ], 422);
            }

            $coupon = \Modules\Orders\Models\Coupon::where('code', $couponCode)->first();

            if (!$coupon || !$coupon->status) {
                return response()->json([
                    'status' => false,
                    'message' => 'كود الخصم غير صحيح أو غير مفعل'
                ], 422);
            }

            $today = \Carbon\Carbon::today();
            if ($coupon->expires_at && $today->greaterThan(\Carbon\Carbon::parse($coupon->expires_at)->startOfDay())) {
                return response()->json([
                    'status' => false,
                    'message' => 'عذراً، انتهت صلاحية هذا الكود'
                ], 422);
            }
        }

        Log::info("Checkout request received", [
            'user_id' => $user->id,
            'scheduled_at' => $scheduledAt,
            'has_scheduled_at_filled' => $request->filled('scheduled_at'),
            'has_items_payload' => $request->has('items')
        ]);

        $groupedItems = collect();
        $cart = null;

        if ($request->has('items') && is_array($request->input('items'))) {
            $inputItems = $request->input('items');
            $processedItems = collect();

            foreach ($inputItems as $itemData) {
                $quantity = (int) $itemData['quantity'];

                if (!empty($itemData['offer_id'])) {
                    // Fetch Combo Offer
                    $now = now();
                    $offer = \Modules\Restaurants\Models\Offer::with('meals')
                        ->where('id', $itemData['offer_id'])
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
                            'message' => "العرض ذو الرقم {$itemData['offer_id']} غير متوفر أو منتهي الصلاحية"
                        ], 422);
                    }

                    $subtotal = $offer->combo_price * $quantity;
                    $comboMeals = $offer->meals->map(function ($m) {
                        return [
                            'name' => $m->name,
                            'quantity' => (int) ($m->pivot->quantity ?? 1),
                        ];
                    })->toArray();

                    $processedItems->push((object) [
                        'meal_id' => null,
                        'offer_id' => $offer->id,
                        'restaurant_id' => $offer->restaurant_id,
                        'name' => $offer->title,
                        'type' => 'combo_offer',
                        'quantity' => $quantity,
                        'price' => (float) $offer->combo_price,
                        'subtotal' => (float) $subtotal,
                        'combo_meals' => $comboMeals,
                    ]);

                } else {
                    // Fetch Regular Meal
                    $meal = \Modules\Restaurants\Models\Meal::find($itemData['meal_id']);
                    if (!$meal) {
                        return response()->json([
                            'status' => false,
                            'message' => "الوجبة ذات الرقم {$itemData['meal_id']} غير متوفرة"
                        ], 422);
                    }

                    $now = now();
                    $hasActiveDiscount = $meal->discount_type &&
                        $meal->discount_value !== null &&
                        ($meal->discount_start === null || $now >= $meal->discount_start) &&
                        ($meal->discount_end === null || $now <= $meal->discount_end);

                    $price = (float) $meal->price;
                    $type = 'regular_meal';

                    if ($hasActiveDiscount) {
                        $type = 'discounted_meal';
                        if ($meal->discount_type === 'percentage') {
                            $discountAmount = ($meal->price * $meal->discount_value) / 100;
                            $price = max(0, (float) ($meal->price - $discountAmount));
                        } elseif ($meal->discount_type === 'fixed') {
                            $price = max(0, (float) ($meal->price - $meal->discount_value));
                        }
                    }

                    $subtotal = $price * $quantity;

                    $processedItems->push((object) [
                        'meal_id' => $meal->id,
                        'offer_id' => null,
                        'restaurant_id' => $meal->restaurant_id,
                        'name' => $meal->name,
                        'type' => $type,
                        'quantity' => $quantity,
                        'price' => $price,
                        'subtotal' => $subtotal,
                        'combo_meals' => null,
                    ]);
                }
            }

            // Group processed items by restaurant_id
            $groupedItems = $processedItems->groupBy('restaurant_id');

        } elseif ($request->filled('offer_id')) {
            // Direct single combo offer checkout logic
            $now = now();
            $offer = \Modules\Restaurants\Models\Offer::with('meals')
                ->where('id', $request->input('offer_id'))
                ->where(function ($query) use ($now) {
                    $query->whereNull('start_date')
                        ->orWhere('start_date', '<=', $now);
                })
                ->where(function ($query) use ($now) {
                    $query->whereNull('end_date')
                        ->orWhere('end_date', '>=', $now);
                })
                ->first();

            if (!$offer) {
                return response()->json([
                    'status' => false,
                    'message' => 'العرض غير متوفر أو منتهي الصلاحية'
                ], 422);
            }

            $restaurantId = $offer->restaurant_id;
            $quantity = (int) $request->input('quantity', 1);
            $subtotal = $offer->combo_price * $quantity;

            $comboMeals = $offer->meals->map(function ($meal) {
                return [
                    'name' => $meal->name,
                    'quantity' => (int) ($meal->pivot->quantity ?? 1),
                ];
            })->toArray();

            $groupedItems = collect([
                $restaurantId => collect([
                    (object) [
                        'meal_id' => null,
                        'offer_id' => $offer->id,
                        'name' => $offer->title,
                        'type' => 'combo_offer',
                        'quantity' => $quantity,
                        'price' => (float) $offer->combo_price,
                        'subtotal' => (float) $subtotal,
                        'combo_meals' => $comboMeals,
                    ]
                ])
            ]);
        } else {
            // Cart-based checkout logic
            /** @var Cart|null $cart */
            $cart = Cart::where('user_id', $user->id)->first();
            $items = $cart ? CartItem::with('meal')->where('cart_id', $cart->id)->get() : collect();

            if (!$cart || $items->isEmpty()) {
                return response()->json([
                    'message' => 'السلة فارغة',
                ], 400);
            }

            $groupedItems = $items->groupBy(function ($item) {
                return $item->meal->restaurant_id;
            })->map(function ($restaurantItems) {
                return $restaurantItems->map(function ($item) {
                    $meal = $item->meal;

                    $now = now();
                    $hasActiveDiscount = $meal->discount_type &&
                        $meal->discount_value !== null &&
                        ($meal->discount_start === null || $now >= $meal->discount_start) &&
                        ($meal->discount_end === null || $now <= $meal->discount_end);

                    $price = (float) $meal->price;
                    $type = 'regular_meal';

                    if ($hasActiveDiscount) {
                        $type = 'discounted_meal';
                        if ($meal->discount_type === 'percentage') {
                            $discountAmount = ($meal->price * $meal->discount_value) / 100;
                            $price = max(0, (float) ($meal->price - $discountAmount));
                        } elseif ($meal->discount_type === 'fixed') {
                            $price = max(0, (float) ($meal->price - $meal->discount_value));
                        }
                    }

                    $quantity = (int) $item->quantity;
                    $subtotal = $price * $quantity;

                    return (object) [
                        'meal_id' => $meal->id,
                        'offer_id' => null,
                        'name' => $meal->name,
                        'type' => $type,
                        'quantity' => $quantity,
                        'price' => $price,
                        'subtotal' => $subtotal,
                        'combo_meals' => null,
                    ];
                });
            });
        }

        // =====================================================================
        // CASE A: طلب مجدول (Scheduled Order)
        // =====================================================================
        if ($request->filled('scheduled_at')) {
            Log::info("Processing as Scheduled Order", ['scheduled_at' => $scheduledAt]);

            $scheduledOrders = DB::transaction(function () use ($user, $cart, $groupedItems, $scheduledAt, $latitude, $longitude) {
                $results = [];

                foreach ($groupedItems as $restaurantId => $restaurantItems) {
                    $restaurantTotal = $restaurantItems->sum('subtotal');
                    $restaurantItemsCount = $restaurantItems->sum('quantity');

                    // تجهيز لقطة (Snapshot) للعناصر لحفظها في JSON
                    $itemsSnapshot = $restaurantItems->map(function ($item) {
                        return [
                            'meal_id' => $item->meal_id,
                            'offer_id' => $item->offer_id,
                            'name' => $item->name,
                            'type' => $item->type,
                            'quantity' => $item->quantity,
                            'subtotal' => (float) $item->subtotal,
                            'combo_meals' => $item->combo_meals,
                        ];
                    })->toArray();

                    // إنشاء رقم الطلب المجدول
                    do {
                        $orderNumber = (string) random_int(100000, 999999);
                    } while (\Modules\Scheduling\Models\ScheduledOrder::where('order_number', $orderNumber)->exists());

                    Log::info("Creating ScheduledOrder entry", [
                        'user_id' => $user->id,
                        'restaurant_id' => $restaurantId,
                        'order_number' => $orderNumber
                    ]);

                    // نظام التسعير الديناميكي
                    $restaurant = \App\Models\Restaurant::find($restaurantId);
                    $restaurantLat = $restaurant ? $restaurant->latitude : null;
                    $restaurantLng = $restaurant ? $restaurant->longitude : null;

                    $deliveryDistance = 0;
                    $deliveryFee = 0;
                    $driverCommission = 0;
                    $platformCommission = 0;

                    if ($restaurantLat && $restaurantLng && $latitude && $longitude) {
                        $earthRadius = 6371;
                        $latFrom = deg2rad((float) $latitude);
                        $lonFrom = deg2rad((float) $longitude);
                        $latTo = deg2rad((float) $restaurantLat);
                        $lonTo = deg2rad((float) $restaurantLng);

                        $latDelta = $latTo - $latFrom;
                        $lonDelta = $lonTo - $lonFrom;

                        $a = sin($latDelta / 2) * sin($latDelta / 2) + cos($latFrom) * cos($latTo) * sin($lonDelta / 2) * sin($lonDelta / 2);
                        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                        $deliveryDistance = round($earthRadius * $c, 2);

                        $slab = \App\Models\DistanceSlab::where('min_distance', '<=', $deliveryDistance)
                            ->where('max_distance', '>=', $deliveryDistance)
                            ->first();

                        if ($slab) {
                            $deliveryFee = $slab->total_fee;
                            $driverCommission = $slab->driver_share;
                            $platformCommission = $slab->platform_share;
                        } else {
                            Log::warning("Scheduled Order out of delivery range", ['distance' => $deliveryDistance]);
                        }
                    }

                    $scheduledOrder = \Modules\Scheduling\Models\ScheduledOrder::create([
                        'user_id' => $user->id,
                        'restaurant_id' => $restaurantId,
                        'order_number' => $orderNumber,
                        'items_count' => $restaurantItemsCount,
                        'total_amount' => $restaurantTotal,
                        'items_content' => $itemsSnapshot,
                        'scheduled_at' => $scheduledAt,
                        'status' => 'scheduled',
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'delivery_distance' => $deliveryDistance,
                        'delivery_fee' => $deliveryFee,
                        'driver_commission' => $driverCommission,
                        'platform_commission' => $platformCommission,
                    ]);

                    $results[] = $scheduledOrder;
                }

                // تفريغ السلة إذا كانت موجودة
                if ($cart) {
                    CartItem::where('cart_id', $cart->id)->delete();
                    $cart->update(['total' => 0]);
                }

                return $results;
            });

            Log::info("Scheduled orders created successfully", ['count' => count($scheduledOrders)]);

            return response()->json([
                'message' => 'تمت جدولة الطلبات بنجاح! ستتم معالجتها في الوقت المحدد. 📅',
                'scheduled_orders' => $scheduledOrders,
            ], 201);
        }

        // --- 1.2 Handle Receipt Image (if provided) ---
        $receiptPath = null;
        if ($request->hasFile('receipt_image')) {
            $file = $request->file('receipt_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('receipts'), $filename);
            $receiptPath = 'receipts/' . $filename;
        }

        // =====================================================================
        // CASE B: طلب فوري (Immediate Order)
        // =====================================================================

        // Calculate the overall cart subtotal across all restaurants/items
        $cartSubtotal = 0.00;
        foreach ($groupedItems as $restaurantId => $restaurantItems) {
            $cartSubtotal += $restaurantItems->sum('subtotal');
        }

        $totalDiscount = 0.00;
        if ($coupon) {
            if ($coupon->type === 'percent') {
                $totalDiscount = ($cartSubtotal * (float) $coupon->discount) / 100;
            } elseif ($coupon->type === 'fixed') {
                $totalDiscount = (float) $coupon->discount;
            }

            // Cap total discount at cart subtotal
            if ($totalDiscount > $cartSubtotal) {
                $totalDiscount = $cartSubtotal;
            }
        }

        $groupId = Str::uuid()->toString();

        $createdOrders = DB::transaction(function () use ($user, $cart, $groupedItems, $paymentMethod, $groupId, $receiptPath, $latitude, $longitude, $coupon, $totalDiscount, $cartSubtotal) {
            $orders = [];

            foreach ($groupedItems as $restaurantId => $restaurantItems) {
                $restaurantTotal = $restaurantItems->sum('subtotal');

                // Initialize status and payment_status based on payment method
                $status = 'pending_driver_acceptance';
                $paymentStatus = 'pending_collection';

                if ($paymentMethod === 'bank_transfer') {
                    $status = 'pending_admin_approval';
                    $paymentStatus = 'pending_verification';
                }

                // Generate unique order number
                do {
                    $orderNumber = 'ORD-' . strtoupper(Str::random(10));
                } while (Order::where('order_number', $orderNumber)->exists());

                // 1. جلب موقع المطعم
                $restaurant = \App\Models\Restaurant::find($restaurantId);
                $restaurantLat = $restaurant ? $restaurant->latitude : null;
                $restaurantLng = $restaurant ? $restaurant->longitude : null;

                $deliveryDistance = 0;
                $deliveryFee = 0;
                $driverCommission = 0;
                $platformCommission = 0;

                // 2. حساب المسافة (Haversine Formula) إذا كانت الإحداثيات متوفرة
                if ($restaurantLat && $restaurantLng && $latitude && $longitude) {
                    $earthRadius = 6371;
                    $latFrom = deg2rad((float) $latitude);
                    $lonFrom = deg2rad((float) $longitude);
                    $latTo = deg2rad((float) $restaurantLat);
                    $lonTo = deg2rad((float) $restaurantLng);

                    $latDelta = $latTo - $latFrom;
                    $lonDelta = $lonTo - $lonFrom;

                    $a = sin($latDelta / 2) * sin($latDelta / 2) +
                        cos($latFrom) * cos($latTo) *
                        sin($lonDelta / 2) * sin($lonDelta / 2);
                    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

                    $deliveryDistance = round($earthRadius * $c, 2);
                    Log::info("Distance calculated", ['order' => $orderNumber, 'distance' => $deliveryDistance]);

                    // 3. جلب الشريحة المالية المطابقة للمسافة
                    $slab = \App\Models\DistanceSlab::where('min_distance', '<=', $deliveryDistance)
                        ->where('max_distance', '>=', $deliveryDistance)
                        ->first();

                    if ($slab) {
                        $deliveryFee = $slab->total_fee;
                        $driverCommission = $slab->driver_share;
                        $platformCommission = $slab->platform_share;
                    } else {
                        Log::warning("Order out of delivery range", ['distance' => $deliveryDistance]);
                    }
                } else {
                    Log::warning("Missing coordinates for distance calculation", ['restaurant' => $restaurantId]);
                }

                $orderDiscount = 0.00;
                if ($totalDiscount > 0 && $cartSubtotal > 0) {
                    // Proportionally distribute the discount
                    $orderDiscount = ($restaurantTotal / $cartSubtotal) * $totalDiscount;
                    $orderDiscount = round($orderDiscount, 2);
                }

                $finalTotal = max(0.00, $restaurantTotal - $orderDiscount);

                // أ. إنشاء سجل الطلب الفرعي للمطعم
                /** @var Order $order */
                $order = Order::create([
                    'order_number' => $orderNumber,
                    'group_id' => $groupId,
                    'user_id' => $user->id,
                    'restaurant_id' => $restaurantId,
                    'driver_id' => null,
                    'payment_method' => $paymentMethod,
                    'coupon_code' => $coupon ? $coupon->code : null,
                    'discount_amount' => $orderDiscount,
                    'total' => $finalTotal,
                    'total_price' => $finalTotal,
                    'status' => $status,
                    'payment_status' => $paymentStatus,
                    'receipt_image' => $receiptPath,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'delivery_distance' => $deliveryDistance,
                    'delivery_fee' => $deliveryFee,
                    'driver_commission' => $driverCommission,
                    'platform_commission' => $platformCommission,
                ]);

                // ب. إنشاء عنصر طلب لكل عنصر في السلة
                foreach ($restaurantItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'meal_id' => $item->meal_id,
                        'offer_id' => $item->offer_id,
                        'type' => $item->type,
                        'combo_meals' => $item->combo_meals,
                        'name' => $item->name,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'subtotal' => $item->subtotal,
                    ]);
                }

                $orders[] = $order->load('items.meal', 'user', 'restaurant.owner');
            }

            // ج. تفريغ السلة بعد إتمام الطلب إذا كانت موجودة
            if ($cart) {
                CartItem::where('cart_id', $cart->id)->delete();
                $cart->update(['total' => 0]);
            }

            return collect($orders);
        });

        // Fire events after transaction commit
        try {
            Log::info("Order Creation: Starting event loop for " . count($createdOrders) . " orders.");
            foreach ($createdOrders as $order) {
                // Only broadcast to restaurants/drivers if it's COD (skips admin approval)
                if ($order->payment_method === 'cod') {
                    Log::info("Order Creation: Dispatching OrderBroadcasted for Order #{$order->id} (COD)");
                    event(new OrderBroadcasted($order));

                    // Fire NewOrderEvent to the restaurant owner's private channel.
                    $ownerId = $order->restaurant?->owner_id;
                    if ($ownerId) {
                        event(new NewOrderEvent($order, $ownerId));
                    }
                }

                Log::info("Order Creation: Dispatching OrderCreated for Order #{$order->id}");
                event(new OrderCreated($order));

                // --- Task 2: Firebase Push Notifications (FCM) ---
                $owner = $order->restaurant?->owner;
                $token = $owner?->fcm_token;
                Log::info('FCM Token Check:', ['token' => $token, 'order_id' => $order->id]);

                if ($token) {
                    Log::info("Order Creation: Sending FCM to Owner #{$owner->id} for Order #{$order->id}");
                    try {
                        $fcmStatus = app(\App\Services\FcmService::class)->sendNotification(
                            $token,
                            "طلب جديد! 🍔",
                            "لديك طلب جديد بقيمة {$order->total} ريال.",
                            ['type' => 'new_order', 'order_id' => (string) $order->id]
                        );

                        if ($fcmStatus) {
                            Log::info("Order Creation: FCM sent successfully to Order #{$order->id}");
                        } else {
                            Log::warning("Order Creation: FCM sendNotification returned false for Order #{$order->id}");
                        }
                    } catch (\Exception $e) {
                        Log::error("FCM failed for order {$order->id}: " . $e->getMessage());
                    }
                } else {
                    Log::warning("Order Creation: Missing FCM token or owner for Order #{$order->id}", [
                        'owner_exists' => (bool) $owner,
                        'token_exists' => false
                    ]);
                }

                // --- Task: Driver Notifications (Broadcast + FCM) ---
                Log::info("Order Creation: Dispatching NewOrderAvailable for Order #{$order->id}");
                event(new NewOrderAvailable($order));

                // Find available drivers
                $availableDrivers = \App\Models\User::role('driver')
                    ->whereHas('availability', function ($q) {
                        $q->where('is_online', true)->where('availability', 'idle');
                    })
                    ->whereNotNull('fcm_token')
                    ->get();

                $driverTokens = $availableDrivers->pluck('fcm_token')->toArray();

                if (!empty($driverTokens)) {
                    Log::info("Order Creation: Sending FCM to " . count($driverTokens) . " available drivers for Order #{$order->id}");
                    try {
                        app(\App\Services\FcmService::class)->sendToMultipleDevices(
                            $driverTokens,
                            "طلب جديد متاح! 🚚",
                            "لديك طلب جديد سارع في قبوله.",
                            ['type' => 'available_order', 'order_id' => (string) $order->id]
                        );
                    } catch (\Exception $e) {
                        Log::error("Driver FCM failed for order {$order->id}: " . $e->getMessage());
                    }
                } else {
                    Log::info("Order Creation: No available drivers found for Order #{$order->id}");
                }

                // Notify Admin about the new order placement for the Alerts Inbox
                try {
                    $admin = \App\Models\User::role('Admin')->first();
                    if ($admin) {
                        $admin->notify(new \App\Notifications\NewOrderAdminNotification($order));
                        Log::info("Order Creation: Admin notified of Order #{$order->id}");
                    }
                } catch (\Exception $e) {
                    Log::error("Admin notification failed for order {$order->id}: " . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error("Broadcasting failed for order creation: " . $e->getMessage());
        }

        return response()->json([
            'message' => 'تم إنشاء الطلبات بنجاح! 🎉',
            'orders' => OrderResource::collection($createdOrders),
            'group_id' => $groupId,

            // +++ [إضافة جديدة] إرجاع الإحداثيات في الاستجابة للتأكيد +++
            'latitude' => $latitude,
            'longitude' => $longitude,
        ], 201);
    }
    // =========================================================================
    // GET /api/v1/orders/{id}  —  تفاصيل طلب واحد
    // =========================================================================
    // =========================================================================
    // GET /api/v1/restaurant/orders  —  Fetch orders for the Restaurant Owner
    // =========================================================================
    public function restaurantOrders(Request $request)
    {
        $restaurant = $request->user()->restaurant;

        if (!$restaurant) {
            return response()->json(['message' => 'هذا المستخدم لا يملك مطعماً'], 403);
        }

        // Default status is 'pending_driver_acceptance' as per DB ENUM
        $status = $request->query('status', 'pending_driver_acceptance');

        $orders = Order::with(['items.meal', 'restaurant', 'user.profile'])
            ->where('restaurant_id', $restaurant->id)
            ->where('status', $status)
            ->latest()
            ->get();

        return response()->json([
            'message' => 'تم جلب طلبات المطعم بنجاح',
            'orders' => OrderResource::collection($orders),
        ]);
    }

    public function show(Request $request, $id)
    {
        $order = Order::with(['items.meal', 'restaurant', 'user.profile'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json([
            'message' => 'تم جلب الطلبات بنجاح',
            'order' => new OrderResource($order),
        ]);
    }

    // =========================================================================
    // الدوال الأخرى (غير مستخدمة حالياً)
    // =========================================================================
    public function create()
    {
    }
    public function edit($id)
    {
    }
    public function update(Request $request, $id)
    {
    }
    public function destroy($id)
    {
    }

    // =========================================================================
    // PUT /api/v1/restaurant/orders/{id}/status  — Update Order Status by Restaurant
    // =========================================================================
    public function updateRestaurantOrderStatus(Request $request, $id)
    {
        $restaurant = $request->user()->restaurant;
        if (!$restaurant) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|string'
        ]);

        $order = \Modules\Orders\Models\Order::where('restaurant_id', $restaurant->id)->findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return response()->json([
            'message' => 'تم تحديث حالة الطلب بنجاح',
            'order' => new \Modules\Orders\Http\Resources\OrderResource($order->load('items.meal', 'user.profile'))
        ]);
    }

    /**
     * Get real-time coordinates and driver status for tracking an order.
     */
    public function track($id)
    {
        $order = Order::with(['restaurant', 'user.profile', 'driver.driverProfile'])
            ->findOrFail($id);

        $driverData = null;

        if ($order->driver_id && $order->driver) {
            $driver = $order->driver;
            // Depending on where driver coordinates are saved, we fetch them
            $profile = $driver->driverProfile;

            $driverData = [
                'name' => $driver->name,
                'phone' => $driver->phone,
                'rating' => 4.5, // Fallback default driver rating
                'lat' => $profile ? (float) $profile->latitude : null,
                'lng' => $profile ? (float) $profile->longitude : null,
            ];
        }

        return response()->json([
            'status' => true,
            'data' => [
                'id' => (int) $order->id,
                'status' => $order->status,
                'driver' => $driverData,
                // Keep the below for backward compatibility just in case
                'restaurant_lat' => (float) ($order->restaurant->latitude ?? 0),
                'restaurant_lng' => (float) ($order->restaurant->longitude ?? 0),
                'customer_lat' => (float) ($order->latitude ?? ($order->user->profile->latitude ?? 0)),
                'customer_lng' => (float) ($order->longitude ?? ($order->user->profile->longitude ?? 0)),
            ]
        ]);
    }
}