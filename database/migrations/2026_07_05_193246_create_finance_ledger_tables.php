<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Chart of Accounts
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->enum('normal_balance', ['debit', 'credit']);
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            // Optional: soft deletes if we want to retain history without breaking foreign keys
            $table->softDeletes();
        });

        // 2. Journal Entries (Header table for immutable transactions)
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable()->comment('e.g. Order #1024, PO #99');
            $table->string('description');
            $table->string('idempotency_key')->unique()->comment('To prevent duplicate processing');
            // Deliberately omit updated_at to enforce immutability at the schema level.
            $table->timestamp('created_at')->useCurrent();
        });

        // 3. Postings (Debit/Credit line items for journal entries)
        Schema::create('postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained('journal_entries')->onDelete('cascade');
            $table->foreignId('account_id')->constrained('accounts')->onDelete('restrict');
            $table->enum('type', ['debit', 'credit']);
            // DECIMAL(20,6) to prevent fractional cent/floating point issues
            $table->decimal('amount', 20, 6);
            $table->string('currency', 3)->default('BDT');
            // Deliberately omit updated_at to enforce immutability
            $table->timestamp('created_at')->useCurrent();
        });

        // 4. Account Balances (Materialized view pattern for fast reads)
        Schema::create('account_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade');
            $table->decimal('balance', 20, 6)->default(0);
            $table->timestamp('last_calculated_at')->useCurrent();
            // Since this is a cache/view, we can update it
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_balances');
        Schema::dropIfExists('postings');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('accounts');
    }
};
