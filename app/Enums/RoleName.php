<?php

namespace App\Enums;

enum RoleName: string
{
    case Admin = 'admin';
    case Supplier = 'supplier';
    case Buyer = 'buyer';
    case MarketingManager = 'marketing_manager';
    case WorkflowManager = 'workflow_manager';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Supplier => 'Supplier',
            self::Buyer => 'Buyer',
            self::MarketingManager => 'Marketing Manager',
            self::WorkflowManager => 'Workflow Manager',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function publicValues(): array
    {
        return array_map(
            fn (self $role): string => $role->value,
            [
                self::Buyer,
                self::Supplier,
                self::MarketingManager,
                self::WorkflowManager,
            ],
        );
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function publicOptions(): array
    {
        return array_map(
            fn (self $role): array => [
                'value' => $role->value,
                'label' => $role->label(),
            ],
            [
                self::Buyer,
                self::Supplier,
                self::MarketingManager,
                self::WorkflowManager,
            ],
        );
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $role): string => $role->value, self::cases());
    }
}
