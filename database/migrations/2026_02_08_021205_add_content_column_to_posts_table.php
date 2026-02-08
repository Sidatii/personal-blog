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
        Schema::table('posts', function (Blueprint $table) {
            // Add content column for admin-created posts (nullable for git-synced posts)
            $table->text('content')->nullable()->after('excerpt');

            // Make filepath nullable for admin-created posts
            $table->string('filepath')->nullable()->change();

            // Make content_hash nullable since admin posts won't have files
            $table->string('content_hash')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('content');

            // Revert filepath and content_hash to not nullable
            $table->string('filepath')->nullable(false)->change();
            $table->string('content_hash')->nullable(false)->change();
        });
    }
};
