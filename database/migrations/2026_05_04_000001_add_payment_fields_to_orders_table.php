<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            if (! Schema::hasColumn('orders', 'checkout_token')) {
                $table->string('checkout_token', 64)->nullable()->after('currency');
                $table->index('checkout_token');
            }

            if (! Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method', 32)->nullable()->after('checkout_token');
            }

            if (! Schema::hasColumn('orders', 'payment_status')) {
                $table->string('payment_status', 32)->default('pending')->index()->after('payment_method');
            }

            if (! Schema::hasColumn('orders', 'transaction_id')) {
                $table->string('transaction_id', 64)->nullable()->after('payment_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            if (Schema::hasColumn('orders', 'transaction_id')) {
                $table->dropColumn('transaction_id');
            }

            if (Schema::hasColumn('orders', 'payment_status')) {
                $table->dropColumn('payment_status');
            }

            if (Schema::hasColumn('orders', 'payment_method')) {
                $table->dropColumn('payment_method');
            }

            if (Schema::hasColumn('orders', 'checkout_token')) {
                $table->dropIndex(['checkout_token']);
                $table->dropColumn('checkout_token');
            }
        });
    }
};
