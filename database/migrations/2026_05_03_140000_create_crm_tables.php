<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('company_name')->nullable();
            $table->string('contact_name');
            $table->string('email')->index();
            $table->string('phone')->nullable();
            $table->string('status', 32)->default('active')->index();
            $table->string('lifecycle_stage', 32)->default('customer')->index();
            $table->json('tags')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('last_activity_at')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
        });

        Schema::create('leads', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('source')->nullable()->index();
            $table->string('status', 32)->default('new')->index();
            $table->string('company_name')->nullable();
            $table->string('contact_name');
            $table->string('email')->nullable()->index();
            $table->string('phone')->nullable();
            $table->decimal('value', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('next_follow_up_at')->nullable()->index();
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('interactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 32)->index();
            $table->string('direction', 32)->nullable();
            $table->string('related_type')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->string('summary');
            $table->json('payload')->nullable();
            $table->timestamp('occurred_at')->index();
            $table->timestamps();
            $table->index(['related_type', 'related_id']);
        });

        Schema::create('customer_segments', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('status', 32)->default('active')->index();
            $table->text('description')->nullable();
            $table->json('filters_json');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_segments');
        Schema::dropIfExists('interactions');
        Schema::dropIfExists('leads');

        Schema::table('orders', function (Blueprint $table): void {
            $table->dropForeign(['customer_id']);
        });

        Schema::dropIfExists('customers');
    }
};
