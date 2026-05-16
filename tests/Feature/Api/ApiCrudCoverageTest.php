<?php

namespace Tests\Feature\Api;

use App\Domains\ECommerce\Models\Supplier;
use App\Domains\Marketing\Models\Campaign;
use App\Domains\Social\Models\SocialAccount;
use App\Domains\Workflow\Models\AutomationRule;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiCrudCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_crud_products_customers_and_orders_via_api(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'admin@plexus.test')->firstOrFail();
        $buyer = User::where('email', 'buyer@plexus.test')->firstOrFail();
        $supplier = Supplier::query()->firstOrFail();

        Sanctum::actingAs($admin);

        $productPayload = [
            'supplier_id' => $supplier->id,
            'sku' => 'API-PX-'.Str::upper(Str::random(8)),
            'name' => 'API Coverage Product',
            'description' => 'Created from API CRUD coverage test',
            'base_price' => 950.5,
            'moq' => 3,
            'stock_quantity' => 150,
            'reserved_quantity' => 5,
            'status' => 'active',
        ];

        $productResponse = $this->postJson('/api/v1/products', $productPayload)
            ->assertCreated()
            ->assertJsonPath('data.name', 'API Coverage Product')
            ->assertJsonPath('data.status', 'active')
            ->json('data');

        $productId = $productResponse['id'];

        $this->patchJson('/api/v1/products/'.$productId, [
            'name' => 'API Coverage Product Updated',
            'status' => 'inactive',
            'stock_quantity' => 80,
        ])
            ->assertOk()
            ->assertJsonPath('data.name', 'API Coverage Product Updated')
            ->assertJsonPath('data.status', 'inactive')
            ->assertJsonPath('data.stock_quantity', 80);

        $this->deleteJson('/api/v1/products/'.$productId)
            ->assertOk()
            ->assertJsonPath('message', 'Product deleted successfully');

        $this->assertSoftDeleted('products', [
            'id' => $productId,
        ]);

        $customerPayload = [
            'contact_name' => 'API Customer',
            'company_name' => 'API Buyer LLC',
            'email' => 'api.customer.'.Str::lower(Str::random(8)).'@example.test',
            'phone' => '+8801700000000',
            'business_type' => 'Wholesale',
            'address' => [
                'line_1' => 'Road 12',
                'city' => 'Dhaka',
                'country' => 'Bangladesh',
            ],
            'status' => 'active',
            'lifecycle_stage' => 'prospect',
            'tags' => ['priority', 'b2b'],
            'notes' => 'Created through API test.',
        ];

        $customerResponse = $this->postJson('/api/v1/customers', $customerPayload)
            ->assertCreated()
            ->assertJsonPath('data.contact_name', 'API Customer')
            ->assertJsonPath('data.status', 'active')
            ->json('data');

        $customerId = $customerResponse['id'];

        $this->patchJson('/api/v1/customers/'.$customerId, [
            'status' => 'inactive',
            'lifecycle_stage' => 'at_risk',
            'tags' => 'priority, follow-up',
        ])
            ->assertOk()
            ->assertJsonPath('data.status', 'inactive')
            ->assertJsonPath('data.lifecycle_stage', 'at_risk');

        $this->deleteJson('/api/v1/customers/'.$customerId)
            ->assertOk()
            ->assertJsonPath('message', 'Customer deleted successfully');

        $this->assertSoftDeleted('customers', [
            'id' => $customerId,
        ]);

        $orderResponse = $this->postJson('/api/v1/orders', [
            'buyer_id' => $buyer->id,
            'status' => 'pending',
            'payment_status' => 'pending',
            'subtotal' => 1000,
            'tax_total' => 100,
            'shipping_total' => 50,
            'discount_total' => 25,
            'currency' => 'BDT',
            'placed_at' => now()->toIso8601String(),
        ])
            ->assertCreated()
            ->assertJsonPath('data.status', 'pending')
            ->json('data');

        $orderId = $orderResponse['id'];

        $this->patchJson('/api/v1/orders/'.$orderId, [
            'status' => 'confirmed',
            'payment_status' => 'processing',
            'grand_total' => 1125,
        ])
            ->assertOk()
            ->assertJsonPath('data.status', 'confirmed')
            ->assertJsonPath('data.payment_status', 'processing');

        $this->deleteJson('/api/v1/orders/'.$orderId)
            ->assertOk()
            ->assertJsonPath('message', 'Order deleted successfully');

        $this->assertSoftDeleted('orders', [
            'id' => $orderId,
        ]);
    }

    public function test_marketing_manager_can_crud_social_posts_and_workflow_logs_via_api(): void
    {
        $this->seed(DatabaseSeeder::class);

        $marketing = User::where('email', 'marketing@plexus.test')->firstOrFail();
        $campaign = Campaign::query()->firstOrFail();
        $account = SocialAccount::query()->firstOrFail();
        $rule = AutomationRule::query()->first();

        Sanctum::actingAs($marketing);

        $socialPostResponse = $this->postJson('/api/v1/social-posts', [
            'campaign_id' => $campaign->id,
            'social_account_id' => $account->id,
            'platform' => 'facebook',
            'content' => 'API CRUD social post',
            'media_url' => 'https://example.test/media/post.jpg',
            'scheduled_at' => now()->addHour()->toIso8601String(),
            'status' => 'scheduled',
            'likes_count' => 0,
            'comments_count' => 0,
            'shares_count' => 0,
        ])
            ->assertCreated()
            ->assertJsonPath('data.status', 'scheduled')
            ->json('data');

        $socialPostId = $socialPostResponse['id'];

        $this->patchJson('/api/v1/social-posts/'.$socialPostId, [
            'status' => 'published',
            'published_at' => now()->toIso8601String(),
            'likes_count' => 12,
            'comments_count' => 3,
        ])
            ->assertOk()
            ->assertJsonPath('data.status', 'published')
            ->assertJsonPath('data.engagement.likes', 12)
            ->assertJsonPath('data.engagement.comments', 3);

        $this->deleteJson('/api/v1/social-posts/'.$socialPostId)
            ->assertOk()
            ->assertJsonPath('message', 'Social post deleted successfully');

        $this->assertSoftDeleted('social_posts', [
            'id' => $socialPostId,
        ]);

        $workflowLogResponse = $this->postJson('/api/v1/workflow-logs', [
            'rule_id' => $rule?->id,
            'trigger_event' => 'api.coverage.created',
            'payload' => [
                'source' => 'api-test',
                'entity' => 'workflow-log',
            ],
            'status' => 'success',
            'result' => [
                'action' => 'queued',
            ],
            'executed_at' => now()->toIso8601String(),
        ])
            ->assertCreated()
            ->assertJsonPath('data.trigger_event', 'api.coverage.created')
            ->assertJsonPath('data.status', 'success')
            ->json('data');

        $workflowLogId = $workflowLogResponse['id'];

        $this->patchJson('/api/v1/workflow-logs/'.$workflowLogId, [
            'status' => 'failed',
            'error' => 'Simulated failure from API coverage test.',
        ])
            ->assertOk()
            ->assertJsonPath('data.status', 'failed')
            ->assertJsonPath('data.error', 'Simulated failure from API coverage test.');

        $this->deleteJson('/api/v1/workflow-logs/'.$workflowLogId)
            ->assertOk()
            ->assertJsonPath('message', 'Workflow log deleted successfully');

        $this->assertDatabaseMissing('workflow_logs', [
            'id' => $workflowLogId,
        ]);
    }
}
