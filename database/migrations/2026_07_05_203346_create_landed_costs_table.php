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
        Schema::create('landed_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->onDelete('cascade');
            $table->decimal('freight_cost', 15, 2)->default(0.00);
            $table->decimal('insurance_cost', 15, 2)->default(0.00);
            $table->decimal('customs_duty', 15, 2)->default(0.00);
            $table->decimal('port_handling', 15, 2)->default(0.00);
            $table->decimal('total_landed_cost', 15, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landed_costs');
    }
};
