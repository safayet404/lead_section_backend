<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop foreign key first
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
        });

        // 2. Update existing rows with null to 1
        DB::table('leads')->whereNull('status_id')->update(['status_id' => 1]);

        // 3. Modify column to default 1
        DB::statement("ALTER TABLE leads MODIFY status_id BIGINT UNSIGNED NOT NULL DEFAULT 1");

        // 4. Re-add foreign key
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
