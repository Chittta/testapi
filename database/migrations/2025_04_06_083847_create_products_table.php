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
        Schema::create('products', function (Blueprint $table) {
            $table->id('ProductId');
            $table->string('ProductName')->nullable();
            $table->string('ProductDescription', 100)->nullable();
            $table->unsignedBigInteger('CategoryID')->nullable();
            $table->unsignedBigInteger('SubcategoryID')->nullable();
            $table->string('Currency')->nullable();
            $table->decimal('UnitPrice', 10, 0)->nullable();
            $table->unsignedBigInteger('SupplierID')->nullable();
            $table->integer('QuntityOnStock')->nullable();
            $table->integer('QuntityOnOrcer')->nullable(); // Typo in original SQL - maybe you meant "QuntityOnOrder"?
            $table->boolean('IsActive')->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('CategoryID')->references('CategoryID')->on('categories')->onDelete('set null');
            $table->foreign('SubcategoryID')->references('SubcategoryID')->on('subcategories')->onDelete('set null');
            $table->foreign('SupplierID')->references('SupplierID')->on('suppliers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
