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
            // Add indexes for frequently queried columns
            $table->index('crop', 'idx_crop_name');
            $table->index('municipality', 'idx_municipality');
            $table->index('year', 'idx_year');
            $table->index('month', 'idx_month');
            $table->index('status', 'idx_status');
            $table->index('cooperative', 'idx_cooperative');
            
            // Composite indexes for common query patterns
            $table->index(['crop', 'municipality'], 'idx_crop_municipality');
            $table->index(['year', 'month'], 'idx_year_month');
            $table->index(['municipality', 'year'], 'idx_municipality_year');
            $table->index(['crop', 'year', 'status'], 'idx_crop_year_status');
            
            // Index for sorting and pagination
            $table->index('created_at', 'idx_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crops', function (Blueprint $table) {
            $table->dropIndex('idx_crop_name');
            $table->dropIndex('idx_municipality');
            $table->dropIndex('idx_year');
            $table->dropIndex('idx_month');
            $table->dropIndex('idx_status');
            $table->dropIndex('idx_cooperative');
            $table->dropIndex('idx_crop_municipality');
            $table->dropIndex('idx_year_month');
            $table->dropIndex('idx_municipality_year');
            $table->dropIndex('idx_crop_year_status');
            $table->dropIndex('idx_created_at');
        });
    }
};
