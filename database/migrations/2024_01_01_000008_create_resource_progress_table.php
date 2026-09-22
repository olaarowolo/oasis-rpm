<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resource_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained('universities')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('resource_id')->constrained('resources')->cascadeOnDelete();
            $table->enum('status', ['pending', 'in_progress', 'submitted', 'reviewed', 'approved', 'rejected'])->default('pending');
            $table->integer('points_earned')->default(0);
            $table->timestamp('submitted_date')->nullable();
            $table->timestamp('reviewed_date')->nullable();
            $table->text('supervisor_comment')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'resource_id']);
            $table->index(['university_id', 'student_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_progress');
    }
};
