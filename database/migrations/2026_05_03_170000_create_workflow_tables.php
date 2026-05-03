<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_rules', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('trigger_event')->index();
            $table->json('conditions_json')->nullable();
            $table->json('actions_json');
            $table->string('status', 32)->default('active')->index();
            $table->unsignedInteger('priority')->default(100)->index();
            $table->boolean('run_async')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('workflow_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('rule_id')->nullable()->constrained('automation_rules')->nullOnDelete();
            $table->string('trigger_event')->index();
            $table->json('payload');
            $table->string('status', 32)->index();
            $table->json('result')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('executed_at')->nullable()->index();
            $table->timestamps();
            $table->index(['rule_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_logs');
        Schema::dropIfExists('automation_rules');
    }
};
