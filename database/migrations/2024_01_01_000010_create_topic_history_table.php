<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('topic_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained('universities')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('proposal_id')->constrained('proposals')->cascadeOnDelete();
            $table->string('topic_title');
            $table->enum('action', ['submitted', 'approved', 'rejected', 'revised'])->default('submitted');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['university_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('topic_history');
    }
};
