<?php

namespace App\Http\Controllers\Api\V1;

use App\Domains\Support\Enums\SupportChannel;
use App\Domains\Support\Enums\TicketPriority;
use App\Domains\Support\Enums\TicketStatus;
use App\Domains\Support\Models\SupportTicket;
use App\Domains\Support\Services\SupportTicketService;
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

    public function __construct(private readonly SupportTicketService $tickets)
    {
    }

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

        return SupportTicketResource::make($supportTicket->load(['requester', 'supplier.user', 'order', 'customer', 'assignee', 'messages.sender', 'supplierNotifications']));
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', SupportTicket::class);

        $validated = $this->validateTicketData($request);

        $ticket = $this->tickets->createTicket($request->user(), $validated, SupportChannel::Web);

        return response()->json([
            'message' => 'Support ticket created successfully',
            'data' => SupportTicketResource::make($ticket->load(['requester', 'supplier.user', 'order', 'customer', 'assignee', 'messages.sender', 'supplierNotifications'])),
        ], 201);
    }

    public function reply(Request $request, SupportTicket $supportTicket): JsonResponse
    {
        $this->authorize('reply', $supportTicket);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $ticket = $this->tickets->replyTicket($supportTicket, $request->user(), $validated);

        return response()->json([
            'message' => 'Reply added successfully',
            'data' => SupportTicketResource::make($ticket),
        ]);
    }

    public function updateStatus(Request $request, SupportTicket $supportTicket): JsonResponse
    {
        $this->authorize('changeStatus', $supportTicket);

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in($this->ticketStatuses())],
        ]);

        $ticket = $this->tickets->updateStatus($supportTicket, TicketStatus::from($validated['status']));

        return response()->json([
            'message' => 'Ticket status updated successfully',
            'data' => SupportTicketResource::make($ticket),
        ]);
    }

    public function assign(Request $request, SupportTicket $supportTicket): JsonResponse
    {
        $this->authorize('assign', $supportTicket);

        $validated = $request->validate([
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $assignee = isset($validated['assigned_to']) ? User::query()->find($validated['assigned_to']) : null;
        $ticket = $this->tickets->assignTicket($supportTicket, $assignee);

        return response()->json([
            'message' => 'Ticket assignment updated successfully',
            'data' => SupportTicketResource::make($ticket),
        ]);
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
