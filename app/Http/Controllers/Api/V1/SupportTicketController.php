<?php

namespace App\Http\Controllers\Api\V1;

use App\Domains\Support\Enums\SupportChannel;
use App\Domains\Support\Enums\TicketPriority;
use App\Domains\Support\Enums\TicketStatus;
use App\Domains\Support\Models\SupportTicket;
use App\Domains\Support\Services\SupportTicketService;
use App\Http\Controllers\Api\Concerns\FormatsApiResponses;
use App\Http\Controllers\Api\V1\Concerns\AppliesApiFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApiIndexRequest;
use App\Http\Resources\Api\SupportTicketResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupportTicketController extends Controller
{
    use AppliesApiFilters;
    use FormatsApiResponses;

    public function __construct(private readonly SupportTicketService $tickets)
    {
    }

    public function index(ApiIndexRequest $request): JsonResponse
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

        $paginator = $query->paginate($request->perPage())->withQueryString();

        return $this->paginatedResourceResponse(
            paginator: $paginator,
            resourceClass: SupportTicketResource::class,
            message: 'Support tickets fetched successfully.',
        );
    }

    public function show(SupportTicket $supportTicket): JsonResponse
    {
        $this->authorize('view', $supportTicket);

        return $this->resourceResponse(
            SupportTicketResource::make($supportTicket->load(['requester', 'supplier.user', 'order', 'customer', 'assignee', 'messages.sender', 'supplierNotifications'])),
            'Support ticket details fetched successfully.',
        );
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', SupportTicket::class);

        $validated = $this->validateTicketData($request);

        $ticket = $this->tickets->createTicket($request->user(), $validated, SupportChannel::Web);

        return $this->resourceResponse(
            SupportTicketResource::make($ticket->load(['requester', 'supplier.user', 'order', 'customer', 'assignee', 'messages.sender', 'supplierNotifications'])),
            'Support ticket created successfully',
            201,
        );
    }

    public function reply(Request $request, SupportTicket $supportTicket): JsonResponse
    {
        $this->authorize('reply', $supportTicket);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $ticket = $this->tickets->replyTicket($supportTicket, $request->user(), $validated);

        return $this->resourceResponse(
            SupportTicketResource::make($ticket),
            'Reply added successfully',
        );
    }

    public function updateStatus(Request $request, SupportTicket $supportTicket): JsonResponse
    {
        $this->authorize('changeStatus', $supportTicket);

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in($this->ticketStatuses())],
        ]);

        $ticket = $this->tickets->updateStatus($supportTicket, TicketStatus::from($validated['status']));

        return $this->resourceResponse(
            SupportTicketResource::make($ticket),
            'Ticket status updated successfully',
        );
    }

    public function assign(Request $request, SupportTicket $supportTicket): JsonResponse
    {
        $this->authorize('assign', $supportTicket);

        $validated = $request->validate([
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $assignee = isset($validated['assigned_to']) ? User::query()->find($validated['assigned_to']) : null;
        $ticket = $this->tickets->assignTicket($supportTicket, $assignee);

        return $this->resourceResponse(
            SupportTicketResource::make($ticket),
            'Ticket assignment updated successfully',
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function validateTicketData(Request $request): array
    {
        return $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'priority' => ['nullable', 'string', Rule::in(array_map(fn (TicketPriority $priority): string => $priority->value, TicketPriority::cases()))],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'order_id' => ['nullable', 'integer', 'exists:orders,id'],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'tags' => ['nullable', 'array'],
            'metadata' => ['nullable', 'array'],
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function ticketStatuses(): array
    {
        return array_map(fn (TicketStatus $status): string => $status->value, TicketStatus::cases());
    }
}
