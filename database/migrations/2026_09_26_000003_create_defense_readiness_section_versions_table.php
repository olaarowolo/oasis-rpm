<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('defense_readiness_section_versions')) {
            return;
        }

        Schema::create('defense_readiness_section_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('defense_readiness_sections')->cascadeOnDelete();
            $table->foreignId('document_id')->constrained('defense_readiness_documents')->cascadeOnDelete();
            $table->integer('version_number');
            $table->longText('content');
            $table->integer('word_count')->default(0);
            $table->foreignId('submitted_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['section_id', 'version_number']);
            $table->index('document_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('defense_readiness_section_versions');
    }
};
