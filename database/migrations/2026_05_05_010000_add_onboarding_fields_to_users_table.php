<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('account_type', 32)->nullable()->after('status')->index();
            $table->string('company_name')->nullable();
            $table->string('job_title')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('employees', 50)->nullable();
            $table->string('country', 120)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'account_type',
                'company_name',
                'job_title',
                'phone',
                'employees',
                'country',
            ]);
        });
    }
};
