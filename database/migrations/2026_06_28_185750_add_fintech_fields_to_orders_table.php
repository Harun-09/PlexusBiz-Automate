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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_term')->default('cash')->after('payment_status'); // cash, net30, net60
            $table->dateTime('due_date')->nullable()->after('payment_term');
            $table->string('escrow_status')->default('held')->after('status'); // held, released, refunded
            $table->string('delivery_status')->default('pending')->after('escrow_status'); // pending, shipped, delivered
            $table->decimal('commission_amount', 12, 2)->default(0)->after('grand_total');
            $table->decimal('late_fee_amount', 12, 2)->default(0)->after('commission_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_term', 'due_date', 'escrow_status', 'delivery_status', 'commission_amount', 'late_fee_amount']);
        });
    }
};
