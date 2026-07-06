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
        Schema::create('inventory_batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->foreignId('stock_location_id')->constrained()->cascadeOnDelete();
            $table->string('batch_number')->unique();
            $table->date('manufacturing_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('initial_quantity');
            $table->integer('available_quantity');
            $table->decimal('unit_cost', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_batches');
    }
};
