<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $database = DB::getDriverName();

        if ($database === 'sqlite') {
            $oldTable = 'users';
            $newTable = 'users_new';

            Schema::create($newTable, function (Blueprint $table) {
                $table->id();
                $table->foreignId('university_id')->constrained('universities')->cascadeOnDelete();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->enum('role', ['student', 'supervisor', 'admin', 'super_admin'])->default('student');
                $table->string('department')->nullable();
                $table->string('phone')->nullable();
                $table->string('avatar_url')->nullable();
                $table->boolean('is_active')->default(true);
                $table->rememberToken();
                $table->timestamps();
                $table->index(['university_id', 'email']);
            });

            DB::statement("INSERT INTO {$newTable} (id, university_id, name, email, email_verified_at, password, role, department, phone, avatar_url, is_active, remember_token, created_at, updated_at)
                SELECT id, university_id, name, email, email_verified_at, password, role, department, phone, avatar_url, is_active, remember_token, created_at, updated_at
                FROM {$oldTable}");

            Schema::dropIfExists($oldTable);
            Schema::rename($newTable, $oldTable);

            return;
        }

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('student', 'supervisor', 'admin', 'super_admin') NOT NULL DEFAULT 'student'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $database = DB::getDriverName();

        if ($database === 'sqlite') {
            $oldTable = 'users';
            $newTable = 'users_old';

            Schema::create($newTable, function (Blueprint $table) {
                $table->id();
                $table->foreignId('university_id')->constrained('universities')->cascadeOnDelete();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->enum('role', ['student', 'supervisor', 'admin'])->default('student');
                $table->string('department')->nullable();
                $table->string('phone')->nullable();
                $table->string('avatar_url')->nullable();
                $table->boolean('is_active')->default(true);
                $table->rememberToken();
                $table->timestamps();
                $table->index(['university_id', 'email']);
            });

            DB::statement("INSERT INTO {$newTable} (id, university_id, name, email, email_verified_at, password, role, department, phone, avatar_url, is_active, remember_token, created_at, updated_at)
                SELECT id, university_id, name, email, email_verified_at, password, role, department, phone, avatar_url, is_active, remember_token, created_at, updated_at
                FROM {$oldTable}");

            Schema::dropIfExists($oldTable);
            Schema::rename($newTable, $oldTable);

            return;
        }

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('student', 'supervisor', 'admin') NOT NULL DEFAULT 'student'");
    }
};
