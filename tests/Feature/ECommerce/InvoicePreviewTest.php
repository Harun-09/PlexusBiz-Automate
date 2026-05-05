<?php

namespace Tests\Feature\ECommerce;

use App\Domains\ECommerce\Enums\InvoiceStatus;
use App\Domains\ECommerce\Enums\OrderStatus;
use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Enums\SupplierStatus;
use App\Domains\ECommerce\Models\Category;
use App\Domains\ECommerce\Models\Invoice;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\OrderItem;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Supplier;
use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InvoicePreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_preview_and_download_invoice_pdf(): void
    {
        $buyer = User::factory()->create();
        $buyer->assignRole(Role::findOrCreate(RoleName::Buyer->value));

        [$invoice] = $this->createInvoiceFixture($buyer);

        $preview = $this->actingAs($buyer)->get("/invoices/{$invoice->id}/preview");

        $preview->assertOk();
        $this->assertStringStartsWith('application/pdf', (string) $preview->headers->get('content-type'));

        $download = $this->actingAs($buyer)->get("/invoices/{$invoice->id}/download");

        $download->assertOk();
        $this->assertStringStartsWith('application/pdf', (string) $download->headers->get('content-type'));
        $this->assertStringContainsString('attachment', (string) $download->headers->get('content-disposition'));
    }

    /**
     * @return array{0: Invoice, 1: Order, 2: Product, 3: Supplier}
     */
    private function createInvoiceFixture(User $buyer): array
    {
        $supplierUser = User::factory()->create();
        $supplier = Supplier::create([
            'user_id' => $supplierUser->id,
            'company_name' => 'Demo Supplier',
            'slug' => 'demo-supplier-'.Str::lower(Str::random(8)),
            'status' => SupplierStatus::Approved,
            'contact_email' => $supplierUser->email,
            'approved_at' => now(),
        ]);

        $category = Category::create([
            'name' => 'Equipment',
            'slug' => 'equipment-'.Str::lower(Str::random(8)),
            'status' => 'active',
        ]);

        $product = Product::create([
            'supplier_id' => $supplier->id,
            'category_id' => $category->id,
            'sku' => 'SKU-'.Str::upper(Str::random(8)),
            'name' => 'Industrial Pump',
            'slug' => 'industrial-pump-'.Str::lower(Str::random(8)),
            'description' => 'Demo invoice item',
            'base_price' => '1250.00',
            'moq' => 2,
            'stock_quantity' => 25,
            'reserved_quantity' => 0,
            'status' => ProductStatus::Active,
            'published_at' => now(),
        ]);

        $order = Order::create([
            'buyer_id' => $buyer->id,
            'order_number' => 'ORD-'.Str::upper(Str::random(10)),
            'status' => OrderStatus::Completed,
            'subtotal' => '2500.00',
            'tax_total' => '0.00',
            'shipping_total' => '0.00',
            'discount_total' => '0.00',
            'grand_total' => '2500.00',
            'currency' => 'BDT',
            'placed_at' => now(),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'supplier_id' => $supplier->id,
            'product_name' => $product->name,
            'sku' => $product->sku,
            'quantity' => 2,
            'unit_price' => '1250.00',
            'total' => '2500.00',
            'status' => 'completed',
        ]);

        $invoice = Invoice::create([
            'order_id' => $order->id,
            'invoice_number' => 'INV-'.Str::upper(Str::random(8)),
            'status' => InvoiceStatus::Issued,
            'subtotal' => '2500.00',
            'tax_total' => '0.00',
            'total' => '2500.00',
            'issued_at' => now(),
            'due_at' => now()->addDays(30),
        ]);

        return [$invoice, $order, $product, $supplier];
    }
}
