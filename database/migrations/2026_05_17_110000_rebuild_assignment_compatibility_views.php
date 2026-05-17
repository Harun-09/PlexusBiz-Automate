<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->dropCompatibilityViews();

        $definitions = [
            [
                'view' => 'product_pricing_tiers',
                'sources' => ['pricing_tiers'],
                'select' => 'SELECT id, product_id, min_quantity, unit_price, created_at, updated_at FROM pricing_tiers',
            ],
            [
                'view' => 'inventory',
                'sources' => ['inventory_movements'],
                'select' => 'SELECT id, product_id, supplier_id, quantity_after AS stock_quantity, quantity_before, quantity, type, reason, reference_type, reference_id, metadata, created_at, updated_at FROM inventory_movements',
            ],
            [
                'view' => 'lead_interactions',
                'sources' => ['interactions'],
                'select' => 'SELECT id, customer_id, user_id, type, direction, related_type, related_id, summary, payload, occurred_at, created_at, updated_at FROM interactions',
            ],
            [
                'view' => 'ticket_replies',
                'sources' => ['support_messages'],
                'select' => 'SELECT id, support_ticket_id, sender_id, sender_type, visibility, message, payload_json, created_at, updated_at FROM support_messages',
            ],
            [
                'view' => 'faqs',
                'sources' => ['support_faqs'],
                'select' => 'SELECT id, question, answer, keywords_json, status, priority, created_at, updated_at, deleted_at FROM support_faqs',
            ],
            [
                'view' => 'email_logs',
                'sources' => ['campaign_logs'],
                'select' => "SELECT id, campaign_id, campaign_recipient_id, customer_id, channel, status, provider, payload, response, error, sent_at, created_at, updated_at FROM campaign_logs WHERE channel = 'email'",
            ],
            [
                'view' => 'sms_logs',
                'sources' => ['campaign_logs'],
                'select' => "SELECT id, campaign_id, campaign_recipient_id, customer_id, channel, status, provider, payload, response, error, sent_at, created_at, updated_at FROM campaign_logs WHERE channel = 'sms'",
            ],
        ];

        foreach ($definitions as $definition) {
            if (! $this->hasAllSourceTables($definition['sources'])) {
                continue;
            }

            $select = $this->applySourceTablePrefix($definition['select'], $definition['sources']);

            DB::statement("CREATE VIEW {$definition['view']} AS {$select}");
        }
    }

    public function down(): void
    {
        $this->dropCompatibilityViews();
    }

    private function dropCompatibilityViews(): void
    {
        foreach ([
            'product_pricing_tiers',
            'inventory',
            'lead_interactions',
            'ticket_replies',
            'faqs',
            'email_logs',
            'sms_logs',
        ] as $view) {
            DB::statement("DROP VIEW IF EXISTS {$view}");
        }
    }

    /**
     * @param  array<int, string>  $sources
     */
    private function applySourceTablePrefix(string $sql, array $sources): string
    {
        $prefix = DB::connection()->getTablePrefix();

        if ($prefix === '') {
            return $sql;
        }

        foreach ($sources as $source) {
            $pattern = '/\b'.preg_quote($source, '/').'\b/i';
            $replacement = $prefix.$source;

            $sql = preg_replace($pattern, $replacement, $sql) ?? $sql;
        }

        return $sql;
    }

    /**
     * @param  array<int, string>  $tables
     */
    private function hasAllSourceTables(array $tables): bool
    {
        foreach ($tables as $table) {
            if (! Schema::hasTable($table)) {
                return false;
            }
        }

        return true;
    }
};
