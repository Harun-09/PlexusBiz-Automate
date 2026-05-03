<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_accounts', function (Blueprint $table): void {
            $table->id();
            $table->string('platform', 32)->index();
            $table->string('name');
            $table->string('handle')->nullable();
            $table->string('status', 32)->default('active')->index();
            $table->text('credentials_json')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('social_posts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('social_account_id')->nullable()->constrained()->nullOnDelete();
            $table->string('platform', 32)->index();
            $table->text('content');
            $table->string('media_url')->nullable();
            $table->timestamp('scheduled_at')->nullable()->index();
            $table->string('status', 32)->default('draft')->index();
            $table->string('external_post_id')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->text('failure_reason')->nullable();
            $table->unsignedInteger('likes_count')->default(0);
            $table->unsignedInteger('comments_count')->default(0);
            $table->unsignedInteger('shares_count')->default(0);
            $table->unsignedInteger('reach_count')->default(0);
            $table->unsignedInteger('clicks_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['platform', 'status', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_posts');
        Schema::dropIfExists('social_accounts');
    }
};
