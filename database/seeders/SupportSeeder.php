<?php

namespace Database\Seeders;

use App\Domains\Support\Enums\SupportFaqStatus;
use App\Domains\Support\Models\SupportFaq;
use Illuminate\Database\Seeder;

class SupportSeeder extends Seeder
{
    public function run(): void
    {
        SupportFaq::updateOrCreate(
            ['question' => 'How can I check order shipping status?'],
            [
                'answer' => 'Your order shipping status is available from the Orders workspace. If the supplier has not updated tracking yet, a support ticket will notify the supplier.',
                'keywords_json' => ['shipping', 'shipment', 'tracking', 'delivery', 'eta', 'order status'],
                'status' => SupportFaqStatus::Active,
                'priority' => 10,
            ],
        );

        SupportFaq::updateOrCreate(
            ['question' => 'How do I request a supplier quote?'],
            [
                'answer' => 'Open the marketplace product and submit an RFQ with quantity, target price, and delivery notes. The supplier will respond from their order workspace.',
                'keywords_json' => ['rfq', 'quote', 'bulk price', 'supplier quote', 'request quote'],
                'status' => SupportFaqStatus::Active,
                'priority' => 20,
            ],
        );
    }
}
