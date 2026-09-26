<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('defense_readiness_reviews')) {
            return;
        }

        Schema::create('defense_readiness_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('defense_readiness_sections')->cascadeOnDelete();
            $table->foreignId('document_id')->constrained('defense_readiness_documents')->cascadeOnDelete();
            $table->unsignedBigInteger('version_id')->nullable();
            $table->foreignId('reviewer_user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('action', ['accepted', 'conditional', 'revision_requested', 'rejected', 'commented']);
            $table->longText('comment')->nullable();
            $table->text('conditions')->nullable();
            $table->timestamps();

            $table->index(['section_id', 'created_at']);
            $table->index(['document_id', 'action']);
        });

        // Attach the circular FK constraints now that all four tables exist.
        try {
            Schema::table('defense_readiness_documents', function (Blueprint $table) {
                $table->foreign('halted_section_id')
                    ->references('id')
                    ->on('defense_readiness_sections')
                    ->nullOnDelete();
            });
        } catch (Throwable $e) {
            // FK may already exist on partial re-run.
        }

        try {
            Schema::table('defense_readiness_sections', function (Blueprint $table) {
                $table->foreign('current_version_id')
                    ->references('id')
                    ->on('defense_readiness_section_versions')
                    ->nullOnDelete();
            });
        } catch (Throwable $e) {
            // FK may already exist.
        }

        try {
            Schema::table('defense_readiness_reviews', function (Blueprint $table) {
                $table->foreign('version_id')
                    ->references('id')
                    ->on('defense_readiness_section_versions')
                    ->nullOnDelete();
            });
        } catch (Throwable $e) {
            // FK may already exist on partial re-run.
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('defense_readiness_reviews');
    }
};
