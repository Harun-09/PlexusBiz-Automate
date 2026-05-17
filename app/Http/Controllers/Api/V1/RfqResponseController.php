<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\ECommerce\RfqResponseData;
use App\Domains\ECommerce\Enums\RfqResponseStatus;
use App\Domains\ECommerce\Enums\RfqStatus;
use App\Domains\ECommerce\Models\Rfq;
use App\Domains\ECommerce\Models\RfqResponse;
use App\Domains\ECommerce\Models\Supplier;
use App\Http\Controllers\Api\Concerns\FormatsApiResponses;
use App\Http\Controllers\Api\V1\Concerns\AppliesApiFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApiIndexRequest;
use App\Http\Resources\Api\RfqResponseResource;
use App\Repositories\ECommerce\RfqResponseRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RfqResponseController extends Controller
{
    use AppliesApiFilters;
    use FormatsApiResponses;

    public function __construct(private readonly RfqResponseRepositoryInterface $responses)
    {
    }

    public function index(ApiIndexRequest $request): JsonResponse
    {
        $query = $this->responses->query()->with(['rfq.buyer', 'supplier', 'responder']);

        if ($request->user()->hasRole('buyer') && ! $request->user()->hasRole('admin')) {
            $query->whereHas('rfq', fn (Builder $rfqQuery) => $rfqQuery->where('buyer_id', $request->user()->id));
        } elseif ($request->user()->hasRole('supplier') && ! $request->user()->hasRole('admin')) {
            $query->where('supplier_id', $request->user()->supplier?->id);
        }

        $this->applySearch($query, $request, ['message', 'currency']);
        $this->applyStatus($query, $request);
        $this->applySort($query, $request, ['created_at', 'updated_at', 'valid_until']);

        $paginator = $query->paginate($request->perPage())->withQueryString();

        return $this->paginatedResourceResponse(
            paginator: $paginator,
            resourceClass: RfqResponseResource::class,
            message: 'RFQ responses fetched successfully.',
        );
    }

    public function show(RfqResponse $rfqResponse): JsonResponse
    {
        $this->ensureResponseAccess($rfqResponse, request()->user());

        return $this->resourceResponse(
            RfqResponseResource::make($rfqResponse->load(['rfq', 'supplier', 'responder'])),
            'RFQ response details fetched successfully.',
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'rfq_id' => ['required', 'integer', 'exists:rfqs,id'],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'quoted_amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['nullable', 'string', 'size:3'],
            'min_order_quantity' => ['nullable', 'integer', 'min:1'],
            'lead_time_days' => ['nullable', 'integer', 'min:0'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:today'],
            'message' => ['nullable', 'string', 'max:5000'],
        ]);

        $rfq = Rfq::query()->findOrFail((int) $validated['rfq_id']);
        $supplier = $this->resolveSupplierForRequest($request, $rfq);

        $response = DB::transaction(function () use ($validated, $rfq, $supplier, $request): RfqResponse {
            if ($rfq->supplier_id === null) {
                $rfq->forceFill(['supplier_id' => $supplier->id])->save();
            }

            $data = RfqResponseData::fromValidated(
                validated: $validated,
                rfqId: (int) $rfq->id,
                supplierId: (int) $supplier->id,
                respondedBy: (int) $request->user()->id,
            );

            $response = $this->responses->upsertForSupplier($data);

            if ($rfq->status === RfqStatus::Open) {
                $rfq->forceFill(['status' => RfqStatus::Quoted->value])->save();
            }

            return $response;
        });

        return $this->resourceResponse(
            RfqResponseResource::make($response->load(['rfq', 'supplier', 'responder'])),
            'RFQ response submitted successfully.',
            201,
        );
    }

    public function update(Request $request, RfqResponse $rfqResponse): JsonResponse
    {
        $this->ensureSupplierEditable($request, $rfqResponse);

        $validated = $request->validate([
            'quoted_amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['nullable', 'string', 'size:3'],
            'min_order_quantity' => ['nullable', 'integer', 'min:1'],
            'lead_time_days' => ['nullable', 'integer', 'min:0'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:today'],
            'message' => ['nullable', 'string', 'max:5000'],
        ]);

        abort_if($rfqResponse->status !== RfqResponseStatus::Pending, 422, 'Only pending RFQ responses can be updated.');

        $data = RfqResponseData::fromValidated(
            validated: $validated,
            rfqId: (int) $rfqResponse->rfq_id,
            supplierId: (int) $rfqResponse->supplier_id,
            respondedBy: (int) $request->user()->id,
        );

        $response = $this->responses->upsertForSupplier($data);

        return $this->resourceResponse(
            RfqResponseResource::make($response->load(['rfq', 'supplier', 'responder'])),
            'RFQ response updated successfully.',
        );
    }

    public function accept(Request $request, RfqResponse $rfqResponse): JsonResponse
    {
        $this->ensureBuyerAccess($request, $rfqResponse);
        abort_if($rfqResponse->status !== RfqResponseStatus::Pending, 422, 'Only pending RFQ responses can be accepted.');

        DB::transaction(function () use ($rfqResponse): void {
            $this->responses->markStatus($rfqResponse, RfqResponseStatus::Accepted);
            $this->responses->rejectOtherPendingForRfq($rfqResponse);
            $rfqResponse->rfq()->update(['status' => RfqStatus::Accepted->value]);
        });

        return $this->resourceResponse(
            RfqResponseResource::make($rfqResponse->fresh()->load(['rfq', 'supplier', 'responder'])),
            'RFQ response accepted successfully.',
        );
    }

    public function reject(Request $request, RfqResponse $rfqResponse): JsonResponse
    {
        $this->ensureBuyerAccess($request, $rfqResponse);
        abort_if($rfqResponse->status !== RfqResponseStatus::Pending, 422, 'Only pending RFQ responses can be rejected.');

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

        return $this->resourceResponse(
            RfqResponseResource::make($rfqResponse->fresh()->load(['rfq', 'supplier', 'responder'])),
            'RFQ response rejected successfully.',
        );
    }

    private function resolveSupplierForRequest(Request $request, Rfq $rfq): Supplier
    {
        if ($request->user()->hasRole('admin')) {
            $supplierId = (int) $request->integer('supplier_id', $rfq->supplier_id ?? 0);
            abort_unless($supplierId > 0, 422, 'supplier_id is required for admin requests.');

            return Supplier::query()->findOrFail($supplierId);
        }

        abort_unless($request->user()->hasRole('supplier'), 403);

        $supplier = $request->user()->supplier;
        abort_unless($supplier?->isApproved(), 403);

        if ($rfq->supplier_id !== null) {
            abort_unless((int) $rfq->supplier_id === (int) $supplier->id, 403);
        }

        return $supplier;
    }

    private function ensureResponseAccess(RfqResponse $rfqResponse, $user): void
    {
        if ($user->hasRole('admin')) {
            return;
        }

        $rfqResponse->loadMissing('rfq');

        if ($user->hasRole('supplier')) {
            abort_unless((int) $rfqResponse->supplier_id === (int) $user->supplier?->id, 403);

            return;
        }

        if ($user->hasRole('buyer')) {
            abort_unless((int) $rfqResponse->rfq?->buyer_id === (int) $user->id, 403);

            return;
        }

        abort(403);
    }

    private function ensureSupplierEditable(Request $request, RfqResponse $rfqResponse): void
    {
        if ($request->user()->hasRole('admin')) {
            return;
        }

        abort_unless(
            $request->user()->hasRole('supplier')
            && (int) $rfqResponse->supplier_id === (int) $request->user()->supplier?->id,
            403,
        );
    }

    private function ensureBuyerAccess(Request $request, RfqResponse $rfqResponse): void
    {
        if ($request->user()->hasRole('admin')) {
            return;
        }

        $rfqResponse->loadMissing('rfq');

        abort_unless(
            $request->user()->hasRole('buyer')
            && (int) $rfqResponse->rfq?->buyer_id === (int) $request->user()->id,
            403,
        );
    }
}
