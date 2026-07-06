<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forecast_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained('budgets')->cascadeOnDelete();
            $table->date('forecast_month');
            $table->decimal('projected_revenue', 15, 2);
            $table->decimal('projected_expense', 15, 2);
            $table->decimal('actual_revenue', 15, 2)->default(0);
            $table->decimal('actual_expense', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forecast_entries');
    }
};
