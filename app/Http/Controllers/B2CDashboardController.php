<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Domains\ECommerce\Models\Order;

class B2CDashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $customer = $request->user('b2c');
        
        $orders = Order::where('customer_id', $customer->id)->latest()->take(5)->get();

        return Inertia::render('B2C/Dashboard', [
            'customer' => $customer,
            'recentOrders' => $orders,
        ]);
    }
}
