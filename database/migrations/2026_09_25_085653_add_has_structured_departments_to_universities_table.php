<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('universities', 'has_structured_departments')) {
            Schema::table('universities', function (Blueprint $table) {
                $table->boolean('has_structured_departments')->default(false)->after('features_enabled');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('universities', 'has_structured_departments')) {
            Schema::table('universities', function (Blueprint $table) {
                $table->dropColumn('has_structured_departments');
            });
        }
    }
};
