<?php

namespace App\Http\Controllers;

use App\Domains\CRM\Enums\InteractionType;
use App\Domains\CRM\Enums\LeadStatus;
use App\Domains\CRM\Models\Lead;
use App\Domains\CRM\Services\CustomerProfileService;
use App\Domains\CRM\Services\InteractionLogger;
use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Enums\RfqStatus;
use App\Domains\ECommerce\Events\RfqCreated;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Rfq;
use App\Domains\ECommerce\Models\RfqItem;
use App\Domains\ECommerce\Services\NumberSequenceService;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class RfqRequestController extends Controller
{
    public function __construct(
        private readonly CustomerProfileService $customers,
        private readonly InteractionLogger $interactions,
        private readonly NumberSequenceService $numbers,
    ) {
    }

    public function create(Request $request, ?Product $product = null): Response
    {
        if ($product && $product->status !== ProductStatus::Active) {
            abort(404);
        }

        return Inertia::render('Frontend/RfqRequest', [
            'product' => $product ? [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'slug' => $product->slug,
                'supplier' => [
                    'id' => $product->supplier?->id,
                    'company_name' => $product->supplier?->company_name,
                ],
                'base_price' => $product->base_price,
                'moq' => $product->moq,
                'available_stock' => $product->availableStock(),
                'primary_image_url' => $product->primaryImageUrl(),
                'default_quantity' => max(1, (int) $product->moq),
                'description' => Str::of(strip_tags((string) $product->description))->squish()->limit(160)->toString(),
            ] : null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'contact_name' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'business_type' => ['nullable', 'string', 'max:255'],
            'product_id' => ['nullable', 'integer', Rule::exists('products', 'id')],
            'product_name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'target_price' => ['nullable', 'numeric', 'min:0'],
            'needed_by' => ['nullable', 'date', 'after_or_equal:today'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $product = null;

        if (! empty($validated['product_id'])) {
            $product = Product::query()
                ->with('supplier')
                ->whereKey($validated['product_id'])
                ->where('status', ProductStatus::Active->value)
                ->first();

            if (! $product) {
                throw ValidationException::withMessages([
                    'product_id' => 'The selected product is not available.',
                ]);
            }
        }

        $buyer = $request->user();
        $customer = $buyer?->hasRole('buyer')
            ? $this->customers->ensureForUser($buyer, [
                'contact_name' => $validated['contact_name'],
                'company_name' => $validated['company_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'business_type' => $validated['business_type'] ?? null,
            ])
            : null;

        $payload = DB::transaction(function () use ($validated, $product, $buyer, $customer): array {
            $estimatedValue = 0.0;

            if (! empty($validated['target_price'])) {
                $estimatedValue = (float) $validated['target_price'] * (int) $validated['quantity'];
            } elseif ($product) {
                $estimatedValue = (float) $product->base_price * (int) $validated['quantity'];
            }

            $lead = Lead::create([
                'customer_id' => $customer?->id,
                'source' => 'rfq',
                'status' => LeadStatus::New,
                'company_name' => $validated['company_name'],
                'contact_name' => $validated['contact_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'value' => $estimatedValue,
                'notes' => $this->composeLeadNotes($validated, $product),
                'next_follow_up_at' => now()->addDay(),
            ]);

            if ($customer) {
                $customer->forceFill([
                    'contact_name' => $validated['contact_name'],
                    'company_name' => $validated['company_name'],
                    'phone' => $validated['phone'] ?? null,
                    'business_type' => $validated['business_type'] ?? null,
                    'email' => $validated['email'],
                    'last_activity_at' => now(),
                ])->save();
            }

            $rfq = null;

            if ($buyer instanceof User && $buyer->hasRole('buyer')) {
                $rfq = Rfq::create([
                    'buyer_id' => $buyer->id,
                    'supplier_id' => $product?->supplier_id,
                    'rfq_number' => $this->numbers->rfqNumber(),
                    'status' => RfqStatus::Open,
                    'message' => $validated['message'],
                    'needed_by' => $validated['needed_by'] ?? null,
                ]);

                RfqItem::create([
                    'rfq_id' => $rfq->id,
                    'product_id' => $product?->id,
                    'description' => $validated['product_name'],
                    'quantity' => (int) $validated['quantity'],
                    'target_price' => $validated['target_price'] ?? null,
                ]);
            }

            if ($customer) {
                $this->interactions->record(
                    customer: $customer,
                    type: InteractionType::Rfq,
                    summary: 'RFQ request submitted through the public intake form.',
                    related: $rfq ?? $lead,
                    payload: [
                        'lead_id' => $lead->id,
                        'rfq_number' => $rfq?->rfq_number,
                        'product_id' => $product?->id,
                        'product_name' => $validated['product_name'],
                        'quantity' => (int) $validated['quantity'],
                        'target_price' => $validated['target_price'] ?? null,
                    ],
                    actor: $buyer instanceof User ? $buyer : null,
                    direction: 'inbound',
                );
            }

            return [
                'lead' => $lead,
                'rfq' => $rfq,
                'product' => $product,
                'buyer' => $buyer,
                'validated' => $validated,
            ];
        });

        if ($payload['rfq']) {
            RfqCreated::dispatch($this->rfqPayload(
                lead: $payload['lead'],
                rfq: $payload['rfq'],
                product: $payload['product'],
                buyer: $payload['buyer'] instanceof User ? $payload['buyer'] : null,
                validated: $payload['validated'],
            ));
        }

        return redirect('/')
            ->with('success', 'Your quotation request has been received.');
    }

    /**
     * @param array<string, mixed> $validated
     */
    private function composeLeadNotes(array $validated, ?Product $product): string
    {
        $lines = [
            $validated['message'],
            $product ? 'Product: '.$product->name.' ('.$product->sku.')' : null,
            'Quantity: '.$validated['quantity'],
            ! empty($validated['target_price']) ? 'Target price: '.$validated['target_price'] : null,
            ! empty($validated['needed_by']) ? 'Needed by: '.$validated['needed_by'] : null,
        ];

        return trim(implode("\n\n", array_values(array_filter($lines, fn ($line) => $line !== null && $line !== ''))));
    }

    /**
     * @param array<string, mixed> $validated
     * @return array<string, mixed>
     */
    private function rfqPayload(Lead $lead, Rfq $rfq, ?Product $product, ?User $buyer, array $validated): array
    {
        return [
            'rfq' => [
                'id' => $rfq->id,
                'rfq_number' => $rfq->rfq_number,
                'supplier_id' => $rfq->supplier_id,
                'status' => $rfq->status->value,
                'message' => $rfq->message,
                'needed_by' => $rfq->needed_by?->toIso8601String(),
            ],
            'lead' => [
                'id' => $lead->id,
                'source' => $lead->source,
                'status' => $lead->status->value,
                'value' => (float) $lead->value,
            ],
            'buyer' => [
                'id' => $buyer?->id,
                'name' => $buyer?->name,
                'email' => $buyer?->email,
            ],
            'product' => $product ? [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'supplier_id' => $product->supplier_id,
                'supplier_name' => $product->supplier?->company_name,
            ] : null,
            'quantity' => (int) $validated['quantity'],
            'target_price' => $validated['target_price'] ?? null,
        ];
    }
}
