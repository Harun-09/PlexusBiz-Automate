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
        Schema::create('custom_forms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('target_role')->nullable()->comment('Restrict to buyer, supplier, etc.');
            $table->timestamps();
        });

        Schema::create('custom_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_form_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->string('name');
            $table->string('type')->default('text'); // text, textarea, select, checkbox, radio, email, number
            $table->boolean('is_required')->default(false);
            $table->json('options')->nullable(); // For select, checkbox, radio
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('custom_form_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_form_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });

        Schema::create('custom_form_submission_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_form_submission_id')->constrained('custom_form_submissions', 'id', 'cfsv_submission_id_fk')->cascadeOnDelete();
            $table->foreignId('custom_form_field_id')->constrained('custom_form_fields', 'id', 'cfsv_field_id_fk')->cascadeOnDelete();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_forms_tables');
    }
};
