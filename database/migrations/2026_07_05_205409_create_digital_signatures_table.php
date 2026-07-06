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
        Schema::create('digital_signatures', function (Blueprint $table) {
            $table->id();
            $table->morphs('signable'); // What is being signed? (FormXII, Resolution, PO)
            $table->morphs('signer'); // Who is signing? (User, Supplier)
            $table->string('signature_hash'); // The mathematical proof of signature
            $table->string('public_key')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamp('signed_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_signatures');
    }
};
