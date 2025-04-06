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
        Schema::create('users', function (Blueprint $table) {
            $table->id('UserId');
            $table->string('Username')->nullable();
            $table->string('Password')->nullable(); // Consider hashing passwords securely in controller/service
            $table->string('EmailId')->nullable();
            $table->string('Mobile', 10)->nullable();
            $table->string('FirstName')->nullable();
            $table->string('LastName')->nullable();
            $table->string('Address')->nullable();
            $table->string('Country')->nullable();
            $table->string('State')->nullable();
            $table->string('City')->nullable();
            $table->string('PIN')->nullable();
            $table->timestamps();

            $table->unique('UserId'); // Optional: primary key is already unique
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
