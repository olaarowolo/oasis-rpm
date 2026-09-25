<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('universities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code', 20)->unique();
            $table->string('email');
            $table->string('department')->nullable();
            $table->string('phone')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('branding_color')->default('#3B82F6');
            $table->json('ai_model_config')->default(json_encode([
                'primary' => 'gemini-pro',
                'fallback' => 'gpt-4-turbo',
                'timeout' => 30,
            ]));
            $table->json('email_config')->default(json_encode([
                'from_address' => 'noreply@supervision.example.com',
                'from_name' => 'Research Supervision Portal',
                'reply_to' => 'support@supervision.example.com',
            ]));
            $table->string('google_chat_webhook_url')->nullable();
            $table->json('features_enabled')->default(json_encode([
                'ai_assistant' => true,
                'google_chat' => true,
                'analytics' => true,
                'resource_tracking' => true,
            ]));
            $table->boolean('has_structured_departments')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('universities');
    }
};
