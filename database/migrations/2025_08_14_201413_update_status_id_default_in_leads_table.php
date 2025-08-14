<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Drop FK
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['status_id']); 
        });

        // Step 2: Modify the column default
        DB::statement("ALTER TABLE leads MODIFY status_id BIGINT UNSIGNED NULL DEFAULT 1");

        // Step 3: Re-add FK
        Schema::table('leads', function (Blueprint $table) {
            $table->foreign('status_id')
                  ->references('id')
                  ->on('lead_statues')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['status_id']); 
        });

        DB::statement("ALTER TABLE leads MODIFY status_id BIGINT UNSIGNED NULL DEFAULT NULL");

        Schema::table('leads', function (Blueprint $table) {
            $table->foreign('status_id')
                  ->references('id')
                  ->on('lead_statues')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
        });
    }
};
