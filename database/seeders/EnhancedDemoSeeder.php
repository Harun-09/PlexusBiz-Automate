<?php

namespace Database\Seeders;

use App\Domains\CRM\Enums\InteractionType;
use App\Domains\CRM\Enums\LeadStatus;
use App\Domains\CRM\Models\Customer;
use App\Domains\CRM\Models\CustomerSegment;
use App\Domains\CRM\Models\Lead;
use App\Domains\CRM\Services\CustomerProfileService;
use App\Domains\CRM\Services\InteractionLogger;
use App\Domains\ECommerce\Enums\CartStatus;
use App\Domains\ECommerce\Enums\InvoiceStatus;
// OrderItemStatus doesn't exist - using OrderStatus
use App\Domains\ECommerce\Enums\OrderStatus;
use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Enums\SupplierStatus;
use App\Domains\ECommerce\Models\Cart;
use App\Domains\ECommerce\Models\Category;
use App\Domains\ECommerce\Models\Invoice;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\PricingTier;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Supplier;
use App\Domains\ECommerce\Services\CheckoutService;
use App\Domains\ECommerce\Services\InventoryService;
use App\Domains\ECommerce\Services\InvoicePdfService;
use App\Domains\Marketing\Enums\CampaignStatus;
use App\Domains\Marketing\Enums\CampaignType;
use App\Domains\Marketing\Enums\MessageChannel;
use App\Domains\Marketing\Models\Campaign;
use App\Domains\Marketing\Models\CampaignTemplate;
use App\Domains\Notifications\Models\Message;
use App\Domains\Social\Enums\SocialPlatform;
use App\Domains\Social\Enums\SocialPostStatus;
use App\Domains\Social\Models\SocialAccount;
use App\Domains\Social\Models\SocialPost;
use App\Domains\Support\Enums\SupportFaqStatus;
use App\Domains\Support\Enums\TicketPriority;
use App\Domains\Support\Enums\TicketStatus;
use App\Domains\Support\Models\SupportFaq;
use App\Domains\Support\Models\SupportMessage;
use App\Domains\Support\Models\SupportTicket;
use App\Domains\Workflow\Enums\AutomationRuleStatus;
use App\Domains\Workflow\Models\AutomationRule;
use App\Domains\Workflow\Models\WorkflowLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EnhancedDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAdditionalUsers();
        $this->seedAdditionalSuppliers();
        $this->seedCategories();
        $this->seedProducts();
        $this->seedSampleOrders();
        $this->seedAdditionalLeads();
        $this->seedAdditionalSegments();
        $this->seedAdditionalCampaigns();
        $this->seedAdditionalSocialPosts();
        $this->seedAdditionalFaqs();
        $this->seedSampleTickets();
        $this->seedMessages();
        $this->seedWorkflowLogs();
    }

    private function seedAdditionalUsers(): void
    {
        // Additional buyers
        $buyers = [
            ['name' => 'Acme Corp Buyer', 'email' => 'buyer2@acme.test'],
            ['name' => 'Global Trade Ltd', 'email' => 'buyer3@global.test'],
            ['name' => 'Metro Industries', 'email' => 'buyer4@metro.test'],
        ];

        foreach ($buyers as $buyer) {
            User::firstOrCreate(
                ['email' => $buyer['email']],
                [
                    'name' => $buyer['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            )->assignRole('buyer');
        }

        // Additional suppliers
        $suppliers = [
            ['name' => 'Dhaka Tools Ltd', 'email' => 'supplier2@dhakatools.test', 'company' => 'Dhaka Tools & Equipment'],
            ['name' => 'Bangladesh Textiles', 'email' => 'supplier3@bdtex.test', 'company' => 'BD Textile Mills'],
        ];

        foreach ($suppliers as $supplier) {
            $user = User::firstOrCreate(
                ['email' => $supplier['email']],
                [
                    'name' => $supplier['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            )->assignRole('supplier');

            Supplier::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'company_name' => $supplier['company'],
                    'slug' => \Illuminate\Support\Str::slug($supplier['company']),
                    'status' => SupplierStatus::Approved,
                    'contact_email' => $supplier['email'],
                    'phone' => '+88017' . rand(10000000, 99999999),
                    'approved_at' => now(),
                ]
            );
        }
    }

    private function seedAdditionalSuppliers(): void
    {
        // Supplier with pending status for approval demo
        $pendingUser = User::firstOrCreate(
            ['email' => 'pending@supplier.test'],
            [
                'name' => 'New Supplier Inc',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        )->assignRole('supplier');

        Supplier::firstOrCreate(
            ['user_id' => $pendingUser->id],
            [
                'company_name' => 'New Supplier Inc',
                'slug' => 'new-supplier-inc',
                'status' => SupplierStatus::Pending,
                'contact_email' => 'pending@supplier.test',
                'phone' => '+8801712345678',
            ]
        );
    }

    private function seedCategories(): void
    {
        $categories = [
            ['name' => 'Office Supplies', 'slug' => 'office-supplies'],
            ['name' => 'Safety Equipment', 'slug' => 'safety-equipment'],
            ['name' => 'Packaging Materials', 'slug' => 'packaging-materials'],
            ['name' => 'Electronics & IT', 'slug' => 'electronics-it'],
            ['name' => 'Cleaning Supplies', 'slug' => 'cleaning-supplies'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name' => $cat['name'],
                    'status' => 'active',
                    'description' => 'B2B wholesale ' . strtolower($cat['name']),
                ]
            );
        }
    }

    private function seedProducts(): void
    {
        $supplier = Supplier::whereHas('user', fn ($q) => $q->where('email', 'supplier@plexus.test'))->first();
        $categories = Category::all()->keyBy('slug');

        $products = [
            [
                'sku' => 'PX-SAFETY-001',
                'name' => 'Industrial Safety Helmet',
                'category' => 'safety-equipment',
                'price' => '450.00',
                'moq' => 10,
                'stock' => 500,
            ],
            [
                'sku' => 'PX-SAFETY-002',
                'name' => 'Safety Gloves (Pack of 12)',
                'category' => 'safety-equipment',
                'price' => '1200.00',
                'moq' => 5,
                'stock' => 200,
            ],
            [
                'sku' => 'PX-OFFICE-001',
                'name' => 'A4 Copy Paper (Box of 10 reams)',
                'category' => 'office-supplies',
                'price' => '3500.00',
                'moq' => 10,
                'stock' => 1000,
            ],
            [
                'sku' => 'PX-OFFICE-002',
                'name' => 'Ergonomic Office Chair',
                'category' => 'office-supplies',
                'price' => '8500.00',
                'moq' => 2,
                'stock' => 50,
            ],
            [
                'sku' => 'PX-PACK-001',
                'name' => 'Cardboard Box 18x18x18 (Bundle of 50)',
                'category' => 'packaging-materials',
                'price' => '2800.00',
                'moq' => 5,
                'stock' => 300,
            ],
            [
                'sku' => 'PX-PACK-002',
                'name' => 'Bubble Wrap Roll 50m',
                'category' => 'packaging-materials',
                'price' => '850.00',
                'moq' => 10,
                'stock' => 150,
            ],
            [
                'sku' => 'PX-IT-001',
                'name' => 'Wireless Mouse (Bulk 20 units)',
                'category' => 'electronics-it',
                'price' => '12000.00',
                'moq' => 2,
                'stock' => 80,
            ],
            [
                'sku' => 'PX-CLEAN-001',
                'name' => 'Industrial Floor Cleaner 5L',
                'category' => 'cleaning-supplies',
                'price' => '650.00',
                'moq' => 20,
                'stock' => 400,
            ],
        ];

        foreach ($products as $prod) {
            $product = Product::firstOrCreate(
                ['sku' => $prod['sku']],
                [
                    'supplier_id' => $supplier->id,
                    'category_id' => $categories[$prod['category']]?->id,
                    'name' => $prod['name'],
                    'slug' => \Illuminate\Support\Str::slug($prod['name']),
                    'description' => 'High-quality B2B wholesale ' . $prod['name'],
                    'base_price' => $prod['price'],
                    'moq' => $prod['moq'],
                    'status' => ProductStatus::Active,
                    'published_at' => now(),
                ]
            );

            // Add bulk pricing tiers
            PricingTier::firstOrCreate(
                ['product_id' => $product->id, 'min_quantity' => $prod['moq'] * 2],
                ['unit_price' => round($prod['price'] * 0.9, 2)]
            );
            PricingTier::firstOrCreate(
                ['product_id' => $product->id, 'min_quantity' => $prod['moq'] * 5],
                ['unit_price' => round($prod['price'] * 0.8, 2)]
            );

            // Seed inventory
            if ($product->inventoryMovements()->doesntExist()) {
                app(InventoryService::class)->stockIn($product, $prod['stock'], $supplier->user, 'Initial stock');
            }
        }
    }

    private function seedSampleOrders(): void
    {
        $buyer = User::where('email', 'buyer@plexus.test')->first();
        $products = Product::where('status', ProductStatus::Active)->take(3)->get();
        $customer = app(CustomerProfileService::class)->ensureForUser($buyer);

        // Create 3 sample completed orders
        for ($i = 1; $i <= 3; $i++) {
            $order = Order::firstOrCreate(
                ['order_number' => 'ORD-2024-' . str_pad($i, 4, '0', STR_PAD_LEFT)],
                [
                    'buyer_id' => $buyer->id,
                    'customer_id' => $customer->id,
                    'status' => OrderStatus::Completed,
                    'subtotal' => 25000 * $i,
                    'tax_total' => 2500 * $i,
                    'shipping_total' => 500,
                    'discount_total' => 0,
                    'grand_total' => 28000 * $i,
                    'currency' => 'BDT',
                    'placed_at' => now()->subDays($i * 7),
                ]
            );

            // Add order items
            foreach ($products as $index => $product) {
                $order->items()->firstOrCreate(
                    ['product_id' => $product->id],
                    [
                        'supplier_id' => $product->supplier_id,
                        'product_name' => $product->name,
                        'sku' => $product->sku,
                        'quantity' => $product->moq,
                        'unit_price' => $product->base_price,
                        'total' => $product->base_price * $product->moq,
                        'status' => OrderStatus::Completed,
                    ]
                );
            }

            // Create invoice
            $invoice = Invoice::firstOrCreate(
                ['order_id' => $order->id],
                [
                    'invoice_number' => 'INV-2024-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                    'status' => InvoiceStatus::Paid,
                    'subtotal' => $order->subtotal,
                    'tax_total' => $order->tax_total,
                    'total' => $order->grand_total,
                    'issued_at' => $order->placed_at,
                    'due_at' => $order->placed_at->copy()->addDays(30),
                ]
            );

            // Generate PDF for invoice
            try {
                app(InvoicePdfService::class)->generatePdf($invoice);
            } catch (\Exception $e) {
                // Silently fail if PDF generation fails during seeding
            }

            // Log interaction
            if ($i === 1) {
                app(InteractionLogger::class)->record(
                    customer: $customer,
                    type: InteractionType::Order,
                    summary: "Sample order {$order->order_number} placed",
                    related: $order,
                    actor: $buyer,
                );
            }
        }
    }

    private function seedAdditionalLeads(): void
    {
        $marketing = User::where('email', 'marketing@plexus.test')->first();
        $leads = [
            [
                'email' => 'procurement@globalsourcing.test',
                'company' => 'Global Sourcing Co',
                'name' => 'Ahmed Khan',
                'status' => LeadStatus::New,
                'value' => 500000,
            ],
            [
                'email' => 'buyer@megacorp.test',
                'company' => 'MegaCorp Industries',
                'name' => 'Sarah Johnson',
                'status' => LeadStatus::Contacted,
                'value' => 1250000,
            ],
            [
                'email' => 'orders@techdistributors.test',
                'company' => 'Tech Distributors Ltd',
                'name' => 'Raj Patel',
                'status' => LeadStatus::Qualified,
                'value' => 750000,
            ],
        ];

        foreach ($leads as $lead) {
            Lead::firstOrCreate(
                ['email' => $lead['email']],
                [
                    'assigned_user_id' => $marketing->id,
                    'source' => ['website', 'referral', 'trade_show'][rand(0, 2)],
                    'status' => $lead['status'],
                    'company_name' => $lead['company'],
                    'contact_name' => $lead['name'],
                    'phone' => '+88017' . rand(10000000, 99999999),
                    'value' => $lead['value'],
                    'notes' => 'Interested in bulk supply agreement',
                    'next_follow_up_at' => now()->addDays(rand(1, 7)),
                ]
            );
        }
    }

    private function seedAdditionalSegments(): void
    {
        $segments = [
            ['slug' => 'vip-enterprise', 'name' => 'VIP Enterprise', 'tags' => ['enterprise', 'vip']],
            ['slug' => 'regular-buyers', 'name' => 'Regular Buyers', 'tags' => ['regular', 'loyal']],
            ['slug' => 'new-prospects', 'name' => 'New Prospects', 'tags' => ['new', 'prospect']],
        ];

        foreach ($segments as $seg) {
            CustomerSegment::firstOrCreate(
                ['slug' => $seg['slug']],
                [
                    'name' => $seg['name'],
                    'status' => 'active',
                    'description' => 'Segment for ' . strtolower($seg['name']),
                    'filters_json' => ['tags' => $seg['tags']],
                ]
            );
        }
    }

    private function seedAdditionalCampaigns(): void
    {
        $marketing = User::where('email', 'marketing@plexus.test')->first();

        $campaigns = [
            [
                'slug' => 'summer-sale-2024',
                'name' => 'Summer Sale 2024',
                'type' => CampaignType::Email,
                'status' => CampaignStatus::Scheduled,
            ],
            [
                'slug' => 'new-product-launch',
                'name' => 'New Product Launch',
                'type' => CampaignType::Email,
                'status' => CampaignStatus::Running,
            ],
            [
                'slug' => 'loyalty-reward',
                'name' => 'Loyalty Reward Program',
                'type' => CampaignType::Sms,
                'status' => CampaignStatus::Completed,
            ],
        ];

        foreach ($campaigns as $camp) {
            Campaign::firstOrCreate(
                ['slug' => $camp['slug']],
                [
                    'created_by' => $marketing->id,
                    'name' => $camp['name'],
                    'type' => $camp['type'],
                    'status' => $camp['status'],
                    'scheduled_at' => now()->addDays(rand(1, 30)),
                    'segment_filters_json' => ['tags' => ['wholesale']],
                ]
            );
        }
    }

    private function seedAdditionalSocialPosts(): void
    {
        $marketing = User::where('email', 'marketing@plexus.test')->first();

        $posts = [
            [
                'platform' => SocialPlatform::Facebook,
                'content' => '🚀 Exciting news! We have expanded our B2B catalog with 50+ new industrial products. Check out our latest offerings! #B2B #Wholesale',
                'status' => SocialPostStatus::Published,
                'scheduled' => now()->subDays(2),
            ],
            [
                'platform' => SocialPlatform::Instagram,
                'content' => 'Behind the scenes at our partner facilities. Quality control is our priority. #QualityFirst #B2B',
                'status' => SocialPostStatus::Scheduled,
                'scheduled' => now()->addDays(1),
            ],
            [
                'platform' => SocialPlatform::Facebook,
                'content' => 'Summer bulk pricing now available! Save up to 20% on orders over 100 units. Contact us today! 💼',
                'status' => SocialPostStatus::Draft,
                'scheduled' => now()->addDays(3),
            ],
        ];

        foreach ($posts as $i => $post) {
            SocialPost::firstOrCreate(
                [
                    'platform' => $post['platform']->value,
                    'content' => $post['content'],
                ],
                [
                    'campaign_id' => null,
                    'social_account_id' => null,
                    'scheduled_at' => $post['scheduled'],
                    'status' => $post['status'],
                    'published_at' => $post['status'] === SocialPostStatus::Published ? $post['scheduled'] : null,
                    'likes_count' => $post['status'] === SocialPostStatus::Published ? rand(50, 500) : 0,
                    'comments_count' => $post['status'] === SocialPostStatus::Published ? rand(5, 50) : 0,
                ]
            );
        }
    }

    private function seedAdditionalFaqs(): void
    {
        $faqs = [
            [
                'question' => 'What is the minimum order quantity (MOQ)?',
                'answer' => 'MOQ varies by product. Each product page displays the specific minimum order quantity. Bulk pricing tiers are available for larger quantities.',
                'keywords' => ['moq', 'minimum order', 'bulk', 'quantity'],
            ],
            [
                'question' => 'How do I become a verified supplier?',
                'answer' => 'Register as a supplier, complete your company profile, and submit for admin approval. Our team will review and approve within 2-3 business days.',
                'keywords' => ['supplier', 'verified', 'approval', 'become supplier'],
            ],
            [
                'question' => 'What payment methods are accepted?',
                'answer' => 'We accept bank transfers, credit cards, and mobile banking (bKash, Nagad). Net-30 terms available for approved enterprise customers.',
                'keywords' => ['payment', 'pay', 'bkash', 'bank transfer', 'credit card'],
            ],
            [
                'question' => 'How can I track my order?',
                'answer' => 'Once your order ships, you will receive a tracking number via email. You can also view order status in your buyer dashboard.',
                'keywords' => ['track', 'tracking', 'order status', 'where is my order'],
            ],
        ];

        foreach ($faqs as $faq) {
            SupportFaq::firstOrCreate(
                ['question' => $faq['question']],
                [
                    'answer' => $faq['answer'],
                    'keywords_json' => $faq['keywords'],
                    'status' => SupportFaqStatus::Active,
                    'priority' => rand(10, 50),
                ]
            );
        }
    }

    private function seedSampleTickets(): void
    {
        $buyer = User::where('email', 'buyer@plexus.test')->first();
        $admin = User::where('email', 'admin@plexus.test')->first();

        $tickets = [
            [
                'number' => 'TKT-' . now()->format('Y') . '-001',
                'subject' => 'Order delivery delay inquiry',
                'status' => TicketStatus::Resolved,
                'priority' => TicketPriority::Normal,
            ],
            [
                'number' => 'TKT-' . now()->format('Y') . '-002',
                'subject' => 'Product quality question',
                'status' => TicketStatus::Open,
                'priority' => TicketPriority::High,
            ],
        ];

        foreach ($tickets as $t) {
            $ticket = SupportTicket::firstOrCreate(
                ['ticket_number' => $t['number']],
                [
                    'requester_id' => $buyer->id,
                    'channel' => 'web',
                    'subject' => $t['subject'],
                    'description' => 'Sample ticket for demo purposes',
                    'priority' => $t['priority'],
                    'status' => $t['status'],
                    'assigned_to' => $admin->id,
                ]
            );

            // Add message to ticket
            SupportMessage::firstOrCreate(
                [
                    'support_ticket_id' => $ticket->id,
                    'sender_id' => $buyer->id,
                ],
                [
                    'sender_type' => 'customer',
                    'visibility' => 'public',
                    'message' => 'I need help with this issue. Please assist.',
                ]
            );
        }
    }

    private function seedMessages(): void
    {
        $admin = User::where('email', 'admin@plexus.test')->first();
        $buyer = User::where('email', 'buyer@plexus.test')->first();
        $supplier = User::where('email', 'supplier@plexus.test')->first();

        $messages = [
            [
                'sender' => $admin,
                'receiver' => $buyer,
                'subject' => 'Welcome to PlexusBiz!',
                'body' => 'Thank you for joining PlexusBiz. Your B2B buyer account is now active.',
                'channel' => MessageChannel::System,
            ],
            [
                'sender' => $supplier,
                'receiver' => $buyer,
                'subject' => 'New products available',
                'body' => 'We have added new safety equipment to our catalog. Check them out!',
                'channel' => MessageChannel::System,
            ],
            [
                'sender' => $admin,
                'receiver' => $supplier,
                'subject' => 'Supplier verification complete',
                'body' => 'Your supplier account has been verified and approved.',
                'channel' => MessageChannel::System,
            ],
        ];

        foreach ($messages as $msg) {
            Message::firstOrCreate(
                [
                    'sender_id' => $msg['sender']->id,
                    'receiver_id' => $msg['receiver']->id,
                    'subject' => $msg['subject'],
                ],
                [
                    'body' => $msg['body'],
                    'channel' => $msg['channel'],
                    'status' => \App\Domains\Marketing\Enums\MessageStatus::Sent,
                    'sent_at' => now()->subDays(rand(1, 7)),
                ]
            );
        }
    }

    private function seedWorkflowLogs(): void
    {
        $rules = AutomationRule::take(3)->get();

        foreach ($rules as $rule) {
            WorkflowLog::firstOrCreate(
                [
                    'rule_id' => $rule->id,
                    'trigger_event' => $rule->trigger_event,
                    'executed_at' => now()->subDays(rand(1, 5)),
                ],
                [
                    'payload' => ['demo' => true, 'order_id' => rand(1, 10)],
                    'status' => \App\Domains\Workflow\Enums\WorkflowLogStatus::Success,
                    'result' => ['executed' => true, 'actions' => 2],
                ]
            );
        }
    }
}
