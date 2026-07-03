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
        Schema::table('customers', function (Blueprint $table) {
            $table->decimal('credit_limit', 12, 2)->default(0)->after('status');
            $table->decimal('credit_used', 12, 2)->default(0)->after('credit_limit');
            $table->integer('net_terms')->default(0)->after('credit_used'); // e.g., 0 (Cash), 30, 60
            $table->boolean('is_credit_restricted')->default(false)->after('net_terms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['credit_limit', 'credit_used', 'net_terms', 'is_credit_restricted']);
        });
    }
};
