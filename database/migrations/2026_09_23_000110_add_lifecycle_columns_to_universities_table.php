<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('google_chat_webhook_url');
            $table->timestamp('archived_at')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'archived_at']);
        });
    }
};