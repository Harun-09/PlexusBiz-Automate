<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;

class AdminTrashController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!$request->user()->hasRole('admin'), 403);

        $type = $request->query('type', 'users');

        $query = match($type) {
            'users' => User::onlyTrashed(),
            'products' => Product::onlyTrashed(),
            'orders' => Order::onlyTrashed(),
            default => User::onlyTrashed(),
        };

        $items = $query->paginate(20);

        return Inertia::render('Admin/Trash/Index', [
            'type' => $type,
            'items' => $items,
        ]);
    }

    public function restore(Request $request, string $type, int $id)
    {
        abort_if(!$request->user()->hasRole('admin'), 403);

        $model = $this->getModelInstance($type, $id);
        
        if ($model) {
            $model->restore();
            return redirect()->back()->with('success', 'Item restored successfully.');
        }

        return redirect()->back()->with('error', 'Item not found.');
    }

    public function forceDelete(Request $request, string $type, int $id)
    {
        abort_if(!$request->user()->hasRole('admin'), 403);

        $model = $this->getModelInstance($type, $id);
        
        if ($model) {
            $model->forceDelete();
            return redirect()->back()->with('success', 'Item permanently deleted.');
        }

        return redirect()->back()->with('error', 'Item not found.');
    }

    private function getModelInstance(string $type, int $id)
    {
        return match($type) {
            'users' => User::onlyTrashed()->find($id),
            'products' => Product::onlyTrashed()->find($id),
            'orders' => Order::onlyTrashed()->find($id),
            default => null,
        };
    }
}
