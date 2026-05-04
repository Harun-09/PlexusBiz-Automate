<?php

namespace Tests\Feature\Audit;

use App\Domains\ECommerce\Enums\SupplierStatus;
use App\Domains\ECommerce\Models\Supplier;
use App\Domains\Workflow\Enums\AutomationRuleStatus;
use App\Domains\Workflow\Enums\WorkflowActionType;
use App\Domains\Workflow\Enums\WorkflowTriggerEvent;
use App\Domains\Workflow\Models\AutomationRule;
use App\Enums\RoleName;
use App\Enums\UserStatus;
use App\Models\User;
use App\Support\Audit\Models\AuditLog;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AuditTrailTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_the_audit_log_screen(): void
    {
        $this->seed(RbacSeeder::class);

        $admin = User::where('email', 'admin@plexus.test')->firstOrFail();

        $this->actingAs($admin)
            ->get('/admin/audit-logs')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Admin/AuditLogs/Index')
                ->where('workspace.title', 'Audit Logs')
                ->where('workspace.columns.0', 'Action')
                ->where('workspace.columns.5', 'Executed'));
    }

    public function test_module_settings_updates_are_logged(): void
    {
        $this->seed(RbacSeeder::class);

        $admin = User::where('email', 'admin@plexus.test')->firstOrFail();

        $this->actingAs($admin)
            ->patch('/settings/modules', [
                'modules' => [
                    'admin' => false,
                    'crm' => true,
                    'ecommerce' => false,
                    'marketing' => true,
                    'notifications' => true,
                    'settings' => false,
                    'social' => true,
                    'support' => true,
                    'workflow' => true,
                ],
            ])
            ->assertRedirect();

        $log = AuditLog::query()->where('action', 'settings.modules.updated')->firstOrFail();

        $this->assertSame('settings', $log->module_key);
        $this->assertSame($admin->id, $log->actor_id);
        $this->assertSame('Module settings', $log->subject_label);
        $this->assertNotEmpty($log->before_json);
        $this->assertNotEmpty($log->after_json);
        $this->assertContains('ecommerce', $log->metadata_json['changed_modules']);
    }

    public function test_admin_user_changes_are_logged(): void
    {
        $this->seed(RbacSeeder::class);

        $admin = User::where('email', 'admin@plexus.test')->firstOrFail();
        $target = User::factory()->create([
            'name' => 'Audit User',
            'email' => 'audit-user@example.test',
            'status' => UserStatus::Active->value,
        ]);
        $target->assignRole(RoleName::Buyer->value);

        $this->actingAs($admin)
            ->patch("/admin/users/{$target->id}", [
                'name' => 'Audit User Updated',
                'email' => 'audit-user-updated@example.test',
                'password' => '',
                'role' => RoleName::MarketingManager->value,
                'status' => UserStatus::Inactive->value,
            ])
            ->assertRedirect();

        $log = AuditLog::query()->where('action', 'admin.users.updated')->firstOrFail();

        $this->assertSame('admin', $log->module_key);
        $this->assertSame($admin->id, $log->actor_id);
        $this->assertSame('Audit User Updated', $log->subject_label);
        $this->assertSame('buyer', $log->before_json['role']);
        $this->assertSame('marketing_manager', $log->after_json['role']);
        $this->assertSame('inactive', $log->after_json['status']);
    }

    public function test_admin_supplier_changes_are_logged(): void
    {
        $this->seed(RbacSeeder::class);

        $admin = User::where('email', 'admin@plexus.test')->firstOrFail();
        $supplierUser = User::factory()->create([
            'name' => 'Supplier Audit',
            'email' => 'supplier-audit@example.test',
            'status' => UserStatus::Active->value,
        ]);

        $this->actingAs($admin)
            ->post('/admin/suppliers', [
                'user_id' => $supplierUser->id,
                'company_name' => 'Audit Supply Ltd',
                'contact_email' => 'audit-supply@example.test',
                'phone' => '01700000001',
                'tax_number' => 'TAX-001',
                'status' => SupplierStatus::Pending->value,
            ])
            ->assertRedirect();

        $supplier = Supplier::query()->where('company_name', 'Audit Supply Ltd')->firstOrFail();

        $this->actingAs($admin)
            ->patch("/admin/suppliers/{$supplier->id}", [
                'company_name' => 'Audit Supply Ltd',
                'contact_email' => 'audit-supply@example.test',
                'phone' => '01700000002',
                'tax_number' => 'TAX-001',
                'status' => SupplierStatus::Approved->value,
            ])
            ->assertRedirect();

        $this->actingAs($admin)
            ->delete("/admin/suppliers/{$supplier->id}")
            ->assertRedirect();

        $actions = AuditLog::query()->where('subject_label', 'Audit Supply Ltd')->pluck('action')->all();

        $this->assertCount(3, $actions);
        $this->assertContains('admin.suppliers.created', $actions);
        $this->assertContains('admin.suppliers.updated', $actions);
        $this->assertContains('admin.suppliers.deleted', $actions);
    }

    public function test_workflow_rule_lifecycle_is_logged(): void
    {
        $this->seed(RbacSeeder::class);

        $admin = User::where('email', 'admin@plexus.test')->firstOrFail();
        $this->actingAs($admin);

        $rule = AutomationRule::create([
            'name' => 'Audit rule',
            'trigger_event' => WorkflowTriggerEvent::OrderPlaced->value,
            'conditions_json' => [
                ['field' => 'order.grand_total', 'operator' => 'greater_than', 'value' => 100],
            ],
            'actions_json' => [
                ['type' => WorkflowActionType::NotifySupplier->value, 'config' => []],
            ],
            'status' => AutomationRuleStatus::Active,
            'priority' => 2,
            'run_async' => false,
        ]);

        $rule->update([
            'name' => 'Audit rule updated',
            'priority' => 3,
        ]);

        $rule->delete();

        $actions = AuditLog::query()->whereIn('action', [
            'workflow.rules.created',
            'workflow.rules.updated',
            'workflow.rules.deleted',
        ])->pluck('action')->all();

        $this->assertCount(3, $actions);
        $this->assertContains('workflow.rules.created', $actions);
        $this->assertContains('workflow.rules.updated', $actions);
        $this->assertContains('workflow.rules.deleted', $actions);
    }
}
