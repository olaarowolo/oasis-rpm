<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'degree_level')) {
                $table->string('degree_level', 10)->default('BSc')->after('email');
            }
            if (!Schema::hasColumn('students', 'account_status')) {
                $table->string('account_status', 20)->default('active')->after('status');
            }
            if (!Schema::hasColumn('students', 'graduated_at')) {
                $table->timestamp('graduated_at')->nullable()->after('last_meeting_date');
            }
            if (!Schema::hasColumn('students', 'archived_at')) {
                $table->timestamp('archived_at')->nullable()->after('graduated_at');
            }
        });

        if (!Schema::hasTable('archive_submissions')) {
            Schema::create('archive_submissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('university_id')->constrained('universities')->cascadeOnDelete();
                $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
                $table->string('degree_level', 10);
                $table->string('project_type', 20)->default('project');
                $table->string('title')->nullable();
                $table->text('abstract')->nullable();
                $table->text('keywords')->nullable();
                $table->string('department')->nullable();
                $table->string('submission_status', 30)->default('draft');
                $table->string('visibility', 30)->default('institution_only');
                $table->string('final_document_path')->nullable();
                $table->string('supplementary_document_path')->nullable();
                $table->timestamp('submitted_at')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamp('published_at')->nullable();
                $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('reviewer_note')->nullable();
                $table->timestamps();

                $table->unique(['university_id', 'student_id']);
                $table->index(['university_id', 'submission_status']);
                $table->index(['degree_level', 'submission_status']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('archive_submissions')) {
            Schema::dropIfExists('archive_submissions');
        }

        Schema::table('students', function (Blueprint $table) {
            $dropColumns = [];
            foreach (['degree_level', 'account_status', 'graduated_at', 'archived_at'] as $col) {
                if (Schema::hasColumn('students', $col)) {
                    $dropColumns[] = $col;
                }
            }

            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
