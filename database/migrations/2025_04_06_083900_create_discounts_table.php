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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id('DiscountID');
            $table->string('DiscountCoupn')->nullable();
            $table->decimal('DiscountPercentage', 10, 0)->nullable();
            $table->date('StartDate')->nullable();
            $table->date('EndDate')->nullable();
            $table->unsignedBigInteger('ProductID')->nullable();
            $table->timestamps();

            // Foreign Key
            $table->foreign('ProductID')
                  ->references('ProductId')
                  ->on('products')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
