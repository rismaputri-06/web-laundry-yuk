<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PickupDelivery;
use App\Models\Service;
use App\Models\User;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Admin dashboard: quick stats + latest orders + latest pickup/delivery activity.
     */
    public function dashboard()
    {
        $today = now()->toDateString();

        $stats = [
            'ordersToday' => Order::whereDate('order_date', $today)->count(),
            'revenueThisMonth' => (float) Order::whereYear('order_date', now()->year)
                ->whereMonth('order_date', now()->month)
                ->sum('total_price'),
            'pendingOrders' => Order::whereNotIn('status', ['Selesai', 'Diantar'])->count(),
            'activeCustomers' => User::where('role', 'customer')->count(),
        ];

        // Tren pendapatan 7 hari terakhir (dari yang paling lama ke hari ini)
        $revenueTrend = collect(range(6, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo);

            return [
                'day' => $date->translatedFormat('D'),
                'amount' => (float) Order::whereDate('order_date', $date->toDateString())->sum('total_price'),
            ];
        })->values();

        $recentOrders = Order::with('user')->latest()->take(6)->get();

        $customersForModal = User::where('role', 'customer')->orderBy('name')->get();

        // Gabungan aktivitas: pesanan baru masuk + perubahan status pickup/delivery
        $orderActivities = Order::with('user')->latest()->take(6)->get()->map(function ($o) {
            return [
                'type' => 'order',
                'text' => 'Pesanan baru ORD-' . str_pad($o->id, 4, '0', STR_PAD_LEFT) . ' dari ' . ($o->user->name ?? 'Pelanggan'),
                'time' => $o->created_at,
            ];
        });

        $deliveryActivities = PickupDelivery::with('order.user')->latest('updated_at')->take(6)->get()->map(function ($t) {
            return [
                'type' => 'delivery',
                'text' => 'ORD-' . str_pad($t->order->id ?? 0, 4, '0', STR_PAD_LEFT) . ' • status: ' . $t->status,
                'time' => $t->updated_at,
            ];
        });

        $activities = $orderActivities->concat($deliveryActivities)
            ->sortByDesc('time')
            ->take(6)
            ->map(fn ($a) => [
                'type' => $a['type'],
                'text' => $a['text'],
                'timeAgo' => $a['time']->diffForHumans(),
            ])
            ->values();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'revenueTrend', 'activities', 'customersForModal'));
    }

    /**
     * Pesanan: list + search/filter all orders, with a quick status-update form per row.
     */
    public function orders(Request $request)
    {
        $query = Order::with(['user', 'pickupDelivery', 'transaction']);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        $statuses = ['Menunggu', 'Diproses', 'Dicuci', 'Dikeringkan', 'Disetrika', 'Diantar', 'Selesai'];

        return view('admin.orders', compact('orders', 'statuses'));
    }

    /**
     * Detail Pesanan: lifecycle tracker, manifest layanan, info pelanggan, dan ringkasan pembayaran.
     */
    public function orderDetail(Order $order)
    {
        $order->load(['user', 'pickupDelivery', 'orderDetails.service', 'transaction.payment']);

        $statuses = ['Menunggu', 'Diproses', 'Dicuci', 'Dikeringkan', 'Disetrika', 'Diantar', 'Selesai'];

        $currentIndex = array_search($order->status, $statuses);
        $nextStatus = ($currentIndex !== false && $currentIndex < count($statuses) - 1)
            ? $statuses[$currentIndex + 1]
            : null;

        return view('admin.order-detail', compact('order', 'statuses', 'currentIndex', 'nextStatus'));
    }

    /**
     * Admin membuat pesanan baru atas nama pelanggan (dari modal di dashboard).
     */
    public function storeOrder(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:30',
            'customer_address' => 'nullable|string|max:500',
            'serviceType' => 'required|in:Cuci Lipat,Cuci Setrika,Setrika Saja',
            'weight' => 'required|numeric|min:0.5|max:30',
            'isPickupDelivery' => 'boolean',
            'isExpress' => 'boolean',
            'pickupDate' => 'required_if:isPickupDelivery,1|nullable|date',
            'pickupTime' => 'nullable|string',
            'pickupAddress' => 'required_if:isPickupDelivery,1|nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Cari pelanggan terdaftar dengan nama yang sama (case-insensitive).
        // Kalau belum ada, buat akun pelanggan baru otomatis (pelanggan walk-in),
        // sekalian simpan no. telepon & alamat kalau diisi.
        $customer = User::where('role', 'customer')
            ->whereRaw('LOWER(name) = ?', [strtolower($request->customer_name)])
            ->first();

        if (! $customer) {
            $customer = User::create([
                'name' => $request->customer_name,
                'email' => Str::slug($request->customer_name) . '-' . uniqid() . '@walkin.local',
                'password' => Str::random(16),
                'role' => 'customer',
                'phone' => $request->customer_phone,
                'address' => $request->customer_address,
            ]);
        }

        $pricePerKg = match ($request->serviceType) {
            'Cuci Setrika' => 10000,
            'Setrika Saja' => 6000,
            default => 7000, // Cuci Lipat
        };

        $isPickup = $request->boolean('isPickupDelivery');
        $isExpress = $request->boolean('isExpress');

        $baseServicePrice = round($request->weight * $pricePerKg);
        $pickupFee = $isPickup ? 5000 : 0;
        $deliveryFee = $isPickup ? 5000 : 0;
        $handlingFee = $isExpress ? 15000 : 0;
        $totalPrice = $baseServicePrice + $pickupFee + $deliveryFee + $handlingFee;

        $order = Order::create([
            'user_id' => $customer->id,
            'order_date' => now()->toDateString(),
            'weight' => $request->weight,
            'service_type' => $request->serviceType,
            'pickup_method' => $isPickup ? 'Pickup' : 'Datang Langsung',
            'status' => 'Menunggu',
            'is_express' => $isExpress,
            'notes' => $request->notes,
            'total_price' => $totalPrice,
        ]);

        if ($isPickup) {
            $order->pickupDelivery()->create([
                'address' => $request->pickupAddress,
                'status' => 'Menunggu Pickup',
                'pickup_date' => $request->pickupDate,
                'pickup_time' => $request->pickupTime,
            ]);
        }

        $message = 'Pesanan baru ORD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT) . ' berhasil dibuat.';
        if ($customer->wasRecentlyCreated) {
            $message .= " Akun pelanggan baru \"{$customer->name}\" otomatis dibuat.";
        }

        return redirect()->route('admin.dashboard')->with('status', $message);
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:Menunggu,Diproses,Dicuci,Dikeringkan,Disetrika,Diantar,Selesai',
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('status', 'Status pesanan ORD-' . str_pad((string) $order->id, 4, '0', STR_PAD_LEFT) . " diperbarui ke {$request->status}.");
    }

    public function services(Request $request)
{
    $query = Order::with(['user', 'transaction']);

    match ($request->string('filter')->toString()) {
        'proses' => $query->whereIn('status', ['Diproses', 'Dicuci', 'Dikeringkan', 'Disetrika']),
        'siap' => $query->whereIn('status', ['Diantar', 'Selesai']),
        'belum' => $query->whereHas('transaction', fn ($t) => $t->where('payment_status', 'Belum Bayar')),
        'mendesak' => $query->where('is_express', true),
        default => null,
    };

    $orders = $query->latest()->get();

    $statusCounts = [
        'Dicuci' => Order::where('status', 'Dicuci')->count(),
        'Dikeringkan' => Order::where('status', 'Dikeringkan')->count(),
        'Disetrika' => Order::where('status', 'Disetrika')->count(),
    ];

    $monthlyRevenue = (float) Order::whereYear('order_date', now()->year)
        ->whereMonth('order_date', now()->month)
        ->sum('total_price');

    $monthlyTarget = 10000000;

    $targetPercent = $monthlyTarget > 0
        ? min(100, round(($monthlyRevenue / $monthlyTarget) * 100))
        : 0;

    return view('admin.services', compact('orders', 'statusCounts', 'monthlyRevenue', 'monthlyTarget', 'targetPercent'));
}

    /**
     * Pelanggan: list all customer accounts with their order stats.
     */
    public function customers(Request $request)
{
    $query = User::where('role', 'customer')
        ->withCount('orders')
        ->withSum('orders', 'total_price');

    if ($request->filled('search')) {
        $search = $request->string('search');
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        });
    }

    $customers = $query->orderBy('name')->paginate(10)->withQueryString();

    $totalCustomers = User::where('role', 'customer')->count();

    $newThisMonth = User::where('role', 'customer')
        ->whereYear('created_at', now()->year)
        ->whereMonth('created_at', now()->month)
        ->count();

    $totalOrders = Order::count();

    return view('admin.customers', compact('customers', 'totalCustomers', 'newThisMonth', 'totalOrders'));
}

/**
 * Detail pelanggan (dipanggil via fetch/AJAX dari drawer di halaman admin.customers).
 */
public function customerDetail(User $customer)
{
    $orders = Order::where('user_id', $customer->id)->latest()->get();

    $activeOrders = $orders->whereNotIn('status', ['Selesai', 'Diantar'])->count();

    $recentOrders = $orders->take(5)->map(function ($o) {
        return [
            'id' => 'ORD-' . str_pad($o->id, 4, '0', STR_PAD_LEFT),
            'date' => $o->order_date,
            'serviceType' => $o->service_type,
            'status' => $o->status,
            'totalPrice' => (float) $o->total_price,
            'showUrl' => route('admin.orders.show', $o->id),
        ];
    });

    return response()->json([
        'name' => $customer->name,
        'email' => $customer->email,
        'phone' => $customer->phone,
        'address' => $customer->address,
        'totalOrders' => $orders->count(),
        'totalSpending' => (float) $orders->sum('total_price'),
        'activeOrders' => $activeOrders,
        'recentOrders' => $recentOrders,
    ]);
}

    /**
     * Pickup & Delivery: list all pickup/delivery tasks with a status-update form.
     */
    public function pickupDeliveries(Request $request)
{
    $query = PickupDelivery::with('order.user', 'driver');

    if ($request->filled('status')) {
        $query->where('status', $request->string('status'));
    }

    $tasks = $query->latest()->paginate(10)->withQueryString();

    $statuses = ['Menunggu Pickup', 'Dalam Perjalanan', 'Sudah Dijemput', 'Sedang Diantar', 'Selesai'];

    $activeTasks = PickupDelivery::where('status', '!=', 'Selesai')->count();

    $completedToday = PickupDelivery::where('status', 'Selesai')
        ->whereDate('updated_at', now()->toDateString())
        ->count();

    $avgMinutes = PickupDelivery::where('status', 'Selesai')
        ->whereDate('updated_at', now()->toDateString())
        ->get()
        ->avg(fn ($task) => $task->created_at->diffInMinutes($task->updated_at));

    $drivers = Driver::orderBy('name')->get();

    return view('admin.pickup-delivery', compact('tasks', 'statuses', 'activeTasks', 'completedToday', 'avgMinutes', 'drivers'));
}
}