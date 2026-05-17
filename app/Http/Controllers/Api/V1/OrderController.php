<?php

namespace App\Http\Controllers\Api\V1;

use App\Domains\ECommerce\Enums\OrderStatus;
use App\Domains\ECommerce\Enums\PaymentStatus;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Services\NumberSequenceService;
use App\Http\Controllers\Api\Concerns\FormatsApiResponses;
use App\Http\Controllers\Api\V1\Concerns\AppliesApiFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApiIndexRequest;
use App\Http\Resources\Api\OrderResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    use AppliesApiFilters;
    use FormatsApiResponses;

    public function __construct(private readonly NumberSequenceService $numbers)
    {
    }

    public function index(ApiIndexRequest $request): JsonResponse
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

        $paginator = $query->paginate($request->perPage())->withQueryString();

        return $this->paginatedResourceResponse(
            paginator: $paginator,
            resourceClass: OrderResource::class,
            message: 'Orders fetched successfully.',
        );
    }

    public function show(Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        return $this->resourceResponse(
            OrderResource::make($order->load(['buyer', 'customer', 'items'])),
            'Order details fetched successfully.',
        );
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Order::class);

        $validated = $this->validateOrder($request);
        $totals = $this->normalizeTotals($validated);
        $buyerId = $this->resolveBuyerId($request, isset($validated['buyer_id']) ? (int) $validated['buyer_id'] : null);

        $order = Order::create([
            'buyer_id' => $buyerId,
            'customer_id' => $validated['customer_id'] ?? null,
            'order_number' => trim((string) ($validated['order_number'] ?? '')) ?: $this->numbers->orderNumber(),
            'status' => $validated['status'] ?? OrderStatus::Pending->value,
            'checkout_token' => $validated['checkout_token'] ?? (string) Str::uuid(),
            'payment_method' => $validated['payment_method'] ?? null,
            'payment_status' => $validated['payment_status'] ?? PaymentStatus::Pending->value,
            'transaction_id' => $validated['transaction_id'] ?? null,
            'subtotal' => $totals['subtotal'],
            'tax_total' => $totals['tax_total'],
            'shipping_total' => $totals['shipping_total'],
            'discount_total' => $totals['discount_total'],
            'grand_total' => $totals['grand_total'],
            'currency' => strtoupper((string) ($validated['currency'] ?? config('commerce.currency', 'BDT'))),
            'placed_at' => $validated['placed_at'] ?? now(),
        ]);

        return $this->resourceResponse(
            OrderResource::make($order->load(['buyer', 'customer', 'items'])),
            'Order created successfully',
            201,
        );
    }

    public function update(Request $request, Order $order): JsonResponse
    {
        $this->authorize('update', $order);

        $validated = $this->validateOrder($request, $order);

        if ($validated === []) {
            return $this->resourceResponse(
                OrderResource::make($order->load(['buyer', 'customer', 'items'])),
                'No changes submitted',
            );
        }

        $payload = [];

        if (array_key_exists('buyer_id', $validated)) {
            $payload['buyer_id'] = $this->resolveBuyerId($request, $validated['buyer_id'] !== null ? (int) $validated['buyer_id'] : null);
        }

        foreach (['customer_id', 'status', 'checkout_token', 'payment_method', 'payment_status', 'transaction_id', 'placed_at'] as $field) {
            if (array_key_exists($field, $validated)) {
                $payload[$field] = $validated[$field];
            }
        }

        if (array_key_exists('order_number', $validated)) {
            $payload['order_number'] = trim($validated['order_number']);
        }

        if (array_key_exists('currency', $validated)) {
            $payload['currency'] = strtoupper((string) $validated['currency']);
        }

        $totals = $this->normalizeTotals($validated);

        foreach (['subtotal', 'tax_total', 'shipping_total', 'discount_total', 'grand_total'] as $field) {
            if (array_key_exists($field, $validated)) {
                $payload[$field] = $totals[$field];
            }
        }

        $order->forceFill($payload)->save();

        return $this->resourceResponse(
            OrderResource::make($order->refresh()->load(['buyer', 'customer', 'items'])),
            'Order updated successfully',
        );
    }

    public function destroy(Order $order): JsonResponse
    {
        $this->authorize('delete', $order);

        $order->delete();

        return $this->successResponse(
            data: null,
            message: 'Order deleted successfully',
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function validateOrder(Request $request, ?Order $order = null): array
    {
        $required = $order === null ? 'required' : 'sometimes';

        return $request->validate([
            'buyer_id' => [$order === null ? 'nullable' : 'sometimes', 'nullable', 'integer', 'exists:users,id'],
            'customer_id' => [$order === null ? 'nullable' : 'sometimes', 'nullable', 'integer', 'exists:customers,id'],
            'order_number' => [
                $order === null ? 'nullable' : 'sometimes',
                'nullable',
                'string',
                'max:100',
                Rule::unique('orders', 'order_number')->ignore($order?->id),
            ],
            'status' => [$required, 'string', Rule::in($this->orderStatuses())],
            'checkout_token' => [$order === null ? 'nullable' : 'sometimes', 'nullable', 'string', 'max:64'],
            'payment_method' => [$order === null ? 'nullable' : 'sometimes', 'nullable', 'string', 'max:32'],
            'payment_status' => [$required, 'string', Rule::in($this->paymentStatuses())],
            'transaction_id' => [$order === null ? 'nullable' : 'sometimes', 'nullable', 'string', 'max:64'],
            'subtotal' => [$order === null ? 'nullable' : 'sometimes', 'nullable', 'numeric', 'min:0'],
            'tax_total' => [$order === null ? 'nullable' : 'sometimes', 'nullable', 'numeric', 'min:0'],
            'shipping_total' => [$order === null ? 'nullable' : 'sometimes', 'nullable', 'numeric', 'min:0'],
            'discount_total' => [$order === null ? 'nullable' : 'sometimes', 'nullable', 'numeric', 'min:0'],
            'grand_total' => [$order === null ? 'nullable' : 'sometimes', 'nullable', 'numeric', 'min:0'],
            'currency' => [$order === null ? 'nullable' : 'sometimes', 'nullable', 'string', 'size:3'],
            'placed_at' => [$order === null ? 'nullable' : 'sometimes', 'nullable', 'date'],
        ]);
    }

    /**
     * @param array<string, mixed> $validated
     * @return array{subtotal:string,tax_total:string,shipping_total:string,discount_total:string,grand_total:string}
     */
    private function normalizeTotals(array $validated): array
    {
        $subtotal = (float) ($validated['subtotal'] ?? 0);
        $tax = (float) ($validated['tax_total'] ?? 0);
        $shipping = (float) ($validated['shipping_total'] ?? 0);
        $discount = (float) ($validated['discount_total'] ?? 0);
        $computedGrandTotal = $subtotal + $tax + $shipping - $discount;
        $grandTotal = (float) ($validated['grand_total'] ?? $computedGrandTotal);

        return [
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'tax_total' => number_format($tax, 2, '.', ''),
            'shipping_total' => number_format($shipping, 2, '.', ''),
            'discount_total' => number_format($discount, 2, '.', ''),
            'grand_total' => number_format(max($grandTotal, 0), 2, '.', ''),
        ];
    }

    private function resolveBuyerId(Request $request, ?int $buyerId = null): int
    {
        if ($request->user()->hasRole('admin')) {
            return $buyerId ?? (int) $request->user()->id;
        }

        return (int) $request->user()->id;
    }

    /**
     * @return array<int, string>
     */
    private function orderStatuses(): array
    {
        return array_map(fn (OrderStatus $status): string => $status->value, OrderStatus::cases());
    }

    /**
     * @return array<int, string>
     */
    private function paymentStatuses(): array
    {
        return array_map(fn (PaymentStatus $status): string => $status->value, PaymentStatus::cases());
    }
}
