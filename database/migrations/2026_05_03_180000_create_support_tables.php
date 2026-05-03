<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table): void {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('requester_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('channel', 32)->index();
            $table->string('subject');
            $table->text('description');
            $table->string('priority', 32)->default('normal')->index();
            $table->string('status', 32)->default('open')->index();
            $table->json('tags_json')->nullable();
            $table->json('metadata_json')->nullable();
            $table->timestamp('last_message_at')->nullable()->index();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['supplier_id', 'status']);
            $table->index(['requester_id', 'status']);
        });

        Schema::create('support_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('support_ticket_id')->constrained('support_tickets')->cascadeOnDelete();
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('sender_type', 32)->index();
            $table->string('visibility', 32)->default('public')->index();
            $table->text('message');
            $table->json('payload_json')->nullable();
            $table->timestamps();
            $table->index(['support_ticket_id', 'created_at']);
        });

        Schema::create('support_faqs', function (Blueprint $table): void {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->json('keywords_json')->nullable();
            $table->string('status', 32)->default('active')->index();
            $table->unsignedSmallInteger('priority')->default(100)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('supplier_notifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('support_ticket_id')->nullable()->constrained('support_tickets')->nullOnDelete();
            $table->string('type', 64)->index();
            $table->string('title');
            $table->text('body');
            $table->json('payload_json')->nullable();
            $table->timestamp('read_at')->nullable()->index();
            $table->timestamps();
            $table->index(['supplier_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_notifications');
        Schema::dropIfExists('support_faqs');
        Schema::dropIfExists('support_messages');
        Schema::dropIfExists('support_tickets');
    }
};
