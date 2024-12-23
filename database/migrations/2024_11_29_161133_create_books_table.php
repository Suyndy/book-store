<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id(); 
            $table->string('isbn', 191)->unique();
            $table->string('title');
            $table->integer('price');
            $table->string('author');
            $table->integer('quantity')->default(0);
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->date('published_at');
            $table->text('description')->nullable();
            $table->text('image')->nullable();
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
