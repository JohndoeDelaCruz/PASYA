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
            $table->string('cropID', 50)->nullable()->after('id')->comment('Custom crop identifier');
            $table->string('cropCategory', 100)->nullable()->after('crop_name')->comment('Category of the crop (e.g., Leafy Vegetables, Root Crops)');
            $table->integer('cropDaysToMaturity')->nullable()->after('cropCategory')->comment('Number of days from planting to maturity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crops', function (Blueprint $table) {
            $table->dropColumn(['cropID', 'cropCategory', 'cropDaysToMaturity']);
        });
    }
};
