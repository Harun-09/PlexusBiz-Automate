<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->string('priority')->default('medium'); // low, medium, high, critical
            $table->string('status')->default('todo'); // todo, in_progress, review, done
            $table->date('due_date')->nullable();
            $table->integer('estimated_hours')->default(0);
            $table->integer('actual_hours')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_tasks');
    }
};
