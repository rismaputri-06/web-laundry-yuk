<?php

namespace App\Http\Controllers;

class CustomerController extends Controller
{
    public function dashboard()
    {
        // The customer area is now the React SPA (from laundry-yuk-3), which
        // loads its own data from /api/orders and /api/profile.
        return view('customer.app');
    }

    public function newOrder()
    {
        // "New order" is a tab inside the React SPA, not a separate page.
        return view('customer.app');
    }

    public function orderDetail(\App\Models\Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        // Order detail is also a tab inside the React SPA; the SPA fetches
        // the order list (including this order) from /api/orders itself.
        return view('customer.app');
    }
}