<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supervisors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('university_id')->constrained('universities')->cascadeOnDelete();
            $table->string('title')->default('Dr.');
            $table->string('department');
            $table->text('research_areas')->nullable();
            $table->string('pin_code');
            $table->string('passphrase');
            $table->boolean('is_active')->default(true);
            $table->string('google_oauth_id')->nullable();
            $table->timestamps();
            $table->index(['university_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supervisors');
    }
};
