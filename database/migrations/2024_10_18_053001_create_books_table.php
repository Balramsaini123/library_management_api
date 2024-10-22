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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid_column')->nullable();
            $table->string('title', 50);
            $table->text('description');
            $table->double('price', 10, 2);
            $table->date('published_date');
            $table->string('author', 50);
            $table->string('ISBN', 13)->unique();
            $table->enum('status', [0,1])->default(1)->comment('0 = non-available, 1 = available');
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
