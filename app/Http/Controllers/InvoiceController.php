<?php

namespace App\Http\Controllers;

use App\Domains\ECommerce\Models\Invoice;
use App\Domains\ECommerce\Services\InvoicePdfService;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceController extends Controller
{
    public function __construct(
        private readonly InvoicePdfService $invoicePdfService,
    ) {
    }

    public function index(Request $request): Response
    {
        $user = $request->user();
        $query = Invoice::query()->with(['order.buyer']);

        // Role-based filtering
        if ($user->hasRole('buyer')) {
            $query->whereHas('order', function ($q) use ($user): void {
                $q->where('buyer_id', $user->id);
            });
        } elseif ($user->hasRole('supplier')) {
            $query->whereHas('order.items', function ($q) use ($user): void {
                $q->where('supplier_id', $user->supplier?->id);
            });
        }

        // Filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($oq) use ($search): void {
                      $oq->where('order_number', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Sorting
        $sort = $request->input('sort', '-issued_at');
        $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
        $field = ltrim($sort, '-');
        $query->orderBy($field, $direction);

        return Inertia::render('Invoices/Index', [
            'invoices' => $query->paginate($request->input('per_page', 15))->withQueryString(),
            'filters' => $request->only(['search', 'status', 'sort']),
        ]);
    }

    public function show(Invoice $invoice): Response
    {
        $this->authorize('view', $invoice);

        return Inertia::render('Invoices/Show', [
            'invoice' => $invoice->load(['order.buyer', 'order.items.product', 'order.items.supplier']),
        ]);
    }

    public function download(Invoice $invoice): HttpResponse
    {
        $this->authorize('view', $invoice);

        return $this->invoicePdfService->download($invoice);
    }

    public function stream(Invoice $invoice): HttpResponse
    {
        $this->authorize('view', $invoice);

        return $this->invoicePdfService->stream($invoice);
    }

    public function generate(Request $request, int $orderId): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('create', Invoice::class);

        $order = \App\Domains\ECommerce\Models\Order::findOrFail($orderId);

        // Check if invoice already exists
        $existingInvoice = Invoice::where('order_id', $orderId)->first();
        if ($existingInvoice) {
            return redirect()->route('invoices.show', $existingInvoice)
                ->with('message', 'Invoice already exists for this order.');
        }

        $invoice = $this->invoicePdfService->generateForOrder($order);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice generated successfully.');
    }
}
