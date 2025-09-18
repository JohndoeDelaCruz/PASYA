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
        Schema::create('tblFarmers', function (Blueprint $table) {
            $table->id('farmerID');
            $table->string('farmerName');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('farmerLocation')->comment('Barangay/Municipality in Benguet');
            $table->string('farmerContactInfo')->nullable()->comment('Optional contact details');
            $table->string('farmerCooperative')->nullable()->comment('Name of the cooperative the farmer belongs to');
            $table->boolean('is_active')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tblFarmers');
    }
};