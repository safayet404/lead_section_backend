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
        Schema::create('student_application_files', function (Blueprint $table) {
            $table->id();
             $table->foreignId('application_id')->constrained('applications')->cascadeOnUpdate()->restrictOnDelete();

            $table->string('file_path');
            $table->string('file_type')->nullable();
            $table->string('original_name')->nullable();
             $table->bigInteger('file_size')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_application_files');
    }
};
