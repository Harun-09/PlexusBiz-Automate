<?php

namespace Database\Seeders;

use App\Enums\PermissionName;
use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (PermissionName::values() as $permission) {
            Permission::findOrCreate($permission);
        }

        $roles = collect(RoleName::cases())
            ->mapWithKeys(fn (RoleName $role): array => [$role->value => Role::findOrCreate($role->value)]);

        $roles[RoleName::Admin->value]->syncPermissions(PermissionName::values());

        $roles[RoleName::Supplier->value]->syncPermissions([
            PermissionName::ViewDashboard->value,
            PermissionName::ManageOwnProducts->value,
            PermissionName::ManageOwnOrders->value,
            PermissionName::ManageOwnTickets->value,
        ]);

        $roles[RoleName::Buyer->value]->syncPermissions([
            PermissionName::ViewDashboard->value,
            PermissionName::ManageCart->value,
            PermissionName::ManageOwnTickets->value,
        ]);

        $roles[RoleName::MarketingManager->value]->syncPermissions([
            PermissionName::ViewDashboard->value,
            PermissionName::ManageCampaigns->value,
            PermissionName::ManageSocialPosts->value,
            PermissionName::ManageAutomationRules->value,
            PermissionName::ManageWorkflowLogs->value,
            PermissionName::ManageMarketing->value,
        ]);

        $roles[RoleName::WorkflowManager->value]->syncPermissions([
            PermissionName::ViewDashboard->value,
            PermissionName::ManageAutomationRules->value,
            PermissionName::ManageWorkflowLogs->value,
        ]);

        $this->seedRoleUser('Admin User', 'admin@plexus.test', RoleName::Admin);
        $this->seedRoleUser('Supplier User', 'supplier@plexus.test', RoleName::Supplier);
        $this->seedRoleUser('Buyer User', 'buyer@plexus.test', RoleName::Buyer);
        $this->seedRoleUser('Marketing Manager', 'marketing@plexus.test', RoleName::MarketingManager);
        $this->seedRoleUser('Workflow Manager', 'workflow@plexus.test', RoleName::WorkflowManager);
    }

    private function seedRoleUser(string $name, string $email, RoleName $role): void
    {
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        if (! $user->hasRole($role->value)) {
            $user->assignRole($role->value);
        }
    }
}
