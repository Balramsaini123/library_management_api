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
        Schema::table('borrowed_books', function (Blueprint $table) {
            $table->boolean('penalty_paid')->default(false);
            $table->date('return_date')->nullable();
            $table->decimal('penalty_amount', 8, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowed_books', function (Blueprint $table) {
            $table->dropColumn('penalty_paid');
            $table->dropColumn('return_date');
            $table->dropColumn('penalty_amount');
        });
    }
};
