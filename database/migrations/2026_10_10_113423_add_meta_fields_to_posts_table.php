<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (! Schema::hasColumn('posts', 'meta_description')) {
                $table->string('meta_description')->nullable()->after('content');
            }

            if (! Schema::hasColumn('posts', 'meta_keywords')) {
                $table->string('meta_keywords')->nullable()->after('meta_description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'meta_keywords')) {
                $table->dropColumn('meta_keywords');
            }

            if (Schema::hasColumn('posts', 'meta_description')) {
                $table->dropColumn('meta_description');
            }
        });
    }
};