<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->integer('file_size')->default(0); // bytes
            $table->morphs('documentable'); // polymorphic: attach to PO, Invoice, Project, etc.
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->integer('version')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
