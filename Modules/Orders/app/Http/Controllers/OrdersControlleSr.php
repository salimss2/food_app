<!-- <!-- 

namespace Modules\Orders\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Orders\Models\Order;
use Carbon\Carbon;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('orders::index');
    }

    /**
     * Display the Order Management Command Center.
     */
    public function commandCenter()
    {
        // 1. Fetch Orders for Kanban (Active)
        $orders_new = Order::where('status', 'pending')->with('user')->orderBy('created_at', 'desc')->get();
        $orders_accepted = Order::where('status', 'accepted')->with('user')->orderBy('created_at', 'desc')->get();
        $orders_preparing = Order::where('status', 'preparing')->with('user')->orderBy('created_at', 'desc')->get();

        // 2. Fetch Orders for History
        $orders_history = Order::whereIn('status', ['delivered', 'canceled'])->with('user')->orderBy('created_at', 'desc')->limit(50)->get();

        // 3. KPI Metrics
        $kpi = [
            'new_count' => Order::where('status', 'pending')->count(),
            'preparing_count' => Order::where('status', 'preparing')->count(),
            'delayed_count' => Order::whereNotIn('status', ['delivered', 'canceled'])
                                    ->where('created_at', '<', Carbon::now()->subMinutes(20))
                                    ->count(),
            'today_sales' => Order::whereDate('created_at', Carbon::today())
                                 ->where('status', 'delivered')
                                 ->sum('total'),
        ];

        return view('orders::command-center', compact(
            'orders_new', 
            'orders_accepted', 
            'orders_preparing', 
            'orders_history',
            'kpi'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('orders::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('orders::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('orders::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
} -->
<!-- 



use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Orders\Models\Cart;
use Modules\Orders\Models\CartItem;
use Modules\Orders\Models\Order;
use Modules\Orders\Models\OrderItem;

class OrdersController extends Controller
{
    // =========================================================================
    // GET /api/v1/orders  —  قائمة طلبات المستخدم الحالي
    // =========================================================================
    public function index(Request $request)
    {
        $orders = Order::with('items')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'message' => 'تم جلب الطلبات بنجاح',
            'orders'  => $orders,
        ]);
    }

    // =========================================================================
    // POST /api/v1/orders  —  إنشاء طلب جديد (Checkout)
    // =========================================================================
    public function store(Request $request)
    {
        $user = $request->user();

        // --- 1. جلب سلة المستخدم مع عناصرها ---
        /** @var Cart|null $cart */
        $cart  = Cart::where('user_id', $user->id)->first();
        $items = $cart ? CartItem::where('cart_id', $cart->id)->get() : collect();

        // --- 2. التحقق من أن السلة ليست فارغة ---
        if (!$cart || $items->isEmpty()) {
            return response()->json([
                'message' => 'السلة فارغة',
            ], 400);
        }

        // --- 3. تنفيذ المنطق داخل معاملة قاعدة بيانات ---
        $order = DB::transaction(function () use ($user, $cart, $items) {

            // أ. إنشاء سجل الطلب الرئيسي
            /** @var Order $order */
            $order = Order::create([
                'user_id' => $user->id,
                'total'   => $cart->total,
                'status'  => 'pending',
            ]);

            // ب. إنشاء عنصر طلب لكل عنصر في السلة
            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'meal_id'  => $item->meal_id,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                ]);
            }

            // ج. تفريغ السلة بعد إتمام الطلب
            CartItem::where('cart_id', $cart->id)->delete();
            $cart->total = 0;
            $cart->save();

            return $order->load('items'); // إعادة الطلب مع عناصره
        });

        // --- 4. إرجاع استجابة نجاح ---
        return response()->json([
            'message' => 'تم إنشاء الطلب بنجاح! 🎉',
            'order'   => $order,
        ], 201);
    }

    // =========================================================================
    // GET /api/v1/orders/{id}  —  تفاصيل طلب واحد
    // =========================================================================
    public function show(Request $request, $id)
    {
        $order = Order::with('items')
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json([
            'message' => 'تم جلب الطلب بنجاح',
            'order'   => $order,
        ]);
    }

    // =========================================================================
    // الدوال الأخرى (غير مستخدمة حالياً)
    // =========================================================================
    public function create() {}
    public function edit($id) {}
    public function update(Request $request, $id) {}
    public function destroy($id) {}
} --> -->
