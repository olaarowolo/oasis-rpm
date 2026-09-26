<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('defense_readiness_documents')) {
            return;
        }

        Schema::create('defense_readiness_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained('universities')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->enum('study_approach', ['quantitative', 'qualitative', 'mixed'])->nullable();
            $table->boolean('primary_data_collection')->default(false);
            $table->enum('status', ['draft', 'in_review', 'in_revision', 'halted', 'completed'])->default('draft');
            $table->timestamp('halted_at')->nullable();
            // halted_section_id points to defense_readiness_sections; added as a plain
            // unsignedBigInteger here (table not yet created) and the FK is attached
            // in a later migration to avoid a circular dependency.
            $table->unsignedBigInteger('halted_section_id')->nullable();
            $table->text('halted_reason')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['university_id', 'status']);
            $table->index('student_id');
            $table->unique('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('defense_readiness_documents');
    }
};
