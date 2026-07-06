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
        Schema::create('letter_of_credits', function (Blueprint $table) {
            $table->id();
            $table->string('lc_number')->unique();
            $table->foreignId('purchase_order_id')->constrained()->onDelete('cascade');
            $table->decimal('lc_value', 15, 2);
            $table->decimal('margin_percentage', 5, 2)->default(10.00);
            $table->decimal('insurance_amount', 15, 2)->default(0.00);
            $table->decimal('cnf_agent_charge', 15, 2)->default(0.00);
            $table->string('status')->default('opened'); // opened, settled, closed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_of_credits');
    }
};
