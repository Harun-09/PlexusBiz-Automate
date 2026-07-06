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
        Schema::create('non_conformance_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quality_inspection_id')->constrained()->cascadeOnDelete();
            $table->text('description');
            $table->string('severity')->default('minor'); // minor, major, critical
            $table->text('corrective_action')->nullable();
            $table->string('status')->default('open'); // open, investigating, resolved, closed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('non_conformance_reports');
    }
};
