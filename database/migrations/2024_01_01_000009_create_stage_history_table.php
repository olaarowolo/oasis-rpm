<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stage_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained('universities')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->integer('stage_number');
            $table->string('stage_name');
            $table->enum('action', ['entered', 'exited', 'reset', 'gated'])->default('entered');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['university_id', 'student_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stage_history');
    }
};
