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
        $orders = Order::with(['items.meal', 'restaurant'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'message' => 'تم جلب الطلبات بنجاح',
            'orders' => OrderResource::collection($orders),
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
        ]);

        $scheduledAt = $request->input('scheduled_at');

        Log::info("Checkout request received", [
            'user_id' => $user->id,
            'scheduled_at' => $scheduledAt,
            'has_scheduled_at_filled' => $request->filled('scheduled_at')
        ]);

        // --- 1. جلب سلة المستخدم مع عناصرها ---
        /** @var Cart|null $cart */
        $cart = Cart::where('user_id', $user->id)->first();
        $items = $cart ? CartItem::with('meal')->where('cart_id', $cart->id)->get() : collect();

        // --- 2. التحقق من أن السلة ليست فارغة ---
        if (!$cart || $items->isEmpty()) {
            return response()->json([
                'message' => 'السلة فارغة',
            ], 400);
        }

        // --- 3. المنطق المشترك: تجميع العناصر حسب المطعم ---
        $groupedItems = $items->groupBy(function ($item) {
            return $item->meal->restaurant_id;
        });

        // =====================================================================
        // CASE A: طلب مجدول (Scheduled Order)
        // =====================================================================
        // if ($request->filled('scheduled_at')) {
        //     Log::info("Processing as Scheduled Order", ['scheduled_at' => $scheduledAt]);

        //     // +++ [إضافة جديدة] تم تمرير $latitude و $longitude هنا +++
        //     $scheduledOrders = DB::transaction(function () use ($user, $cart, $groupedItems, $scheduledAt, $latitude, $longitude) {
        //         $results = [];

        //         foreach ($groupedItems as $restaurantId => $restaurantItems) {
        //             $restaurantTotal = $restaurantItems->sum('subtotal');
        //             $restaurantItemsCount = $restaurantItems->sum('quantity');

        //             // تجهيز لقطة (Snapshot) للعناصر لحفظها في JSON
        //             $itemsSnapshot = $restaurantItems->map(function ($item) {
        //                 return [
        //                     'meal_id' => $item->meal_id,
        //                     'quantity' => $item->quantity,
        //                     'subtotal' => (float) $item->subtotal,
        //                     'meal_name' => $item->meal->name ?? 'Unknown',
        //                 ];
        //             })->toArray();

        //             // إنشاء الطلب المجدول
        //             do {
        //                 $orderNumber = (string) random_int(100000, 999999);
        //             } while (\Modules\Scheduling\Models\ScheduledOrder::where('order_number', $orderNumber)->exists());

        //             Log::info("Creating ScheduledOrder entry", [
        //                 'user_id' => $user->id,
        //                 'restaurant_id' => $restaurantId,
        //                 'order_number' => $orderNumber
        //             ]);

        //             $scheduledOrder = \Modules\Scheduling\Models\ScheduledOrder::create([
        //                 'user_id' => $user->id,
        //                 'restaurant_id' => $restaurantId,
        //                 'order_number' => $orderNumber,
        //                 'items_count' => $restaurantItemsCount,
        //                 'total_amount' => $restaurantTotal,
        //                 'items_content' => $itemsSnapshot,
        //                 'scheduled_at' => $scheduledAt,
        //                 'status' => 'scheduled',
        //                 // +++ [ملاحظة] إذا كان جدول الطلبات المجدولة يحتوي أيضاً على أعمدة الموقع، أزل التعليق عن السطرين التاليين: +++
        //                 // 'latitude' => $latitude,
        //                 // 'longitude' => $longitude,
        //             ]);

        //             $results[] = $scheduledOrder;
        //         }

        //         // تفريغ السلة
        //         CartItem::where('cart_id', $cart->id)->delete();
        //         $cart->update(['total' => 0]);

        //         return $results;
        //     });

        //     Log::info("Scheduled orders created successfully", ['count' => count($scheduledOrders)]);

        //     return response()->json([
        //         'message' => 'تمت جدولة الطلبات بنجاح! ستتم معالجتها في الوقت المحدد. 📅',
        //         'scheduled_orders' => $scheduledOrders,
        //     ], 201);
        // }
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

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
                            'quantity' => $item->quantity,
                            'subtotal' => (float) $item->subtotal,
                            'meal_name' => $item->meal->name ?? 'Unknown',
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

                    // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
                    // +++ نظام التسعير الديناميكي (حساب المسافة والعمولات للطلب المجدول) +++
                    // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
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
                    // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

                    $scheduledOrder = \Modules\Scheduling\Models\ScheduledOrder::create([
                        'user_id' => $user->id,
                        'restaurant_id' => $restaurantId,
                        'order_number' => $orderNumber,
                        'items_count' => $restaurantItemsCount,
                        'total_amount' => $restaurantTotal,
                        'items_content' => $itemsSnapshot,
                        'scheduled_at' => $scheduledAt,
                        'status' => 'scheduled',

                        // الإحداثيات (قمت بإزالة التعليق عنها لأنها ضرورية)
                        'latitude' => $latitude,
                        'longitude' => $longitude,

                        // +++ إضافة العمولات والمسافة للطلب المجدول +++
                        'delivery_distance' => $deliveryDistance,
                        'delivery_fee' => $deliveryFee,
                        'driver_commission' => $driverCommission,
                        'platform_commission' => $platformCommission,
                    ]);

                    $results[] = $scheduledOrder;
                }

                // تفريغ السلة
                CartItem::where('cart_id', $cart->id)->delete();
                $cart->update(['total' => 0]);

                return $results;
            });

            Log::info("Scheduled orders created successfully", ['count' => count($scheduledOrders)]);

            return response()->json([
                'message' => 'تمت جدولة الطلبات بنجاح! ستتم معالجتها في الوقت المحدد. 📅',
                'scheduled_orders' => $scheduledOrders,
            ], 201);
        }


        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////




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
        $groupId = Str::uuid()->toString();

        // +++ [إضافة جديدة] تم تمرير $latitude و $longitude لكي يراها الكود بالداخل +++
        $createdOrders = DB::transaction(function () use ($user, $cart, $groupedItems, $paymentMethod, $groupId, $receiptPath, $latitude, $longitude) {
            $orders = [];

            // foreach ($groupedItems as $restaurantId => $restaurantItems) {
            //     $restaurantTotal = $restaurantItems->sum('subtotal');

            //     // Initialize status and payment_status based on payment method
            //     $status = 'pending_driver_acceptance';
            //     $paymentStatus = 'pending_collection';

            //     if ($paymentMethod === 'bank_transfer') {
            //         $status = 'pending_admin_approval';
            //         $paymentStatus = 'pending_verification';
            //     }

            //     // Generate unique order number
            //     do {
            //         $orderNumber = 'ORD-' . strtoupper(Str::random(10));
            //     } while (Order::where('order_number', $orderNumber)->exists());

            //     // أ. إنشاء سجل الطلب الفرعي للمطعم
            //     /** @var Order $order */
            //     $order = Order::create([
            //         'order_number' => $orderNumber,
            //         'group_id' => $groupId,
            //         'user_id' => $user->id,
            //         'restaurant_id' => $restaurantId,
            //         'driver_id' => null,
            //         'payment_method' => $paymentMethod,
            //         'total' => $restaurantTotal,
            //         'status' => $status,
            //         'payment_status' => $paymentStatus,
            //         'receipt_image' => $receiptPath,

            //         // +++ [إضافة جديدة] تجميد الإحداثيات وربطها بالطلب الحالي +++
            //         'latitude' => $latitude,
            //         'longitude' => $longitude,
            //     ]);

            //     // ب. إنشاء عنصر طلب لكل عنصر في السلة
            //     foreach ($restaurantItems as $item) {
            //         OrderItem::create([
            //             'order_id' => $order->id,
            //             'meal_id' => $item->meal_id,
            //             'quantity' => $item->quantity,
            //             'subtotal' => $item->subtotal,
            //         ]);
            //     }

            //     $orders[] = $order->load('items.meal', 'user');
            // }

            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// --- استيراد الموديلات والمعادلات المطلوبة في أعلى الـ Controller ---
            // use App\Models\DistanceSlab;
            // use App\Models\Restaurant; // تأكد من استدعاء موديل المطعم
            // use Illuminate\Support\Facades\Log;

            // ب. إنشاء عنصر طلب لكل عنصر في السلة
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

                // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
                // +++ نظام التسعير الديناميكي (حساب المسافة والعمولات) +++
                // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

                // 1. جلب موقع المطعم
                $restaurant = \App\Models\Restaurant::find($restaurantId); // افترض أن اسم الموديل Restaurant
                $restaurantLat = $restaurant ? $restaurant->latitude : null;
                $restaurantLng = $restaurant ? $restaurant->longitude : null;

                $deliveryDistance = 0;
                $deliveryFee = 0;
                $driverCommission = 0;
                $platformCommission = 0;

                // 2. حساب المسافة (Haversine Formula) إذا كانت الإحداثيات متوفرة
                if ($restaurantLat && $restaurantLng && $latitude && $longitude) {
                    $earthRadius = 6371; // نصف قطر الأرض بالكيلومتر
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

                    $deliveryDistance = round($earthRadius * $c, 2); // المسافة بالكيلومتر
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
                        // إذا كانت المسافة خارج النطاق (أكبر من أقصى مسافة في الإعدادات)
                        Log::warning("Order out of delivery range", ['distance' => $deliveryDistance]);
                        // يمكنك هنا رمي Exception أو وضع قيم افتراضية حسب سياسة عملك
                        // throw new \Exception('موقع التوصيل خارج النطاق المسموح به للمطعم.');
                    }
                } else {
                    Log::warning("Missing coordinates for distance calculation", ['restaurant' => $restaurantId]);
                }
                // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


                // أ. إنشاء سجل الطلب الفرعي للمطعم
                /** @var Order $order */
                $order = Order::create([
                    'order_number' => $orderNumber,
                    'group_id' => $groupId,
                    'user_id' => $user->id,
                    'restaurant_id' => $restaurantId,
                    'driver_id' => null,
                    'payment_method' => $paymentMethod,
                    'total' => $restaurantTotal,
                    'status' => $status,
                    'payment_status' => $paymentStatus,
                    'receipt_image' => $receiptPath,

                    // الإحداثيات
                    'latitude' => $latitude,
                    'longitude' => $longitude,

                    // +++ إضافة العمولات والمسافة للطلب +++
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
                        'quantity' => $item->quantity,
                        'subtotal' => $item->subtotal,
                    ]);
                }

                $orders[] = $order->load('items.meal', 'user');
            }




            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

            // ج. تفريغ السلة بعد إتمام الطلب
            CartItem::where('cart_id', $cart->id)->delete();
            $cart->update(['total' => 0]);

            return collect($orders);
        });

        // Fire events after transaction commit
        try {
            foreach ($createdOrders as $order) {
                // Only broadcast to restaurants/drivers if it's COD (skips admin approval)
                if ($order->payment_method === 'cod') {
                    event(new OrderBroadcasted($order));

                    // Fire NewOrderEvent to the restaurant owner's private channel.
                    // The owner listens on: private-restaurant.{owner_id}
                    $ownerId = $order->restaurant?->owner_id;
                    if ($ownerId) {
                        event(new NewOrderEvent($order, $ownerId));
                    }
                }
                // Always fire OrderCreated for internal logging or user notifications
                event(new OrderCreated($order));
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

        $orders = Order::with(['items.meal', 'restaurant', 'user'])
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
        $order = Order::with(['items.meal', 'restaurant'])
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
            'order' => new \Modules\Orders\Http\Resources\OrderResource($order->load('items.meal', 'user'))
        ]);
    }
}