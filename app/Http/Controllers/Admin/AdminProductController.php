<?php

namespace App\Http\Controllers\Admin;

use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Supplier;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AdminProductController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', '');
        $supplierId = (string) $request->query('supplier', '');

        $statuses = array_map(fn (ProductStatus $s): string => $s->value, ProductStatus::cases());

        if ($status !== '' && ! in_array($status, $statuses, true)) {
            $status = '';
        }

        $query = Product::query()->with('supplier.user');

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($supplierId !== '') {
            $query->where('supplier_id', $supplierId);
        }

        $products = $query->latest()->paginate(20)->through(fn (Product $product): array => [
            'id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->name,
            'supplier' => $product->supplier?->company_name,
            'supplier_id' => $product->supplier_id,
            'base_price' => $product->base_price,
            'stock' => $product->availableStock(),
            'moq' => $product->moq,
            'status' => $product->status->value,
            'created_at' => $product->created_at?->format('Y-m-d H:i'),
        ]);

        $suppliers = Supplier::query()
            ->where('status', 'approved')
            ->orderBy('company_name')
            ->get(['id', 'company_name'])
            ->map(fn (Supplier $s): array => [
                'id' => $s->id,
                'label' => $s->company_name,
            ])
            ->all();

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'supplier' => $supplierId,
            ],
            'statuses' => $statuses,
            'suppliers' => $suppliers,
        ]);
    }

    public function create(): Response
    {
        $suppliers = Supplier::query()
            ->where('status', 'approved')
            ->orderBy('company_name')
            ->get(['id', 'company_name'])
            ->map(fn (Supplier $s): array => [
                'id' => $s->id,
                'label' => $s->company_name,
            ])
            ->all();

        return Inertia::render('Admin/Products/Create', [
            'suppliers' => $suppliers,
            'statuses' => array_map(fn (ProductStatus $s): string => $s->value, ProductStatus::cases()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'moq' => ['required', 'integer', 'min:1'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', Rule::in(array_map(fn (ProductStatus $s): string => $s->value, ProductStatus::cases()))],
        ]);

        Product::create([
            ...$validated,
            'slug' => Str::slug($validated['name']),
            'reserved_quantity' => 0,
            'published_at' => $validated['status'] === 'active' ? now() : null,
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): Response
    {
        $product->load('supplier');

        $suppliers = Supplier::query()
            ->where('status', 'approved')
            ->orderBy('company_name')
            ->get(['id', 'company_name'])
            ->map(fn (Supplier $s): array => [
                'id' => $s->id,
                'label' => $s->company_name,
            ])
            ->all();

        return Inertia::render('Admin/Products/Edit', [
            'product' => [
                'id' => $product->id,
                'supplier_id' => $product->supplier_id,
                'sku' => $product->sku,
                'name' => $product->name,
                'description' => $product->description ?? '',
                'base_price' => $product->base_price,
                'moq' => $product->moq,
                'stock_quantity' => $product->stock_quantity,
                'status' => $product->status->value,
            ],
            'suppliers' => $suppliers,
            'statuses' => array_map(fn (ProductStatus $s): string => $s->value, ProductStatus::cases()),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'sku' => ['required', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($product->id)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'moq' => ['required', 'integer', 'min:1'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', Rule::in(array_map(fn (ProductStatus $s): string => $s->value, ProductStatus::cases()))],
        ]);

        $wasActive = $product->status === ProductStatus::Active;
        $isNowActive = $validated['status'] === 'active';

        $product->update([
            ...$validated,
            'slug' => Str::slug($validated['name']),
            ...($isNowActive && ! $wasActive ? ['published_at' => now()] : []),
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
