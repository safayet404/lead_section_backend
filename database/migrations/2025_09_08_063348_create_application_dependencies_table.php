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

        Schema::create('intakes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. "January 2026"
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });

        Schema::create('universities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->string('city')->nullable();
            $table->timestamps();
        });

        Schema::create('course_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Graduate, Post Graduate, PhD etc.
            $table->timestamps();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->foreignId('intake_id')->constrained()->cascadeOnDelete();
            $table->foreignId('university_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_type_id')->constrained('course_types')->cascadeOnDelete();

            $table->string('name'); // e.g. Business Management
            $table->string('course_duration')->nullable();
            $table->decimal('tution_fee', 12, 2)->nullable();
            $table->text('academic_requirement')->nullable();
            $table->text('english_requirement')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
        Schema::dropIfExists('course_types');
        Schema::dropIfExists('universities');
        Schema::dropIfExists('intakes');

    }
};
