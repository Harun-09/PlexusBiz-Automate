<?php

namespace App\Domains\ECommerce\Services;

class PaymentGatewayResolver
{
    public function __construct(
        private readonly StripeGatewayService $stripeGateway,
        private readonly SslCommerzService $sslCommerz,
    ) {
    }

    public function resolve(?string $preferredGateway = null): ?string
    {
        $candidates = [];

        $preferredGateway = $this->normalize($preferredGateway);
        if ($preferredGateway !== '') {
            $candidates[] = $preferredGateway;
        }

        $defaultGateway = $this->normalize(config('commerce.default_payment_gateway', 'stripe'));
        if ($defaultGateway !== '') {
            $candidates[] = $defaultGateway;
        }

        $candidates[] = 'stripe';
        $candidates[] = 'sslcommerz';

        foreach (array_values(array_unique($candidates)) as $gateway) {
            if ($this->isConfigured($gateway)) {
                return $gateway;
            }
        }

        return null;
    }

    public function isConfigured(string $gateway): bool
    {
        return match ($this->normalize($gateway)) {
            'stripe' => $this->stripeGateway->isConfigured(),
            'sslcommerz' => $this->sslCommerz->isConfigured(),
            default => false,
        };
    }

    public function label(?string $gateway): string
    {
        $gateway = $this->normalize($gateway);

        return match ($gateway) {
            'stripe' => 'Stripe',
            'sslcommerz' => 'SSLCOMMERZ',
            '' => 'Unavailable',
            default => ucwords(str_replace(['_', '-'], ' ', $gateway)),
        };
    }

    private function normalize(?string $gateway): string
    {
        return strtolower(trim((string) $gateway));
    }
}
