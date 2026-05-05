<?php

namespace App\Enums;

enum PermissionName: string
{
    case ViewDashboard = 'view_dashboard';
    case ManageUsers = 'manage_users';
    case ManageSuppliers = 'manage_suppliers';
    case ManageProducts = 'manage_products';
    case ManageOrders = 'manage_orders';
    case ManageCustomers = 'manage_customers';
    case ManageCampaigns = 'manage_campaigns';
    case ManageSocialPosts = 'manage_social_posts';
    case ManageSocialAccounts = 'manage_social_accounts';
    case ManageAutomationRules = 'manage_automation_rules';
    case ManageWorkflowLogs = 'manage_workflow_logs';
    case ManageTickets = 'manage_tickets';
    case ManageSettings = 'manage_settings';
    case ManageOwnProducts = 'manage_own_products';
    case ManageOwnOrders = 'manage_own_orders';
    case ManageCart = 'manage_cart';
    case ManageOwnTickets = 'manage_own_tickets';
    case ManageMarketing = 'manage_marketing';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $permission): string => $permission->value, self::cases());
    }
}
