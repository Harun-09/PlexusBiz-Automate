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
        Schema::create('mushak_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tax_invoice_id')->constrained('tax_invoices')->cascadeOnDelete();
            $table->string('form_type')->default('6.3'); // e.g., 6.3 for Sales, 6.6 for VDS
            $table->timestamp('issue_date');
            $table->string('challan_number')->nullable();
            $table->decimal('total_vat_amount', 15, 2)->default(0);
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mushak_documents');
    }
};
