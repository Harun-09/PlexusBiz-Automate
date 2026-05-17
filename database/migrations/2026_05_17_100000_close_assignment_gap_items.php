<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table): void {
            if (! Schema::hasColumn('suppliers', 'logo_path')) {
                $table->string('logo_path')->nullable()->after('tax_number');
            }

            if (! Schema::hasColumn('suppliers', 'verification_document_path')) {
                $table->string('verification_document_path')->nullable()->after('logo_path');
            }

            if (! Schema::hasColumn('suppliers', 'verification_document_name')) {
                $table->string('verification_document_name')->nullable()->after('verification_document_path');
            }
        });

        Schema::table('products', function (Blueprint $table): void {
            if (! Schema::hasColumn('products', 'tags')) {
                $table->json('tags')->nullable()->after('description');
            }
        });

        if (! Schema::hasTable('rfq_responses')) {
            Schema::create('rfq_responses', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('rfq_id')->constrained('rfqs')->cascadeOnDelete();
                $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
                $table->foreignId('responded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->decimal('quoted_amount', 12, 2);
                $table->string('currency', 3)->default('BDT');
                $table->unsignedInteger('min_order_quantity')->nullable();
                $table->unsignedInteger('lead_time_days')->nullable();
                $table->timestamp('valid_until')->nullable()->index();
                $table->text('message')->nullable();
                $table->string('status', 32)->default('pending')->index();
                $table->timestamp('buyer_action_at')->nullable();
                $table->timestamps();
                $table->unique(['rfq_id', 'supplier_id']);
                $table->index(['supplier_id', 'status']);
                $table->index(['rfq_id', 'status']);
            });
        }

        if (! Schema::hasTable('social_campaigns')) {
            Schema::create('social_campaigns', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('name');
                $table->text('objective')->nullable();
                $table->string('status', 32)->default('draft')->index();
                $table->timestamp('start_at')->nullable()->index();
                $table->timestamp('end_at')->nullable();
                $table->decimal('budget', 12, 2)->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('content_calendar')) {
            Schema::create('content_calendar', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('social_post_id')->nullable()->constrained('social_posts')->nullOnDelete();
                $table->foreignId('social_campaign_id')->nullable()->constrained('social_campaigns')->nullOnDelete();
                $table->string('platform', 32)->index();
                $table->string('title')->nullable();
                $table->text('content')->nullable();
                $table->timestamp('scheduled_for')->nullable()->index();
                $table->string('status', 32)->default('draft')->index();
                $table->timestamp('published_at')->nullable()->index();
                $table->json('metadata')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('engagement_logs')) {
            Schema::create('engagement_logs', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('social_post_id')->nullable()->constrained('social_posts')->nullOnDelete();
                $table->foreignId('social_campaign_id')->nullable()->constrained('social_campaigns')->nullOnDelete();
                $table->string('platform', 32)->index();
                $table->string('metric_type', 32)->index();
                $table->unsignedInteger('metric_value')->default(0);
                $table->timestamp('recorded_at')->nullable()->index();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->unique(['social_post_id', 'metric_type']);
            });
        }

        $this->backfillSocialData();
        $this->createCompatibilityViews();
    }

    public function down(): void
    {
        $this->dropCompatibilityViews();

        Schema::dropIfExists('engagement_logs');
        Schema::dropIfExists('content_calendar');
        Schema::dropIfExists('social_campaigns');
        Schema::dropIfExists('rfq_responses');

        Schema::table('products', function (Blueprint $table): void {
            if (Schema::hasColumn('products', 'tags')) {
                $table->dropColumn('tags');
            }
        });

        Schema::table('suppliers', function (Blueprint $table): void {
            foreach (['verification_document_name', 'verification_document_path', 'logo_path'] as $column) {
                if (Schema::hasColumn('suppliers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    private function createCompatibilityViews(): void
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

            try {
                DB::statement("CREATE VIEW {$definition['view']} AS {$select}");
            } catch (\Throwable $exception) {
                // Keep migration compatible with existing databases where legacy source tables
                // were altered or dropped; compatibility views are best-effort only.
                continue;
            }
        }
    }

    private function backfillSocialData(): void
    {
        if (
            ! Schema::hasTable('social_posts')
            || ! Schema::hasTable('social_campaigns')
            || ! Schema::hasTable('content_calendar')
            || ! Schema::hasTable('engagement_logs')
        ) {
            return;
        }

        $posts = DB::table('social_posts')->select([
            'id',
            'campaign_id',
            'platform',
            'content',
            'scheduled_at',
            'status',
            'published_at',
            'likes_count',
            'comments_count',
            'shares_count',
            'reach_count',
            'clicks_count',
            'created_at',
            'updated_at',
        ])->get();

        foreach ($posts as $post) {
            $socialCampaignId = null;

            if ($post->campaign_id !== null) {
                DB::table('social_campaigns')->updateOrInsert(
                    ['campaign_id' => $post->campaign_id],
                    [
                        'name' => 'Campaign '.$post->campaign_id,
                        'status' => (string) $post->status,
                        'start_at' => $post->scheduled_at,
                        'end_at' => $post->published_at,
                        'metadata' => json_encode(['platform' => $post->platform]),
                        'updated_at' => $post->updated_at ?? now(),
                        'created_at' => $post->created_at ?? now(),
                    ],
                );

                $socialCampaignId = DB::table('social_campaigns')
                    ->where('campaign_id', $post->campaign_id)
                    ->value('id');
            }

            DB::table('content_calendar')->updateOrInsert(
                ['social_post_id' => $post->id],
                [
                    'social_campaign_id' => $socialCampaignId,
                    'platform' => (string) $post->platform,
                    'title' => mb_substr((string) $post->content, 0, 120),
                    'content' => (string) $post->content,
                    'scheduled_for' => $post->scheduled_at,
                    'status' => (string) $post->status,
                    'published_at' => $post->published_at,
                    'metadata' => json_encode(['source' => 'backfill']),
                    'updated_at' => $post->updated_at ?? now(),
                    'created_at' => $post->created_at ?? now(),
                ],
            );

            $metrics = [
                'likes' => (int) ($post->likes_count ?? 0),
                'comments' => (int) ($post->comments_count ?? 0),
                'shares' => (int) ($post->shares_count ?? 0),
                'reach' => (int) ($post->reach_count ?? 0),
                'clicks' => (int) ($post->clicks_count ?? 0),
            ];

            foreach ($metrics as $type => $value) {
                DB::table('engagement_logs')->updateOrInsert(
                    [
                        'social_post_id' => $post->id,
                        'metric_type' => $type,
                    ],
                    [
                        'social_campaign_id' => $socialCampaignId,
                        'platform' => (string) $post->platform,
                        'metric_value' => $value,
                        'recorded_at' => $post->updated_at ?? now(),
                        'metadata' => json_encode(['source' => 'backfill']),
                        'updated_at' => $post->updated_at ?? now(),
                        'created_at' => $post->created_at ?? now(),
                    ],
                );
            }
        }
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
     * @param array<int, string> $tables
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
