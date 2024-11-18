<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE INDEX books_fulltext_idx ON books USING GIN (to_tsvector('english', title || ' ' || author || ' ' || \"ISBN\" || ' ' || status));");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS books_fulltext_idx;');
    }
};
