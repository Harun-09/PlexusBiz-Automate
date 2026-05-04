<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('payment_method', 32);
            $table->string('transaction_id', 64)->unique();
            $table->string('gateway_transaction_id', 128)->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('BDT');
            $table->string('status', 32)->default('pending')->index();
            $table->json('gateway_response')->nullable();
            $table->string('payer_reference')->nullable();
            $table->string('payer_phone')->nullable();
            $table->timestamp('paid_at')->nullable()->index();
            $table->timestamps();
            $table->index(['order_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
