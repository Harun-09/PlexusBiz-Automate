<?php

namespace App\Domains\ECommerce\Services;

use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StripeGatewayService
{
    private string $secretKey;
    private string $publicKey;
    private string $webhookSecret;
    private string $currency;
    private string $displayName;
    private ?string $buttonColor;
    private ?string $backgroundColor;
    private ?string $borderStyle;
    private ?string $fontFamily;
    private ?string $logoUrl;
    private ?string $iconUrl;

    public function __construct()
    {
        $config = config('services.stripe', []);
        $mode = (string) ($config['mode'] ?? 'sandbox');

        if ($mode === 'live') {
            $this->secretKey = (string) ($config['live_secret_key'] ?? '');
            $this->publicKey = (string) ($config['live_public_key'] ?? '');
        } else {
            $this->secretKey = (string) ($config['sandbox_secret_key'] ?? '');
            $this->publicKey = (string) ($config['sandbox_public_key'] ?? '');
        }

        $this->webhookSecret = (string) ($config['webhook_secret'] ?? '');
        $this->currency = strtolower((string) ($config['currency'] ?? 'bdt'));
        $this->displayName = trim((string) ($config['display_name'] ?? 'PlexusBiz'));
        $this->buttonColor = $this->normalizeHexColor($config['button_color'] ?? null);
        $this->backgroundColor = $this->normalizeHexColor($config['background_color'] ?? null);
        $this->borderStyle = $this->normalizeOptionalString($config['border_style'] ?? null);
        $this->fontFamily = $this->normalizeOptionalString($config['font_family'] ?? null);
        $this->logoUrl = $this->normalizeHttpsUrl($config['logo_url'] ?? null);
        $this->iconUrl = $this->normalizeHttpsUrl($config['icon_url'] ?? null);
    }

    public function isConfigured(): bool
    {
        return ! empty($this->secretKey)
            && ! empty($this->publicKey)
            && ! str_contains($this->secretKey, 'XXXX')
            && ! str_contains($this->publicKey, 'XXXX');
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function createCheckoutSession(Order $order, Payment $payment): array
    {
        if (! $this->isConfigured()) {
            return ['error' => 'Stripe is not configured.'];
        }

        $amountInMinor = (int) round(((float) $payment->amount) * 100);
        if ($amountInMinor <= 0) {
            return ['error' => 'Invalid payment amount for checkout session.'];
        }

        $productName = $this->buildProductName($order);
        $description = $this->buildDescription($order);
        $customerEmail = $order->buyer?->email ?: $order->customer?->email;
        $accessToken = trim((string) $order->checkout_token);
        $successUrl = route('payment.stripe.success', $order->order_number, true).'?session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl = route('payment.stripe.cancel', $order->order_number, true);

        if ($accessToken !== '') {
            $successUrl .= '&access_token='.rawurlencode($accessToken);
            $cancelUrl .= '?access_token='.rawurlencode($accessToken);
        }

        $payload = [
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'submit_type' => 'pay',
            'locale' => 'auto',
            'billing_address_collection' => 'auto',
            'phone_number_collection[enabled]' => 'true',
            'custom_text[submit][message]' => 'Secure card payment for your PlexusBiz order.',
            'client_reference_id' => (string) $payment->id,
            'metadata[order_id]' => (string) $order->id,
            'metadata[order_number]' => (string) $order->order_number,
            'metadata[payment_id]' => (string) $payment->id,
            'payment_intent_data[metadata][order_id]' => (string) $order->id,
            'payment_intent_data[metadata][order_number]' => (string) $order->order_number,
            'payment_intent_data[metadata][payment_id]' => (string) $payment->id,
            'line_items[0][price_data][currency]' => $this->currency,
            'line_items[0][price_data][unit_amount]' => $amountInMinor,
            'line_items[0][price_data][product_data][name]' => $productName,
            'line_items[0][price_data][product_data][description]' => $description,
            'line_items[0][quantity]' => 1,
        ];

        $this->appendBrandingSettings($payload);

        if (! empty($customerEmail)) {
            $payload['customer_email'] = $customerEmail;
        }

        if (! empty($order->buyer_id)) {
            $payload['metadata[user_id]'] = (string) $order->buyer_id;
            $payload['payment_intent_data[metadata][user_id]'] = (string) $order->buyer_id;
        }

        $session = $this->makeRequest('checkout/sessions', 'POST', $payload);

        if (isset($session['error']) && $this->isBrandingError($session)) {
            Log::warning('Stripe branding settings failed, retrying checkout session without branding.', [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'error' => $session['error'],
                'code' => $session['code'] ?? null,
            ]);

            $fallbackPayload = $payload;
            $this->stripBrandingSettings($fallbackPayload);
            $session = $this->makeRequest('checkout/sessions', 'POST', $fallbackPayload);
        }

        if (isset($session['error'])) {
            return $session;
        }

        return [
            'status' => 'SUCCESS',
            'id' => $session['id'] ?? null,
            'url' => $session['url'] ?? null,
            'raw' => $session,
        ];
    }

    public function retrieveCheckoutSession(string $sessionId): array
    {
        if (! $this->isConfigured()) {
            return ['error' => 'Stripe is not configured.'];
        }

        return $this->makeRequest('checkout/sessions/'.$sessionId, 'GET', [
            'expand[]' => 'payment_intent',
        ]);
    }

    public function verifyWebhookSignature(?string $signatureHeader, string $payload, int $tolerance = 300): bool
    {
        if (empty($this->webhookSecret) || empty($signatureHeader) || empty($payload)) {
            return false;
        }

        $parts = [];
        foreach (explode(',', $signatureHeader) as $segment) {
            [$key, $value] = array_pad(explode('=', trim($segment), 2), 2, null);
            if ($key !== null && $value !== null) {
                $parts[$key][] = $value;
            }
        }

        $timestamp = isset($parts['t'][0]) ? (int) $parts['t'][0] : 0;
        $v1Signatures = $parts['v1'] ?? [];

        if ($timestamp <= 0 || empty($v1Signatures)) {
            return false;
        }

        if (abs(time() - $timestamp) > $tolerance) {
            return false;
        }

        $signedPayload = $timestamp.'.'.$payload;
        $expectedSignature = hash_hmac('sha256', $signedPayload, $this->webhookSecret);

        foreach ($v1Signatures as $signature) {
            if (hash_equals($expectedSignature, $signature)) {
                return true;
            }
        }

        return false;
    }

    private function buildProductName(Order $order): string
    {
        $itemName = (string) ($order->items()->select('product_name')->value('product_name') ?? '');

        if ($itemName !== '') {
            return Str::limit($itemName, 60);
        }

        return 'Order '.$order->order_number;
    }

    private function buildDescription(Order $order): string
    {
        $itemCount = (int) $order->items()->sum('quantity');
        $summary = 'Order '.$order->order_number;

        if ($itemCount > 0) {
            $summary .= ' | '.$itemCount.' item'.($itemCount > 1 ? 's' : '');
        }

        return $summary;
    }

    private function appendBrandingSettings(array &$payload): void
    {
        if ($this->displayName !== '') {
            $payload['branding_settings[display_name]'] = $this->displayName;
        }

        if ($this->buttonColor !== null) {
            $payload['branding_settings[button_color]'] = $this->buttonColor;
        }

        if ($this->backgroundColor !== null) {
            $payload['branding_settings[background_color]'] = $this->backgroundColor;
        }

        if ($this->borderStyle !== null) {
            $payload['branding_settings[border_style]'] = $this->borderStyle;
        }

        if ($this->fontFamily !== null) {
            $payload['branding_settings[font_family]'] = $this->fontFamily;
        }

        if ($this->logoUrl !== null) {
            $payload['branding_settings[logo][type]'] = 'url';
            $payload['branding_settings[logo][url]'] = $this->logoUrl;
        }

        if ($this->iconUrl !== null) {
            $payload['branding_settings[icon][type]'] = 'url';
            $payload['branding_settings[icon][url]'] = $this->iconUrl;
        }
    }

    private function stripBrandingSettings(array &$payload): void
    {
        foreach (array_keys($payload) as $key) {
            if (str_starts_with($key, 'branding_settings[')) {
                unset($payload[$key]);
            }
        }
    }

    private function isBrandingError(array $response): bool
    {
        $error = strtolower((string) ($response['error'] ?? ''));

        return $error !== '' && str_contains($error, 'branding_settings');
    }

    private function normalizeOptionalString(mixed $value): ?string
    {
        $normalized = trim((string) $value);

        return $normalized !== '' ? $normalized : null;
    }

    private function normalizeHexColor(mixed $value): ?string
    {
        $normalized = trim((string) $value);
        if ($normalized === '') {
            return null;
        }

        if (preg_match('/^#?[0-9a-fA-F]{6}$/', $normalized) !== 1) {
            return null;
        }

        return str_starts_with($normalized, '#') ? $normalized : ('#'.$normalized);
    }

    private function normalizeHttpsUrl(mixed $value): ?string
    {
        $url = trim((string) $value);
        if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        return strtolower((string) parse_url($url, PHP_URL_SCHEME)) === 'https' ? $url : null;
    }

    private function makeRequest(string $endpoint, string $method = 'GET', array $data = []): array
    {
        try {
            $request = Http::withBasicAuth($this->secretKey, '')
                ->withoutVerifying()
                ->asForm()
                ->acceptJson()
                ->timeout(30);

            $url = 'https://api.stripe.com/v1/'.ltrim($endpoint, '/');

            $response = strtoupper($method) === 'POST'
                ? $request->post($url, $data)
                : $request->get($url, $data);

            $result = $response->json() ?? [];

            if (! $response->successful()) {
                Log::error('Stripe API error', [
                    'status' => $response->status(),
                    'endpoint' => $endpoint,
                    'response' => $result,
                ]);

                return [
                    'error' => data_get($result, 'error.message', 'Stripe request failed.'),
                    'code' => data_get($result, 'error.code', 'stripe_error'),
                ];
            }

            return is_array($result) ? $result : ['error' => 'Invalid Stripe response.'];
        } catch (\Throwable $exception) {
            Log::error('Stripe request exception', [
                'endpoint' => $endpoint,
                'message' => $exception->getMessage(),
            ]);

            return [
                'error' => 'Failed to connect to Stripe.',
                'code' => 'stripe_connection_error',
            ];
        }
    }
}
