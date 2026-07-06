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
        Schema::create('mushak_records', function (Blueprint $table) {
            $table->id();
            $table->string('book_type'); // e.g. 6.1 (Purchase), 6.2 (Sales), 6.5 (Transfer)
            $table->unsignedBigInteger('reference_id')->nullable(); // Order ID or PO ID
            $table->string('reference_type')->nullable(); // 'App\Domains\ECommerce\Models\Order'
            $table->decimal('amount', 15, 2)->default(0.00); // Base Amount
            $table->decimal('vat_amount', 15, 2)->default(0.00); // Input/Output VAT
            $table->decimal('vds_amount', 15, 2)->default(0.00); // VAT Deducted at Source
            $table->timestamp('date')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mushak_records');
    }
};
