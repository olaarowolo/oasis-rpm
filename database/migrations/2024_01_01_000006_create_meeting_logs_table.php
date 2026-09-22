<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained('universities')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('log_id')->unique();
            $table->integer('meeting_number');
            $table->date('meeting_date');
            $table->enum('meeting_mode', ['in_person', 'virtual', 'hybrid'])->default('in_person');
            $table->integer('duration');
            $table->text('previous_actions');
            $table->text('progress_since');
            $table->text('discussion_points');
            $table->text('work_reviewed');
            $table->string('chapter_focus');
            $table->text('risks')->nullable();
            $table->text('support_required')->nullable();
            $table->date('next_meeting_date');
            $table->string('next_meeting_focus');
            $table->text('student_signature')->nullable();
            $table->text('feedback_summary')->nullable();
            $table->text('areas_revision')->nullable();
            $table->text('agreed_actions')->nullable();
            $table->text('supervisor_signature')->nullable();
            $table->date('signoff_date')->nullable();
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'approved'])->default('draft');
            $table->text('feedback')->nullable();
            $table->timestamps();
            $table->index(['university_id', 'student_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_logs');
    }
};
