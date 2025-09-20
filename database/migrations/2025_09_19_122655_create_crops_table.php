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
        Schema::create('crops', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farmer_id');
            $table->string('name');
            $table->string('variety')->nullable();
            $table->date('planting_date');
            $table->date('expected_harvest_date')->nullable();
            $table->date('actual_harvest_date')->nullable();
            $table->decimal('area_hectares', 8, 2);
            $table->text('description')->nullable();
            $table->enum('status', ['planted', 'growing', 'harvested', 'failed'])->default('planted');
            $table->decimal('expected_yield_kg', 10, 2)->nullable();
            $table->decimal('actual_yield_kg', 10, 2)->nullable();
            $table->timestamps();
            
            $table->foreign('farmer_id')->references('farmerID')->on('tblFarmers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crops');
    }
};
