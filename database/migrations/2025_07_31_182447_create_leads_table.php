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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->date('lead_date');
            $table->string('email');
            $table->string('name');
            $table->string('phone');
            $table->string('interested_course');
            $table->string('interested_country');
            $table->string('current_qualification');
            $table->string('ielts_or_english_test')->nullable();
            $table->string('soruce')->nullable();
       
            $table->text('notes')->nullable();
            $table->foreignId('assigned_branch')->nullable()->constrained('branches')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('assigned_user')->nullable()->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
