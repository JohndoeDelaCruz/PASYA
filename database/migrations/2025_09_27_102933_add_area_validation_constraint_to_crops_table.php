<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SQLite doesn't support adding CHECK constraints to existing tables
        // We'll implement validation at the application level instead
        // For future reference, this would work on MySQL/PostgreSQL:
        // DB::statement('ALTER TABLE crops ADD CONSTRAINT chk_area_harvested_lte_area_planted CHECK (area_harvested <= area_planted OR area_harvested IS NULL OR area_planted IS NULL)');
        
        // For SQLite, we can create a trigger instead
        DB::statement('
            CREATE TRIGGER area_validation_trigger
            BEFORE INSERT ON crops
            WHEN NEW.area_harvested IS NOT NULL AND NEW.area_planted IS NOT NULL AND NEW.area_harvested > NEW.area_planted
            BEGIN
                SELECT RAISE(ABORT, "Area harvested cannot be greater than area planted");
            END
        ');
        
        DB::statement('
            CREATE TRIGGER area_validation_trigger_update
            BEFORE UPDATE ON crops
            WHEN NEW.area_harvested IS NOT NULL AND NEW.area_planted IS NOT NULL AND NEW.area_harvested > NEW.area_planted
            BEGIN
                SELECT RAISE(ABORT, "Area harvested cannot be greater than area planted");
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the triggers
        DB::statement('DROP TRIGGER IF EXISTS area_validation_trigger');
        DB::statement('DROP TRIGGER IF EXISTS area_validation_trigger_update');
    }
};
