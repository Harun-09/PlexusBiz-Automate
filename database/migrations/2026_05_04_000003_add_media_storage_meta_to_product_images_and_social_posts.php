<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_images', function (Blueprint $table): void {
            $table->json('storage_meta')->nullable()->after('path');
        });

        Schema::table('social_posts', function (Blueprint $table): void {
            $table->json('media_meta')->nullable()->after('media_url');
        });
    }

    public function down(): void
    {
        Schema::table('social_posts', function (Blueprint $table): void {
            $table->dropColumn('media_meta');
        });

        Schema::table('product_images', function (Blueprint $table): void {
            $table->dropColumn('storage_meta');
        });
    }
};
