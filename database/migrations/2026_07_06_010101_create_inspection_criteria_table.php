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
        Schema::create('inspection_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quality_inspection_id')->constrained()->cascadeOnDelete();
            $table->string('criterion_name');
            $table->string('expected_value');
            $table->string('actual_value')->nullable();
            $table->string('result')->default('pending'); // pending, pass, fail
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspection_criteria');
    }
};
