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
use Modules\Orders\Http\Resources\OrderResource;

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
            'orders'  => OrderResource::collection($orders),
        ]);
    }

    // =========================================================================
    // POST /api/v1/orders  —  إنشاء طلب جديد (Checkout)
    // =========================================================================
    public function store(Request $request)
    {
        $user          = $request->user();
        $paymentMethod = $request->input('payment_method', 'cod');
        
        // --- 1. Validation ---
        $request->validate([
            'payment_method' => ['nullable', 'string'],
            'scheduled_at'   => ['nullable', 'date'],
        ]);

        $scheduledAt = $request->input('scheduled_at');

        Log::info("Checkout request received", [
            'user_id'      => $user->id,
            'scheduled_at' => $scheduledAt,
            'has_scheduled_at_filled' => $request->filled('scheduled_at')
        ]);

        // --- 1. جلب سلة المستخدم مع عناصرها ---
        /** @var Cart|null $cart */
        $cart  = Cart::where('user_id', $user->id)->first();
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
        if ($request->filled('scheduled_at')) {
            Log::info("Processing as Scheduled Order", ['scheduled_at' => $scheduledAt]);

            $scheduledOrders = DB::transaction(function () use ($user, $cart, $groupedItems, $scheduledAt) {
                $results = [];

                foreach ($groupedItems as $restaurantId => $restaurantItems) {
                    $restaurantTotal = $restaurantItems->sum('subtotal');
                    $restaurantItemsCount = $restaurantItems->sum('quantity');

                    // تجهيز لقطة (Snapshot) للعناصر لحفظها في JSON
                    $itemsSnapshot = $restaurantItems->map(function ($item) {
                        return [
                            'meal_id'   => $item->meal_id,
                            'quantity'  => $item->quantity,
                            'subtotal'  => (float) $item->subtotal,
                            'meal_name' => $item->meal->name ?? 'Unknown',
                        ];
                    })->toArray();

                    // إنشاء الطلب المجدول
                    do {
                        $orderNumber = (string) random_int(100000, 999999);
                    } while (\Modules\Scheduling\Models\ScheduledOrder::where('order_number', $orderNumber)->exists());

                    Log::info("Creating ScheduledOrder entry", [
                        'user_id'       => $user->id,
                        'restaurant_id' => $restaurantId,
                        'order_number'  => $orderNumber
                    ]);

                    $scheduledOrder = \Modules\Scheduling\Models\ScheduledOrder::create([
                        'user_id'       => $user->id,
                        'restaurant_id' => $restaurantId,
                        'order_number'  => $orderNumber,
                        'items_count'   => $restaurantItemsCount,
                        'total_amount'  => $restaurantTotal,
                        'items_content' => $itemsSnapshot,
                        'scheduled_at'  => $scheduledAt,
                        'status'        => 'scheduled',
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

        $createdOrders = DB::transaction(function () use ($user, $cart, $groupedItems, $paymentMethod, $groupId, $receiptPath) {
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

                // أ. إنشاء سجل الطلب الفرعي للمطعم
                /** @var Order $order */
                $order = Order::create([
                    'order_number'   => $orderNumber,
                    'group_id'       => $groupId,
                    'user_id'        => $user->id,
                    'restaurant_id'  => $restaurantId,
                    'driver_id'      => null,
                    'payment_method' => $paymentMethod,
                    'total'          => $restaurantTotal,
                    'status'         => $status,
                    'payment_status' => $paymentStatus,
                    'receipt_image'  => $receiptPath,
                ]);

                // ب. إنشاء عنصر طلب لكل عنصر في السلة
                foreach ($restaurantItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'meal_id'  => $item->meal_id,
                        'quantity' => $item->quantity,
                        'subtotal' => $item->subtotal,
                    ]);
                }

                $orders[] = $order->load('items.meal', 'user');
            }

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
                }
                // Always fire OrderCreated for internal logging or user notifications
                event(new OrderCreated($order));
            }
        } catch (\Exception $e) {
            Log::error("Broadcasting failed for order creation: " . $e->getMessage());
        }

        return response()->json([
            'message'  => 'تم إنشاء الطلبات بنجاح! 🎉',
            'orders'   => OrderResource::collection($createdOrders),
            'group_id' => $groupId,
        ], 201);
    }

    // =========================================================================
    // GET /api/v1/orders/{id}  —  تفاصيل طلب واحد
    // =========================================================================
    public function show(Request $request, $id)
    {
        $order = Order::with(['items.meal', 'restaurant'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json([
            'message' => 'تم جلب الطلبات بنجاح',
            'order'   => new OrderResource($order),
        ]);
    }

    // =========================================================================
    // الدوال الأخرى (غير مستخدمة حالياً)
    // =========================================================================
    public function create() {}
    public function edit($id) {}
    public function update(Request $request, $id) {}
    public function destroy($id) {}
}