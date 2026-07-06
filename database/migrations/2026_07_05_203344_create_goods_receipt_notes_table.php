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
        Schema::create('goods_receipt_notes', function (Blueprint $table) {
            $table->id();
            $table->string('grn_number')->unique();
            $table->foreignId('purchase_order_id')->constrained()->onDelete('cascade');
            $table->date('receipt_date');
            $table->string('received_by');
            // Normally you would have GoodsReceiptNoteItem for exact quantities received,
            // but for simplicity, we assume the GRN is tied to the PO and verifies full receipt
            // or we add a JSON field for received quantities.
            $table->json('received_quantities')->nullable(); // format: {"po_item_id": quantity}
            $table->string('status')->default('received');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_receipt_notes');
    }
};
