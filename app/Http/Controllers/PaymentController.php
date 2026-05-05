<?php

namespace App\Http\Controllers;

use App\Domains\ECommerce\Enums\PaymentStatus;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\Payment;
use App\Domains\ECommerce\Services\SslCommerzService;
use App\Domains\ECommerce\Services\StripeGatewayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function __construct(
        private readonly SslCommerzService $sslCommerz,
        private readonly StripeGatewayService $stripeGateway,
    ) {
    }

    public function checkoutSuccess(Request $request, string $orderNumber)
    {
        $order = $this->resolveOrderByNumber($orderNumber);
        $this->assertCheckoutAccess($request, $order);

        return Inertia::render('Checkout/Success', [
            'orderNumber' => $order->order_number,
            'paymentMethod' => $order->payment_method,
            'paymentStatus' => $order->payment_status,
            'transactionId' => $order->transaction_id,
            'checkoutToken' => $order->checkout_token,
            'currency' => $order->currency,
            'grandTotal' => $order->grand_total,
        ]);
    }

    public function process(Request $request, string $orderNumber): RedirectResponse
    {
        $order = $this->resolveOwnedOrder($request, $orderNumber);
        $gateway = $this->resolveGateway(
            $request->string('gateway')->toString() ?: $order->payment_method ?: config('commerce.default_payment_gateway', 'stripe')
        );

        if (! in_array($gateway, ['stripe', 'sslcommerz'], true)) {
            return $this->checkoutSuccessRoute($order)
                ->with('error', 'Selected payment gateway is not supported.');
        }

        if ($order->isPaid()) {
            return $this->checkoutSuccessRoute($order)
                ->with('success', 'Payment already completed for this order.');
        }

        $payment = $this->resolvePayment($order, $gateway);

        $order->forceFill([
            'payment_method' => $gateway,
            'payment_status' => PaymentStatus::Processing->value,
            'checkout_token' => $order->checkout_token ?: (string) Str::uuid(),
        ])->save();

        if ($gateway === 'stripe') {
            if (! $this->stripeGateway->isConfigured()) {
                return $this->checkoutSuccessRoute($order)
                    ->with('error', 'Stripe is not configured yet.');
            }

            $response = $this->stripeGateway->createCheckoutSession($order, $payment);

            if (isset($response['error']) || empty($response['url'])) {
                $payment->forceFill([
                    'status' => PaymentStatus::Pending,
                    'gateway_response' => array_merge((array) $payment->gateway_response, [
                        'stripe_error' => $response['error'] ?? 'Unknown Stripe error',
                        'stripe_error_at' => now()->toIso8601String(),
                    ]),
                ])->save();

                $order->forceFill(['payment_status' => PaymentStatus::Pending->value])->save();

                return $this->checkoutSuccessRoute($order)
                    ->with('error', 'Unable to initiate Stripe checkout right now.');
            }

            $payment->forceFill([
                'status' => PaymentStatus::Processing,
                'gateway_transaction_id' => $response['id'] ?? null,
                'gateway_response' => array_merge((array) $payment->gateway_response, [
                    'stripe_session_id' => $response['id'] ?? null,
                    'stripe_redirect_url' => $response['url'] ?? null,
                    'stripe_initiated_at' => now()->toIso8601String(),
                ]),
            ])->save();

            return redirect()->away($response['url']);
        }

        if (! $this->sslCommerz->isConfigured()) {
            return $this->checkoutSuccessRoute($order)
                ->with('error', 'SSLCOMMERZ is not configured yet.');
        }

        $response = $this->sslCommerz->initiatePayment($order, $payment);

        if (isset($response['error']) || empty($response['GatewayPageURL'])) {
            $payment->forceFill([
                'status' => PaymentStatus::Pending,
                'gateway_response' => array_merge((array) $payment->gateway_response, [
                    'sslcommerz_error' => $response['error'] ?? 'Unknown SSLCOMMERZ error',
                    'sslcommerz_error_at' => now()->toIso8601String(),
                ]),
            ])->save();

            $order->forceFill(['payment_status' => PaymentStatus::Pending->value])->save();

            return $this->checkoutSuccessRoute($order)
                ->with('error', 'Unable to initiate SSLCOMMERZ checkout right now.');
        }

        $payment->forceFill([
            'status' => PaymentStatus::Processing,
            'gateway_response' => array_merge((array) $payment->gateway_response, [
                'sslcommerz_sessionkey' => $response['sessionkey'] ?? null,
                'sslcommerz_redirect_url' => $response['GatewayPageURL'] ?? null,
                'sslcommerz_initiated_at' => now()->toIso8601String(),
            ]),
        ])->save();

        return redirect()->away($response['GatewayPageURL']);
    }

    public function stripeSuccess(Request $request, string $orderNumber): RedirectResponse
    {
        $order = $this->resolveOrderByNumber($orderNumber);
        $this->assertCheckoutAccess($request, $order);
        $payment = $this->latestPaymentForGateway($order, 'stripe');

        if (! $payment) {
            return $this->checkoutSuccessRoute($order)
                ->with('error', 'Stripe payment record not found.');
        }

        if ($payment->isCompleted() || $order->isPaid()) {
            return $this->checkoutSuccessRoute($order)
                ->with('success', 'Payment already completed for this order.');
        }

        $sessionId = trim((string) $request->query('session_id', ''));
        if ($sessionId === '') {
            return $this->checkoutSuccessRoute($order)
                ->with('error', 'Stripe session ID was missing.');
        }

        $session = $this->stripeGateway->retrieveCheckoutSession($sessionId);
        if (isset($session['error'])) {
            $payment->forceFill([
                'gateway_response' => array_merge((array) $payment->gateway_response, [
                    'stripe_success_error' => $session['error'],
                ]),
            ])->save();

            return $this->checkoutSuccessRoute($order)
                ->with('error', 'Unable to verify Stripe payment.');
        }

        $amountMinor = (int) round(((float) $payment->amount) * 100);
        $sessionAmount = $this->stripeAmount($session);
        $isPaid = (string) data_get($session, 'payment_status') === 'paid'
            || (string) data_get($session, 'status') === 'complete'
            || (string) data_get($session, 'status') === 'paid';

        if ($isPaid && $sessionAmount === $amountMinor) {
            $payment->markAsPaid(
                $this->stripeTransactionId(data_get($session, 'payment_intent'), $sessionId),
                $this->buildStripeGatewayResponse($payment, 'success_redirect', $session)
            );

            return $this->checkoutSuccessRoute($order)
                ->with('success', 'Stripe payment completed successfully.');
        }

        $payment->markAsFailed(
            $this->buildStripeGatewayResponse($payment, 'success_redirect_pending', $session)
        );

        return $this->checkoutSuccessRoute($order)
            ->with('error', 'Stripe payment could not be verified.');
    }

    public function stripeCancel(Request $request, string $orderNumber): RedirectResponse
    {
        $order = $this->resolveOrderByNumber($orderNumber);
        $this->assertCheckoutAccess($request, $order);
        $payment = $this->latestPaymentForGateway($order, 'stripe');

        if ($payment) {
            $payment->markAsCancelled([
                'stripe_cancelled_at' => now()->toIso8601String(),
                'cancel_data' => $request->all(),
            ]);
        }

        return $this->checkoutSuccessRoute($order)
            ->with('error', 'Stripe payment was cancelled.');
    }

    public function stripeWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        if (! $this->stripeGateway->verifyWebhookSignature($request->header('Stripe-Signature'), $payload)) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $eventType = (string) $request->input('type');
        $object = (array) $request->input('data.object', []);
        $payment = $this->resolveStripePaymentFromObject($object);

        if (! $payment) {
            return response()->json(['message' => 'Payment not found'], 200);
        }

        if ($this->isStripeSuccessEvent($eventType) && $this->isStripeObjectPaid($eventType, $object) && $this->isStripeAmountValid($payment, $object)) {
            $payment->markAsPaid(
                $this->stripeTransactionId(data_get($object, 'payment_intent'), (string) data_get($object, 'id', '')),
                $this->buildStripeGatewayResponse($payment, $eventType, $object)
            );
        }

        if ($this->isStripeFailureEvent($eventType)) {
            $payment->markAsFailed(
                $this->buildStripeGatewayResponse($payment, $eventType, $object)
            );
        }

        return response()->json(['message' => 'Webhook processed'], 200);
    }

    public function sslcommerzSuccess(Request $request, string $orderNumber): RedirectResponse
    {
        $order = $this->resolveOrderByNumber($orderNumber);
        $this->assertCheckoutAccess($request, $order);
        $payment = $this->latestPaymentForGateway($order, 'sslcommerz');

        if (! $payment) {
            return $this->checkoutSuccessRoute($order)
                ->with('error', 'SSLCOMMERZ payment record not found.');
        }

        if ($payment->isCompleted() || $order->isPaid()) {
            return $this->checkoutSuccessRoute($order)
                ->with('success', 'Payment already confirmed.');
        }

        $valId = trim((string) $request->input('val_id', ''));
        $status = strtoupper((string) $request->input('status', ''));

        if ($valId !== '' && $status === 'VALID') {
            $validation = $this->sslCommerz->validateOrder($valId);

            if ($this->sslCommerz->isTransactionValid($validation)) {
                $validatedAmount = (float) ($validation['amount'] ?? 0);
                $expectedAmount = (float) $payment->amount;

                if (abs($validatedAmount - $expectedAmount) < 0.01) {
                    $payment->markAsPaid(
                        (string) ($validation['tran_id'] ?? $payment->transaction_id),
                        $this->buildSslGatewayResponse($payment, 'success_redirect', $request->all(), $validation)
                    );

                    return $this->checkoutSuccessRoute($order)
                        ->with('success', 'SSLCOMMERZ payment completed successfully.');
                }
            }
        }

        $payment->forceFill([
            'gateway_response' => $this->buildSslGatewayResponse($payment, 'success_redirect_pending', $request->all()),
        ])->save();

        return $this->checkoutSuccessRoute($order)
            ->with('success', 'Order placed. Payment confirmation is pending.');
    }

    public function sslcommerzFail(Request $request, string $orderNumber): RedirectResponse
    {
        $order = $this->resolveOrderByNumber($orderNumber);
        $this->assertCheckoutAccess($request, $order);
        $payment = $this->latestPaymentForGateway($order, 'sslcommerz');

        if ($payment && ! $payment->isCompleted()) {
            $payment->markAsFailed($this->buildSslGatewayResponse($payment, 'payment_failed', $request->all()));
        }

        return $this->checkoutSuccessRoute($order)
            ->with('error', 'SSLCOMMERZ payment failed.');
    }

    public function sslcommerzCancel(Request $request, string $orderNumber): RedirectResponse
    {
        $order = $this->resolveOrderByNumber($orderNumber);
        $this->assertCheckoutAccess($request, $order);
        $payment = $this->latestPaymentForGateway($order, 'sslcommerz');

        if ($payment) {
            $payment->markAsCancelled([
                'sslcommerz_cancelled_at' => now()->toIso8601String(),
                'cancel_data' => $request->all(),
            ]);
        }

        return $this->checkoutSuccessRoute($order)
            ->with('error', 'SSLCOMMERZ payment was cancelled.');
    }

    public function sslcommerzIPN(Request $request): JsonResponse
    {
        $data = $request->all();

        if (! $this->sslCommerz->verifyIpnHash($data)) {
            return response()->json(['message' => 'Invalid IPN hash'], 400);
        }

        $payment = $this->resolveSslPaymentFromIpn($data);

        if (! $payment) {
            return response()->json(['message' => 'Payment not found'], 200);
        }

        $tranId = (string) ($data['tran_id'] ?? $payment->transaction_id);
        $status = strtoupper((string) ($data['status'] ?? ''));
        $valId = (string) ($data['val_id'] ?? '');

        if ($status === 'VALID' && $valId !== '') {
            $validation = $this->sslCommerz->validateOrder($valId);

            if ($this->sslCommerz->isTransactionValid($validation)) {
                $validatedAmount = (float) ($validation['amount'] ?? 0);
                $expectedAmount = (float) $payment->amount;

                if (abs($validatedAmount - $expectedAmount) < 0.01) {
                    $payment->markAsPaid(
                        (string) ($validation['tran_id'] ?? $tranId),
                        $this->buildSslGatewayResponse($payment, 'ipn_valid', $data, $validation)
                    );
                }
            }
        } elseif ($this->sslCommerz->isTransactionFailed($data) || $this->sslCommerz->isTransactionCancelled($data)) {
            $payment->markAsFailed($this->buildSslGatewayResponse($payment, 'ipn_failed', $data));
        }

        return response()->json(['message' => 'IPN processed'], 200);
    }

    private function resolveOwnedOrder(Request $request, string $orderNumber): Order
    {
        $order = $this->resolveOrderByNumber($orderNumber);
        $user = $request->user();

        if ($user && ! $user->hasRole('admin') && (int) $order->buyer_id !== (int) $user->id) {
            abort(403);
        }

        return $order;
    }

    private function assertCheckoutAccess(Request $request, Order $order): void
    {
        $expectedToken = trim((string) $order->checkout_token);
        if ($expectedToken === '') {
            return;
        }

        $providedToken = trim((string) $request->query('access_token', $request->input('access_token', '')));
        if ($providedToken === '' || ! hash_equals($expectedToken, $providedToken)) {
            abort(403);
        }
    }

    private function resolveOrderByNumber(string $orderNumber): Order
    {
        return Order::query()
            ->with(['buyer', 'customer', 'items.product', 'invoice', 'payments'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();
    }

    private function resolveGateway(string $gateway): string
    {
        $gateway = strtolower(trim($gateway));

        return in_array($gateway, ['stripe', 'sslcommerz'], true)
            ? $gateway
            : (string) config('commerce.default_payment_gateway', 'stripe');
    }

    private function resolvePayment(Order $order, string $gateway): Payment
    {
        $pendingStatuses = [PaymentStatus::Pending->value, PaymentStatus::Processing->value];

        $payment = $order->payments()
            ->where('payment_method', $gateway)
            ->whereIn('status', $pendingStatuses)
            ->latest()
            ->first();

        if ($payment instanceof Payment) {
            return $payment;
        }

        return Payment::create([
            'order_id' => $order->id,
            'user_id' => $order->buyer_id,
            'payment_method' => $gateway,
            'amount' => $order->grand_total,
            'currency' => $order->currency,
            'status' => PaymentStatus::Pending,
        ]);
    }

    private function latestPaymentForGateway(Order $order, string $gateway): ?Payment
    {
        return $order->payments()
            ->where('payment_method', $gateway)
            ->latest()
            ->first();
    }

    private function checkoutSuccessRoute(Order $order): RedirectResponse
    {
        $checkoutToken = trim((string) $order->checkout_token);

        if ($checkoutToken === '') {
            $checkoutToken = (string) Str::uuid();
            $order->forceFill(['checkout_token' => $checkoutToken])->save();
        }

        return redirect()->route('checkout.success', [
            'orderNumber' => $order->order_number,
            'access_token' => $checkoutToken,
        ]);
    }

    private function resolveStripePaymentFromObject(array $object): ?Payment
    {
        $paymentId = (int) data_get($object, 'metadata.payment_id', 0);
        if ($paymentId > 0) {
            return Payment::with('order')->find($paymentId);
        }

        $transactionId = $this->stripeTransactionId(data_get($object, 'payment_intent'));
        if ($transactionId !== '') {
            $payment = Payment::with('order')
                ->where('gateway_transaction_id', $transactionId)
                ->latest()
                ->first();

            if ($payment) {
                return $payment;
            }
        }

        $orderNumber = (string) data_get($object, 'metadata.order_number', '');
        if ($orderNumber !== '') {
            return Payment::with('order')
                ->whereHas('order', fn ($query) => $query->where('order_number', $orderNumber))
                ->latest()
                ->first();
        }

        return null;
    }

    private function stripeTransactionId(mixed $value, string $fallback = ''): string
    {
        if (is_string($value) || is_numeric($value)) {
            $value = trim((string) $value);

            if ($value !== '') {
                return $value;
            }
        }

        if (is_array($value) || is_object($value)) {
            $transactionId = trim((string) data_get($value, 'id', ''));

            if ($transactionId !== '') {
                return $transactionId;
            }
        }

        return trim($fallback);
    }

    private function resolveSslPaymentFromIpn(array $data): ?Payment
    {
        $paymentId = (int) ($data['value_a'] ?? 0);
        if ($paymentId > 0) {
            return Payment::with('order')->find($paymentId);
        }

        $tranId = (string) ($data['tran_id'] ?? '');
        if ($tranId !== '') {
            $payment = Payment::with('order')
                ->where('transaction_id', $tranId)
                ->latest()
                ->first();

            if ($payment) {
                return $payment;
            }
        }

        $orderNumber = (string) ($data['value_c'] ?? '');
        if ($orderNumber !== '') {
            return Payment::with('order')
                ->whereHas('order', fn ($query) => $query->where('order_number', $orderNumber))
                ->latest()
                ->first();
        }

        return null;
    }

    private function stripeAmount(array $session): int
    {
        $amount = data_get($session, 'amount_total');
        if ($amount === null) {
            $amount = data_get($session, 'amount_received');
        }
        if ($amount === null) {
            $amount = data_get($session, 'amount');
        }

        return is_numeric($amount) ? (int) $amount : 0;
    }

    private function isStripeAmountValid(Payment $payment, array $object): bool
    {
        $expected = (int) round(((float) $payment->amount) * 100);
        $actual = $this->stripeAmount($object);

        return $actual === $expected;
    }

    private function isStripeSuccessEvent(string $eventType): bool
    {
        return in_array($eventType, [
            'checkout.session.completed',
            'checkout.session.async_payment_succeeded',
            'payment_intent.succeeded',
        ], true);
    }

    private function isStripeFailureEvent(string $eventType): bool
    {
        return in_array($eventType, [
            'checkout.session.async_payment_failed',
            'payment_intent.payment_failed',
        ], true);
    }

    private function isStripeObjectPaid(string $eventType, array $object): bool
    {
        if (str_starts_with($eventType, 'payment_intent')) {
            return (string) data_get($object, 'status') === 'succeeded';
        }

        return in_array((string) data_get($object, 'payment_status'), ['paid', 'no_payment_required'], true)
            || (string) data_get($object, 'status') === 'complete';
    }

    private function buildStripeGatewayResponse(Payment $payment, string $eventType, array $object): array
    {
        return array_merge((array) $payment->gateway_response, [
            'last_webhook_event' => [
                'type' => $eventType,
                'received_at' => now()->toIso8601String(),
            ],
            'webhook_snapshot' => [
                'object_id' => data_get($object, 'id'),
                'payment_status' => data_get($object, 'payment_status'),
                'status' => data_get($object, 'status'),
                'amount_total' => data_get($object, 'amount_total'),
                'amount_received' => data_get($object, 'amount_received'),
                'amount' => data_get($object, 'amount'),
            ],
        ]);
    }

    private function buildSslGatewayResponse(Payment $payment, string $eventType, array $sslData, array $validationData = []): array
    {
        return array_merge((array) $payment->gateway_response, [
            'last_event' => [
                'type' => $eventType,
                'received_at' => now()->toIso8601String(),
            ],
            'sslcommerz_data' => [
                'tran_id' => $sslData['tran_id'] ?? null,
                'val_id' => $sslData['val_id'] ?? null,
                'status' => $sslData['status'] ?? null,
                'amount' => $sslData['amount'] ?? null,
                'currency' => $sslData['currency'] ?? null,
            ],
            'validation_data' => ! empty($validationData) ? [
                'status' => $validationData['status'] ?? null,
                'amount' => $validationData['amount'] ?? null,
                'currency_amount' => $validationData['currency_amount'] ?? null,
                'validated_at' => now()->toIso8601String(),
            ] : null,
        ]);
    }
}
