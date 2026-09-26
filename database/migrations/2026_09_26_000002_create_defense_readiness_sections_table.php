<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('defense_readiness_sections')) {
            return;
        }

        Schema::create('defense_readiness_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('defense_readiness_documents')->cascadeOnDelete();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('key');
            $table->string('title');
            $table->integer('position');
            $table->enum('kind', ['fixed', 'group', 'custom'])->default('fixed');
            $table->text('guidance')->nullable();
            $table->integer('target_min_words')->nullable();
            $table->integer('target_max_words')->nullable();
            $table->string('word_count_label')->nullable();
            $table->longText('content')->nullable();
            $table->integer('word_count')->default(0);
            $table->enum('status', ['locked', 'draft', 'submitted', 'accepted', 'conditional', 'revision_requested', 'rejected'])->default('locked');
            $table->timestamp('conditions_acknowledged_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('decided_at')->nullable();
            // current_version_id points to defense_readiness_section_versions; added as a
            // plain unsignedBigInteger here (table not yet created) and the FK is attached
            // in a later migration to avoid a circular dependency.
            $table->unsignedBigInteger('current_version_id')->nullable();
            $table->timestamps();

            $table->index(['document_id', 'status']);
            $table->index(['parent_id', 'position']);
            $table->unique(['document_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('defense_readiness_sections');
    }
};
