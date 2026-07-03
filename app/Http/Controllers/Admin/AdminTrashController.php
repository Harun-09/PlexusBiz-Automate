<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class AdminTrashController extends Controller
{
    public function index(Request $request): Response
    {
        $type = $request->query('type', 'products');
        $items = collect();

        if ($type === 'products') {
            $items = Product::onlyTrashed()->with('supplier')->paginate(15);
        } elseif ($type === 'orders') {
            $items = Order::onlyTrashed()->with('buyer')->paginate(15);
        } elseif ($type === 'users') {
            $items = User::onlyTrashed()->paginate(15);
        } elseif ($type === 'suppliers') {
            $items = Supplier::onlyTrashed()->with('user')->paginate(15);
        }

        return Inertia::render('Admin/Trash/Index', [
            'items' => $items,
            'type' => $type,
        ]);
    }

    public function restoreProduct($id): RedirectResponse
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();
        return redirect()->back()->with('success', 'Product restored successfully.');
    }

    public function restoreOrder($id): RedirectResponse
    {
        $order = Order::onlyTrashed()->findOrFail($id);
        $order->restore();
        return redirect()->back()->with('success', 'Order restored successfully.');
    }

    public function restoreUser($id): RedirectResponse
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        return redirect()->back()->with('success', 'User restored successfully.');
    }

    public function restoreSupplier($id): RedirectResponse
    {
        $supplier = Supplier::onlyTrashed()->findOrFail($id);
        $supplier->restore();
        return redirect()->back()->with('success', 'Supplier restored successfully.');
    }
}
