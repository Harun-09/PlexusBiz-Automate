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
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('trade_license_path')->nullable();
            $table->string('tin_number')->nullable();
            $table->string('bin_number')->nullable();
            $table->string('corporate_certificate_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn([
                'trade_license_path',
                'tin_number',
                'bin_number',
                'corporate_certificate_path'
            ]);
        });
    }
};
