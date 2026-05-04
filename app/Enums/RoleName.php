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
    public static function values(): array
    {
        return array_map(fn (self $role): string => $role->value, self::cases());
    }
}
