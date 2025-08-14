<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Get the actual foreign key name for `status_id`
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
              AND TABLE_NAME = 'leads' 
              AND COLUMN_NAME = 'status_id' 
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        $fkName = $foreignKeys[0]->CONSTRAINT_NAME ?? null;

        Schema::table('leads', function (Blueprint $table) use ($fkName) {
            // Step 2: Drop the foreign key if it exists
            if ($fkName) {
                $table->dropForeign($fkName);
            }

            // Step 3: Modify the column to add default 1
            $table->unsignedBigInteger('status_id')->nullable()->default(1)->change();

            // Step 4: Re-add the foreign key
            $table->foreign('status_id')
                  ->references('id')
                  ->on('lead_statues')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        // Step 1: Get the foreign key name again
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
              AND TABLE_NAME = 'leads' 
              AND COLUMN_NAME = 'status_id' 
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        $fkName = $foreignKeys[0]->CONSTRAINT_NAME ?? null;

        Schema::table('leads', function (Blueprint $table) use ($fkName) {
            // Drop FK if exists
            if ($fkName) {
                $table->dropForeign($fkName);
            }

            // Revert the column
            $table->unsignedBigInteger('status_id')->nullable()->default(null)->change();

            // Re-add old foreign key
            $table->foreign('status_id')
                  ->references('id')
                  ->on('lead_statues')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
        });
    }
};
