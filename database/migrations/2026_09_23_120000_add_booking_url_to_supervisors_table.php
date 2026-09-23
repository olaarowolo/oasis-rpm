<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('supervisors') || Schema::hasColumn('supervisors', 'booking_url')) {
            return;
        }

        Schema::table('supervisors', function (Blueprint $table) {
            $table->text('booking_url')->nullable()->after('research_areas');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('supervisors') || ! Schema::hasColumn('supervisors', 'booking_url')) {
            return;
        }

        Schema::table('supervisors', function (Blueprint $table) {
            $table->dropColumn('booking_url');
        });
    }
};