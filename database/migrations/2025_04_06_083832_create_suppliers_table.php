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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id('SupplierID');
            $table->string('SupplierName')->nullable();
            $table->string('SupplierDescription', 100)->nullable();
            $table->string('SupplierEmailId')->nullable();
            $table->string('SupplierPhone')->nullable();
            $table->string('SupplierAddress', 100)->nullable();
            $table->timestamps(); // Optional: Adds created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
