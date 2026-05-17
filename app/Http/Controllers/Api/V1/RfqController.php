<?php

namespace App\Http\Controllers\Api\V1;

use App\Domains\ECommerce\Models\Rfq;
use App\Http\Controllers\Api\Concerns\FormatsApiResponses;
use App\Http\Controllers\Api\V1\Concerns\AppliesApiFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApiIndexRequest;
use App\Http\Resources\Api\RfqResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RfqController extends Controller
{
    use AppliesApiFilters;
    use FormatsApiResponses;

    public function index(ApiIndexRequest $request): JsonResponse
    {
        $query = Rfq::query()
            ->with(['buyer', 'supplier'])
            ->withCount('responses');

        if ($request->user()->hasRole('buyer') && ! $request->user()->hasRole('admin')) {
            $query->where('buyer_id', $request->user()->id);
        } elseif ($request->user()->hasRole('supplier') && ! $request->user()->hasRole('admin')) {
            $supplierId = $request->user()->supplier?->id;
            $query->where('supplier_id', $supplierId);
        }

        $this->applySearch($query, $request, ['rfq_number', 'message']);
        $this->applyStatus($query, $request);
        $this->applySort($query, $request, ['created_at', 'updated_at', 'needed_by']);

        $paginator = $query->paginate($request->perPage())->withQueryString();

        return $this->paginatedResourceResponse(
            paginator: $paginator,
            resourceClass: RfqResource::class,
            message: 'RFQs fetched successfully.',
        );
    }

    public function show(Request $request, Rfq $rfq): JsonResponse
    {
        $this->ensureRfqAccess($rfq, $request->user());

        return $this->resourceResponse(
            RfqResource::make($rfq->load(['buyer', 'supplier', 'items.product'])->loadCount('responses')),
            'RFQ details fetched successfully.',
        );
    }

    private function ensureRfqAccess(Rfq $rfq, $user): void
    {
        if ($user->hasRole('admin')) {
            return;
        }

        if ($user->hasRole('buyer')) {
            abort_unless((int) $rfq->buyer_id === (int) $user->id, 403);

            return;
        }

        if ($user->hasRole('supplier')) {
            abort_unless((int) $rfq->supplier_id === (int) $user->supplier?->id, 403);

            return;
        }

        abort(403);
    }
}
