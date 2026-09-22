<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('university_id')->constrained('universities')->cascadeOnDelete();
            $table->string('matric_number')->unique();
            $table->string('lastname');
            $table->string('full_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('research_topic')->nullable();
            $table->timestamp('research_topic_approved_date')->nullable();
            $table->integer('current_stage')->default(1);
            $table->integer('progress_percentage')->default(0);
            $table->integer('points_earned')->default(0);
            $table->enum('status', ['active', 'suspended', 'completed', 'graduated'])->default('active');
            $table->string('personal_drive_url')->nullable();
            $table->timestamp('last_meeting_date')->nullable();
            $table->timestamps();
            $table->index(['university_id', 'matric_number']);
            $table->index(['university_id', 'current_stage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
