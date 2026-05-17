<?php

namespace App\Http\Controllers;

use App\DTOs\ECommerce\RfqResponseData;
use App\Domains\ECommerce\Enums\RfqResponseStatus;
use App\Domains\ECommerce\Enums\RfqStatus;
use App\Domains\ECommerce\Models\Rfq;
use App\Domains\ECommerce\Models\RfqResponse;
use App\Domains\ECommerce\Models\Supplier;
use App\Repositories\ECommerce\RfqResponseRepositoryInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RfqResponseController extends Controller
{
    public function __construct(private readonly RfqResponseRepositoryInterface $responses)
    {
    }

    public function create(Request $request, Rfq $rfq): View
    {
        $supplier = $this->resolveSupplier($request, $rfq);

        $rfq->loadMissing(['buyer', 'supplier', 'items.product']);

        $response = $this->responses->query()
            ->where('rfq_id', $rfq->id)
            ->where('supplier_id', $supplier->id)
            ->first();

        return view('commerce.rfq-responses.create', [
            'rfq' => [
                'id' => $rfq->id,
                'rfq_number' => $rfq->rfq_number,
                'status' => $rfq->status->value,
                'message' => $rfq->message,
                'needed_by' => $rfq->needed_by?->toJSON(),
                'buyer' => $rfq->buyer ? [
                    'id' => $rfq->buyer->id,
                    'name' => $rfq->buyer->name,
                    'email' => $rfq->buyer->email,
                ] : null,
                'items' => $rfq->items->map(fn ($item): array => [
                    'id' => $item->id,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'target_price' => $item->target_price,
                    'product' => $item->product ? [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'sku' => $item->product->sku,
                    ] : null,
                ])->values()->all(),
            ],
            'supplier' => [
                'id' => $supplier->id,
                'company_name' => $supplier->company_name,
            ],
            'response' => $response ? [
                'id' => $response->id,
                'quoted_amount' => $response->quoted_amount,
                'currency' => $response->currency,
                'min_order_quantity' => $response->min_order_quantity,
                'lead_time_days' => $response->lead_time_days,
                'valid_until' => $response->valid_until?->format('Y-m-d'),
                'message' => $response->message,
                'status' => $response->status->value,
            ] : null,
        ]);
    }

    public function store(Request $request, Rfq $rfq): RedirectResponse
    {
        $supplier = $this->resolveSupplier($request, $rfq);

        $validated = $request->validate([
            'quoted_amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['nullable', 'string', 'size:3'],
            'min_order_quantity' => ['nullable', 'integer', 'min:1'],
            'lead_time_days' => ['nullable', 'integer', 'min:0'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:today'],
            'message' => ['nullable', 'string', 'max:5000'],
        ]);

        DB::transaction(function () use ($validated, $rfq, $supplier, $request): void {
            if ($rfq->supplier_id === null) {
                $rfq->forceFill(['supplier_id' => $supplier->id])->save();
            }

            $data = RfqResponseData::fromValidated(
                validated: $validated,
                rfqId: (int) $rfq->id,
                supplierId: (int) $supplier->id,
                respondedBy: (int) $request->user()->id,
            );

            $this->responses->upsertForSupplier($data);

            if ($rfq->status === RfqStatus::Open) {
                $rfq->forceFill(['status' => RfqStatus::Quoted->value])->save();
            }
        });

        return redirect()
            ->route('commerce.rfq-responses.index')
            ->with('success', 'RFQ quote submitted successfully.');
    }

    public function accept(Request $request, RfqResponse $rfqResponse): RedirectResponse
    {
        $this->ensureBuyerAccess($request, $rfqResponse);
        abort_if($rfqResponse->status !== RfqResponseStatus::Pending, 422, 'Only pending quotes can be accepted.');

        DB::transaction(function () use ($rfqResponse): void {
            $this->responses->markStatus($rfqResponse, RfqResponseStatus::Accepted);
            $this->responses->rejectOtherPendingForRfq($rfqResponse);

            $rfq = $rfqResponse->rfq()->first();
            if ($rfq) {
                $rfq->forceFill(['status' => RfqStatus::Accepted->value])->save();
            }
        });

        return back()->with('success', 'RFQ quote accepted.');
    }

    public function reject(Request $request, RfqResponse $rfqResponse): RedirectResponse
    {
        $this->ensureBuyerAccess($request, $rfqResponse);
        abort_if($rfqResponse->status !== RfqResponseStatus::Pending, 422, 'Only pending quotes can be rejected.');

        DB::transaction(function () use ($rfqResponse): void {
            $this->responses->markStatus($rfqResponse, RfqResponseStatus::Rejected);

            $rfq = $rfqResponse->rfq()->first();
            if (! $rfq) {
                return;
            }

            $hasPending = $rfq->responses()
                ->where('status', RfqResponseStatus::Pending->value)
                ->exists();

            if (! $hasPending) {
                $rfq->forceFill(['status' => RfqStatus::Rejected->value])->save();
            }
        });

        return back()->with('success', 'RFQ quote rejected.');
    }

    private function resolveSupplier(Request $request, Rfq $rfq): Supplier
    {
        $user = $request->user();

        if ($user->hasRole('admin')) {
            $supplierId = (int) $request->integer('supplier_id', $rfq->supplier_id ?? 0);
            abort_unless($supplierId > 0, 422, 'supplier_id is required for admin RFQ response actions.');

            return Supplier::query()->findOrFail($supplierId);
        }

        $supplier = $user->supplier;

        abort_unless($supplier?->isApproved(), 403, 'Approved supplier access is required.');

        if ($rfq->supplier_id !== null) {
            abort_unless((int) $rfq->supplier_id === (int) $supplier->id, 403);
        }

        return $supplier;
    }

    private function ensureBuyerAccess(Request $request, RfqResponse $rfqResponse): void
    {
        $rfqResponse->loadMissing('rfq');
        $user = $request->user();

        if ($user->hasRole('admin')) {
            return;
        }

        abort_unless(
            $user->hasRole('buyer') && (int) $rfqResponse->rfq?->buyer_id === (int) $user->id,
            403,
        );
    }
}
