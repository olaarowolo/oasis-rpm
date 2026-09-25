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
            // For SQLite, use raw SQL to avoid Blueprint index conflicts
            DB::statement('
                CREATE TABLE users_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                    university_id INTEGER NULL,
                    name VARCHAR NOT NULL,
                    email VARCHAR NOT NULL UNIQUE,
                    email_verified_at DATETIME NULL,
                    password VARCHAR NOT NULL,
                    role VARCHAR CHECK (role IN (\'student\', \'supervisor\', \'admin\', \'super_admin\')) NOT NULL DEFAULT \'student\',
                    department VARCHAR NULL,
                    phone VARCHAR NULL,
                    avatar_url VARCHAR NULL,
                    is_active TINYINT(1) NOT NULL DEFAULT 1,
                    remember_token VARCHAR NULL,
                    created_at DATETIME NOT NULL,
                    updated_at DATETIME NOT NULL,
                    FOREIGN KEY (university_id) REFERENCES universities (id) ON DELETE CASCADE
                )
            ');
            DB::statement('CREATE INDEX users_new_role_university_idx ON users_new (role, university_id)');

            DB::statement("INSERT INTO users_new (id, university_id, name, email, email_verified_at, password, role, department, phone, avatar_url, is_active, remember_token, created_at, updated_at)
                SELECT id, university_id, name, email, email_verified_at, password, role, department, phone, avatar_url, is_active, remember_token, created_at, updated_at
                FROM users");

            DB::statement('DROP TABLE users');
            DB::statement('ALTER TABLE users_new RENAME TO users');

            // Update existing super_admin records to have NULL university_id
            DB::table('users')->where('role', 'super_admin')->update(['university_id' => null]);

            return;
        }

        // For MySQL/PostgreSQL
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('university_id')->nullable()->change();
        });

        // Add partial index for super_admin performance
        if ($database === 'pgsql') {
            DB::statement("CREATE INDEX users_super_admin_partial_idx ON users (role) WHERE role = 'super_admin' AND university_id IS NULL");
        } elseif ($database === 'mysql') {
            // MySQL doesn't support partial indexes, but we can add a composite index
            DB::statement("ALTER TABLE users ADD INDEX idx_role_university_id (role, university_id)");
        }

        // Update existing super_admin records to have NULL university_id
        DB::table('users')->where('role', 'super_admin')->update(['university_id' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $database = DB::getDriverName();

        if ($database === 'sqlite') {
            // Set any NULL university_id for super_admin to a default (first university)
            $defaultUniversityId = DB::table('universities')->value('id');
            if ($defaultUniversityId) {
                DB::table('users')->where('role', 'super_admin')->whereNull('university_id')->update(['university_id' => $defaultUniversityId]);
            }

            DB::statement('
                CREATE TABLE users_old (
                    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                    university_id INTEGER NOT NULL,
                    name VARCHAR NOT NULL,
                    email VARCHAR NOT NULL UNIQUE,
                    email_verified_at DATETIME NULL,
                    password VARCHAR NOT NULL,
                    role VARCHAR CHECK (role IN (\'student\', \'supervisor\', \'admin\')) NOT NULL DEFAULT \'student\',
                    department VARCHAR NULL,
                    phone VARCHAR NULL,
                    avatar_url VARCHAR NULL,
                    is_active TINYINT(1) NOT NULL DEFAULT 1,
                    remember_token VARCHAR NULL,
                    created_at DATETIME NOT NULL,
                    updated_at DATETIME NOT NULL,
                    FOREIGN KEY (university_id) REFERENCES universities (id) ON DELETE CASCADE
                )
            ');
            DB::statement('CREATE INDEX users_old_university_id_email_idx ON users_old (university_id, email)');

            DB::statement("INSERT INTO users_old (id, university_id, name, email, email_verified_at, password, role, department, phone, avatar_url, is_active, remember_token, created_at, updated_at)
                SELECT id, university_id, name, email, email_verified_at, password, role, department, phone, avatar_url, is_active, remember_token, created_at, updated_at
                FROM users");

            DB::statement('DROP TABLE users');
            DB::statement('ALTER TABLE users_old RENAME TO users');

            return;
        }

        // Remove partial index if PostgreSQL
        if ($database === 'pgsql') {
            DB::statement("DROP INDEX IF EXISTS users_super_admin_partial_idx");
        } elseif ($database === 'mysql') {
            DB::statement("DROP INDEX IF EXISTS idx_role_university_id ON users");
        }

        // Make university_id NOT NULL again
        // First, set any NULL university_id for super_admin to a default (first university)
        $defaultUniversityId = DB::table('universities')->value('id');
        if ($defaultUniversityId) {
            DB::table('users')->where('role', 'super_admin')->whereNull('university_id')->update(['university_id' => $defaultUniversityId]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('university_id')->nullable(false)->change();
        });
    }
};