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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained('students');
            $table->foreignId('country_id')->nullable()->constrained('countries');
            $table->foreignId('intake_id')->nullable()->constrained('intakes');
            $table->foreignId('course_type_id')->nullable()->constrained('course_types');
            $table->foreignId('university_id')->nullable()->constrained('universities');
            $table->foreignId('')->nullable()->constrained('courses');
            $table->string('passport_country');
            $table->foreignId('channel_partner_id')->nullable()->constrained('channel_partners');
            $table->foreignId('application_status_id')->nullable()->constrained('application_statuses');
            $table->foreignId('branch_id')->nullable()->constrained('branches');
            $table->foreignId('created_by')->nullable()->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
