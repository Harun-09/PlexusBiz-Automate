<?php

namespace Tests\Feature\Marketing;

use App\Domains\CRM\Enums\CustomerLifecycleStage;
use App\Domains\CRM\Enums\CustomerStatus;
use App\Domains\CRM\Models\Customer;
use App\Domains\ECommerce\Enums\CartStatus;
use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Enums\SupplierStatus;
use App\Domains\ECommerce\Models\Category;
use App\Domains\ECommerce\Models\Cart;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Supplier;
use App\Domains\ECommerce\Services\CartService;
use App\Domains\ECommerce\Services\CheckoutService;
use App\Domains\Marketing\Jobs\ProcessAbandonedCartRemindersJob;
use App\Domains\Marketing\Mail\MarketingCampaignMail;
use App\Domains\Marketing\Enums\MessageChannel;
use App\Domains\Marketing\Models\CampaignTemplate;
use App\Domains\Marketing\Models\CampaignLog;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class MarketingAutomationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_registered_user_triggers_welcome_email(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'name' => 'New Buyer',
            'email' => 'buyer@example.test',
        ]);

        CampaignTemplate::create([
            'template_key' => 'new_customer_welcome',
            'channel' => MessageChannel::Email,
            'name' => 'Welcome',
            'subject' => 'Welcome to PlexusBiz, {{ customer_name }}',
            'body' => 'Hello {{ customer_name }}, thank you for joining PlexusBiz.',
            'variables' => ['customer_name', 'company_name'],
            'status' => 'active',
        ]);

        event(new Registered($user));

        Mail::assertSent(MarketingCampaignMail::class, function (MarketingCampaignMail $mail): bool {
            return $mail->subjectLine === 'Welcome to PlexusBiz, New Buyer';
        });

        $this->assertDatabaseHas('campaign_logs', [
            'channel' => 'email',
            'status' => 'sent',
        ]);
    }

    public function test_order_checkout_triggers_confirmation_email(): void
    {
        Mail::fake();

        $buyer = User::factory()->create();
        CampaignTemplate::create([
            'template_key' => 'order_confirmation',
            'channel' => MessageChannel::Email,
            'name' => 'Order Confirmation',
            'subject' => 'Order {{ order_number }} confirmed',
            'body' => 'Hello {{ customer_name }}, order {{ order_number }} has been confirmed.',
            'variables' => ['customer_name', 'order_number', 'invoice_url'],
            'status' => 'active',
        ]);
        $product = $this->product();
        app(CartService::class)->addItem($buyer, $product, 2);

        $order = app(CheckoutService::class)->checkout($buyer);

        Mail::assertSent(MarketingCampaignMail::class, function (MarketingCampaignMail $mail) use ($order): bool {
            return $mail->subjectLine === 'Order '.$order->order_number.' confirmed';
        });

        $this->assertDatabaseHas('campaign_logs', [
            'channel' => 'email',
            'status' => 'sent',
            'campaign_id' => null,
        ]);
    }

    public function test_abandoned_cart_job_sends_reminder_and_marks_cart_abandoned(): void
    {
        Mail::fake();

        $buyer = User::factory()->create();
        CampaignTemplate::create([
            'template_key' => 'abandoned_cart_reminder',
            'channel' => MessageChannel::Email,
            'name' => 'Abandoned Cart Reminder',
            'subject' => 'Complete your PlexusBiz order',
            'body' => 'Hello {{ customer_name }}, your cart is waiting: {{ abandoned_cart_url }}',
            'variables' => ['customer_name', 'abandoned_cart_url'],
            'status' => 'active',
        ]);
        $product = $this->product();
        app(CartService::class)->addItem($buyer, $product, 1);

        $cart = Cart::query()->where('user_id', $buyer->id)->firstOrFail();
        Cart::query()->whereKey($cart->id)->update([
            'updated_at' => now()->subDay()->subHour(),
        ]);

        app(ProcessAbandonedCartRemindersJob::class)->handle(
            app(\App\Domains\CRM\Services\CustomerProfileService::class),
            app(\App\Domains\Marketing\Services\MarketingTriggerService::class),
        );

        Mail::assertSent(MarketingCampaignMail::class, function (MarketingCampaignMail $mail): bool {
            return $mail->subjectLine === 'Complete your PlexusBiz order';
        });

        $this->assertSame(CartStatus::Abandoned, $cart->refresh()->status);
    }

    private function product(): Product
    {
        $supplierUser = User::factory()->create();
        $supplier = Supplier::create([
            'user_id' => $supplierUser->id,
            'company_name' => 'Supplier '.Str::random(8),
            'slug' => 'supplier-'.Str::random(12),
            'status' => SupplierStatus::Approved,
            'contact_email' => $supplierUser->email,
            'approved_at' => now(),
        ]);

        $category = Category::create([
            'name' => 'Equipment',
            'slug' => 'equipment-'.Str::random(12),
            'status' => 'active',
        ]);

        return Product::create([
            'supplier_id' => $supplier->id,
            'category_id' => $category->id,
            'sku' => 'SKU-'.Str::upper(Str::random(10)),
            'name' => 'Industrial Pump',
            'slug' => 'industrial-pump-'.Str::random(12),
            'base_price' => '100.00',
            'moq' => 1,
            'stock_quantity' => 50,
            'reserved_quantity' => 0,
            'status' => ProductStatus::Active,
            'published_at' => now(),
        ]);
    }
}
