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
        // 1. Countries Table
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 5)->nullable();
            $table->timestamps();
        });

        // 2. Intakes Table
        Schema::create('intakes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. "January 2026"
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });

        // 3. Country-Intake Pivot
        Schema::create('country_intake', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->foreignId('intake_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['country_id', 'intake_id']);
        });

        // 4. Universities Table
        Schema::create('universities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('city')->nullable();
            $table->timestamps();
        });

        // 5. Pivot: country_intake_university
        Schema::create('country_intake_university', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->foreignId('intake_id')->constrained()->cascadeOnDelete();
            $table->foreignId('university_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['country_id', 'intake_id', 'university_id']);
        });

        // 6. Course Types Table
        Schema::create('course_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Graduate, Post Graduate, PhD etc.
            $table->timestamps();
        });

        // 7. Pivot: country_intake_university_course_type
        Schema::create('country_intake_university_course_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->foreignId('intake_id')->constrained()->cascadeOnDelete();
            $table->foreignId('university_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_type_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['country_id', 'intake_id', 'university_id', 'course_type_id']);
        });

        // 8. Courses Table
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->foreignId('intake_id')->constrained()->cascadeOnDelete();
            $table->foreignId('university_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_type_id')->constrained()->cascadeOnDelete();

            $table->string('name'); // e.g. Business Management
            $table->decimal('tuition_fee', 10, 2)->nullable();
            $table->string('currency', 10)->default('GBP');
            $table->string('duration')->nullable(); // e.g. "3 years"
            $table->text('requirements')->nullable();
            $table->text('english_requirements')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
        Schema::dropIfExists('country_intake_university_course_type');
        Schema::dropIfExists('course_types');
        Schema::dropIfExists('country_intake_university');
        Schema::dropIfExists('universities');
        Schema::dropIfExists('country_intake');
        Schema::dropIfExists('intakes');
        Schema::dropIfExists('countries');
    }
};
