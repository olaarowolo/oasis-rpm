<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('supervisor_id')->nullable()->after('university_id')->constrained('supervisors')->nullOnDelete();
            $table->index(['university_id', 'supervisor_id']);
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['supervisor_id']);
            $table->dropIndex(['university_id', 'supervisor_id']);
            $table->dropColumn('supervisor_id');
        });
    }
};
