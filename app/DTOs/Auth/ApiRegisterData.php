<?php

namespace App\DTOs\Auth;

class ApiRegisterData
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly string $deviceName,
    ) {
    }

    /**
     * @param array<string, mixed> $validated
     */
    public static function fromValidated(array $validated): self
    {
        return new self(
            name: trim((string) $validated['name']),
            email: strtolower(trim((string) $validated['email'])),
            password: (string) $validated['password'],
            deviceName: trim((string) ($validated['device_name'] ?? 'api-client')),
        );
    }
}

