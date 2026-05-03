<?php

namespace App\Http\Controllers\Api\V1;

use App\Domains\Support\Models\SupportTicket;
use App\Http\Controllers\Api\V1\Concerns\AppliesApiFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApiIndexRequest;
use App\Http\Resources\Api\SupportTicketResource;
use Illuminate\Database\Eloquent\Builder;

class SupportTicketController extends Controller
{
    use AppliesApiFilters;

    public function index(ApiIndexRequest $request)
    {
        $this->authorize('viewAny', SupportTicket::class);

        $query = SupportTicket::query()->with(['requester', 'supplier']);

        if ($request->user()->hasRole('buyer')) {
            $query->where('requester_id', $request->user()->id);
        } elseif ($request->user()->hasRole('supplier') && ! $request->user()->hasRole('admin')) {
            $supplierId = $request->user()->supplier?->id;
            $query->whereHas('supplier', fn (Builder $supplier) => $supplier->whereKey($supplierId));
        }

        $this->applySearch($query, $request, ['ticket_number', 'subject', 'description']);
        $this->applyStatus($query, $request);
        $this->applySort($query, $request, ['created_at', 'updated_at', 'last_message_at']);

        return SupportTicketResource::collection($query->paginate($request->perPage())->withQueryString());
    }

    public function show(SupportTicket $supportTicket): SupportTicketResource
    {
        $this->authorize('view', $supportTicket);

        return SupportTicketResource::make($supportTicket->load(['requester', 'supplier', 'messages']));
    }
}
