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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('employee_code')->unique();
            $table->decimal('basic_salary', 15, 2);
            $table->date('joining_date');
            
            // Tax / Demographic Flags
            $table->boolean('is_female_or_senior')->default(false);
            $table->boolean('is_physically_challenged')->default(false);
            $table->boolean('is_freedom_fighter')->default(false);
            
            // Provident Fund
            $table->boolean('is_pf_active')->default(true);
            $table->decimal('pf_percentage', 5, 2)->default(7.50); // Usually 7-8%
            
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->timestamp('punch_time');
            $table->string('punch_type')->default('unknown'); // IN, OUT
            $table->string('biometric_device_id')->nullable();
            $table->timestamps();
            
            $table->index(['employee_id', 'punch_time']);
        });

        Schema::create('payroll_slips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('month_year'); // e.g. "2026-07"
            
            $table->decimal('basic_pay', 15, 2);
            $table->decimal('house_rent', 15, 2);
            $table->decimal('medical_allowance', 15, 2);
            $table->decimal('gross_pay', 15, 2);
            
            // Deductions
            $table->decimal('tax_deducted', 15, 2)->default(0);
            $table->decimal('pf_deducted', 15, 2)->default(0);
            
            $table->decimal('net_pay', 15, 2);
            
            $table->boolean('is_disbursed')->default(false);
            $table->timestamp('disbursed_at')->nullable();
            
            $table->timestamps();
            
            $table->unique(['employee_id', 'month_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_slips');
        Schema::dropIfExists('attendance_logs');
        Schema::dropIfExists('employees');
    }
};
