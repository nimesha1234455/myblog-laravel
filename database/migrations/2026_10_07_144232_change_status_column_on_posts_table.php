<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE posts MODIFY status VARCHAR(20) NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        //
    }
};