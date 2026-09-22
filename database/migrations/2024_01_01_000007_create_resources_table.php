<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained('universities')->cascadeOnDelete();
            $table->string('section');
            $table->enum('type', ['video', 'document', 'link', 'quiz', 'assignment'])->default('document');
            $table->string('title');
            $table->string('url');
            $table->text('description');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_mandatory')->default(false);
            $table->integer('stage')->default(1);
            $table->integer('points')->default(0);
            $table->timestamps();
            $table->index(['university_id', 'stage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
