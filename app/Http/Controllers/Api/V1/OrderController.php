<?php

namespace App\Http\Controllers\Api\V1;

use App\Domains\ECommerce\Models\Order;
use App\Http\Controllers\Api\V1\Concerns\AppliesApiFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApiIndexRequest;
use App\Http\Resources\Api\OrderResource;
use Illuminate\Database\Eloquent\Builder;

class OrderController extends Controller
{
    use AppliesApiFilters;

    public function index(ApiIndexRequest $request)
    {
        $this->authorize('viewAny', Order::class);

        $query = Order::query()->with(['buyer', 'customer', 'items']);

        if ($request->user()->hasRole('buyer')) {
            $query->where('buyer_id', $request->user()->id);
        } elseif ($request->user()->hasRole('supplier') && ! $request->user()->hasRole('admin')) {
            $supplierId = $request->user()->supplier?->id;
            $query->whereHas('items', fn (Builder $items) => $items->where('supplier_id', $supplierId));
        }

        $this->applySearch($query, $request, ['order_number']);
        $this->applyStatus($query, $request);
        $this->applySort($query, $request, ['created_at', 'updated_at', 'placed_at', 'grand_total']);

        return OrderResource::collection($query->paginate($request->perPage())->withQueryString());
    }

    public function show(Order $order): OrderResource
    {
        $this->authorize('view', $order);

        return OrderResource::make($order->load(['buyer', 'customer', 'items']));
    }
}
