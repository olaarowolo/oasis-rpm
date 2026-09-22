<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained('universities')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('proposal_id')->unique();
            $table->string('title');
            $table->string('location');
            $table->longText('abstract');
            $table->timestamp('date_submitted');
            $table->enum('status', ['pending', 'approved', 'conditional', 'revision_required'])->default('pending');
            $table->longText('supervisor_comment')->nullable();
            $table->json('conditions')->nullable();
            $table->timestamps();
            $table->index(['university_id', 'student_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
