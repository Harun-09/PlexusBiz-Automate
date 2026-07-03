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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('weight', 8, 2)->default(0.00)->after('base_price')->comment('Weight in kg');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->foreignId('b2c_customer_id')->nullable()->after('user_id')->constrained('b2c_customers')->nullOnDelete();
            $table->string('shipping_method')->nullable()->after('status')->comment('e.g., standard, weight_based, own_logistics');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('buyer_id')->nullable()->change();
            $table->string('shipping_method')->nullable()->after('grand_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('buyer_id')->nullable(false)->change();
            $table->dropColumn('shipping_method');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign(['b2c_customer_id']);
            $table->dropColumn('b2c_customer_id');
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->dropColumn('shipping_method');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('weight');
        });
    }
};
