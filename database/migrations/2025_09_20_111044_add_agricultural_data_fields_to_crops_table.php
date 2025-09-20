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
        Schema::table('crops', function (Blueprint $table) {
            // Add new fields for agricultural statistics data
            $table->string('municipality')->nullable();
            $table->string('farm_type')->nullable(); // irrigated, rainfed, upland, lowland
            $table->integer('year')->nullable();
            $table->string('crop_name')->nullable(); // Alternative to 'name' field
            $table->decimal('area_planted', 10, 2)->nullable();
            $table->decimal('area_harvested', 10, 2)->nullable();
            $table->decimal('production_mt', 10, 2)->nullable(); // Production in metric tons
            $table->decimal('productivity_mt_ha', 10, 2)->nullable(); // Productivity in mt/ha
            
            // Make some existing fields nullable to support both data structures
            $table->integer('farmer_id')->nullable()->change();
            $table->string('name')->nullable()->change();
            $table->date('planting_date')->nullable()->change();
            $table->decimal('area_hectares', 8, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crops', function (Blueprint $table) {
            $table->dropColumn([
                'municipality',
                'farm_type', 
                'year',
                'crop_name',
                'area_planted',
                'area_harvested',
                'production_mt',
                'productivity_mt_ha'
            ]);
        });
    }
};
