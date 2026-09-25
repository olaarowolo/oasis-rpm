<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (! Schema::hasColumn('students', 'faculty')) {
                $table->string('faculty', 100)->nullable()->after('degree_level');
            }
            if (! Schema::hasColumn('students', 'department')) {
                $table->string('department', 150)->nullable()->after('faculty');
            }
            if (! Schema::hasColumn('students', 'programme')) {
                $table->string('programme', 255)->nullable()->after('department');
            }
        });

        // Composite index to support filtering the student roster by
        // faculty/department within a university tenant.
        try {
            Schema::table('students', function (Blueprint $table) {
                $table->index(['university_id', 'faculty', 'department'], 'students_university_faculty_department_index');
            });
        } catch (Throwable $e) {
            // Index already exists (e.g. partial re-run); safe to ignore.
        }
    }

    public function down(): void
    {
        // Drop the index (guarded — it may already be absent on rollback).
        try {
            Schema::table('students', function (Blueprint $table) {
                $table->dropIndex('students_university_faculty_department_index');
            });
        } catch (Throwable $e) {
            // Index not present; nothing to drop.
        }

        Schema::table('students', function (Blueprint $table) {
            $columns = [];
            foreach (['faculty', 'department', 'programme'] as $column) {
                if (Schema::hasColumn('students', $column)) {
                    $columns[] = $column;
                }
            }
            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
