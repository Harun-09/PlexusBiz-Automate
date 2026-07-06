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
        Schema::create('compliance_documents', function (Blueprint $table) {
            $table->id();
            $table->string('document_type'); // e.g. Form_XII, Schedule_X
            $table->string('status')->default('draft'); // draft, signed, filed
            $table->text('payload'); // The JSON payload or content of the form
            $table->string('pdf_path')->nullable();
            $table->date('filing_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_documents');
    }
};
