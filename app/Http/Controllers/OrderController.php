<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query()->with('pickupDelivery')->latest();

        if ($request->filled('customerId')) {
            $query->where('user_id', $request->customerId);
        } elseif (auth()->check() && !auth()->user()->isAdmin()) {
            // Customers only ever see their own orders.
            $query->where('user_id', auth()->id());
        }

        $orders = $query->get()->map(fn ($order) => $this->transform($order));

        return response()->json(['orders' => $orders]);
    }

    private function transform(Order $order): array
    {
        $pickup = $order->pickupDelivery;

        return [
            'id' => 'ORD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT),
            'rawId' => $order->id,
            'serviceType' => $order->service_type,
            'weight' => (float) $order->weight,
            'totalPrice' => (float) $order->total_price,
            'status' => $order->status,
            'isExpress' => (bool) $order->is_express,
            'pickupMethod' => $order->pickup_method,
            'notes' => $order->notes,
            'orderDate' => $order->order_date,
            'createdAt' => optional($order->created_at)->toIso8601String(),
            'estimatedDelivery' => optional($order->created_at)->translatedFormat('d M Y'),
            'pickup' => $pickup ? [
                'address' => $pickup->address,
                'status' => $pickup->status,
                'pickupDate' => $pickup->pickup_date,
                'pickupTime' => $pickup->pickup_time,
            ] : null,
        ];
    }

        public function store(Request $request)
{
    $request->validate([
        'serviceType' => 'required|in:Cuci Lipat,Cuci Setrika,Setrika Saja',
        'weight' => 'required|numeric|min:1|max:30',
        'isPickupDelivery' => 'boolean',
        'isExpress' => 'boolean',
        'pickupDate' => 'required_if:isPickupDelivery,true|nullable|date',
        'pickupTime' => 'nullable|string',
        'pickupAddress' => 'required_if:isPickupDelivery,true|nullable|string',
        'instructions' => 'nullable|string',
    ]);

    $pricePerKg = match ($request->serviceType) {
        'Cuci Setrika' => 10000,
        'Setrika Saja' => 6000,
        default => 7000, // Cuci Lipat
    };

    $baseServicePrice = round($request->weight * $pricePerKg);
    $isPickup = $request->boolean('isPickupDelivery');
    $isExpress = $request->boolean('isExpress');

    $pickupFee = $isPickup ? 5000 : 0;
    $deliveryFee = $isPickup ? 5000 : 0;
    $handlingFee = $isExpress ? 15000 : 0;
    $totalPrice = $baseServicePrice + $pickupFee + $deliveryFee + $handlingFee;

    $order = Order::create([
        'user_id' => auth()->id(),
        'order_date' => now()->toDateString(),
        'weight' => $request->weight,
        'service_type' => $request->serviceType,
        'pickup_method' => $isPickup ? 'Pickup' : 'Datang Langsung',
        'status' => 'Menunggu',
        'is_express' => $isExpress,
        'notes' => $request->instructions,
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

    return response()->json([
        'message' => 'Pesanan berhasil dibuat',
        'order_id' => $order->id,
    ]);
}

}
