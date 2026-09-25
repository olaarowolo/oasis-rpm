<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('faculty')->nullable();
            $table->string('unit_type')->default('faculty');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['university_id', 'faculty', 'unit_type']);
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
