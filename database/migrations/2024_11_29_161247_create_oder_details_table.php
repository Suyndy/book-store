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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('book_id')->constrained(); 
            $table->integer('quantity');
            $table->integer('price');
            $table->foreignId('order_id')->constrained()->onDelete('cascade'); 
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
