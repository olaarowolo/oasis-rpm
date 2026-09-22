<?php

namespace App\Services;

use App\Models\University;

class GeminiAIService
{
    protected University $university;
    protected string $apiKey;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct(University $university)
    {
        $this->university = $university;
        $this->apiKey = config('services.gemini.api_key', '');
    }

    public function queryResearchAssistant(string $query, string $context = ''): array
    {
        $systemPrompt = "You are an expert research advisor helping students with their academic research. Provide clear, actionable guidance.";
        $fullPrompt = $context ? "{$systemPrompt}\n\nContext: {$context}\n\nQuery: {$query}" : "{$systemPrompt}\n\nQuery: {$query}";

        return $this->generateContent($fullPrompt, 'gemini-pro');
    }

    public function getMethodologyGuidance(string $query, string $researchTopic = ''): array
    {
        $systemPrompt = "You are a research methodology expert. Help the student refine their research methodology and approach.";
        $fullPrompt = $researchTopic ? "{$systemPrompt}\n\nResearch Topic: {$researchTopic}\n\nQuery: {$query}" : "{$systemPrompt}\n\nQuery: {$query}";

        return $this->generateContent($fullPrompt, 'gemini-pro');
    }

    public function getLiteratureAssistance(string $topic, string $keywords = ''): array
    {
        $systemPrompt = "You are a literature review expert. Help identify relevant academic sources and synthesize literature.";
        $fullPrompt = $keywords ? "{$systemPrompt}\n\nKeywords: {$keywords}\n\nTopic: {$topic}" : "{$systemPrompt}\n\nTopic: {$topic}";

        return $this->generateContent($fullPrompt, 'gemini-pro');
    }

    public function getWritingFeedback(string $text, string $focusArea = 'general'): array
    {
        $systemPrompt = "You are an academic writing coach. Provide constructive feedback on writing quality, clarity, and academic tone.";
        $fullPrompt = "{$systemPrompt}\n\nFocus Area: {$focusArea}\n\nText to review:\n{$text}";

        return $this->generateContent($fullPrompt, 'gemini-pro');
    }

    protected function generateContent(string $prompt, string $model = 'gemini-pro'): array
    {
        try {
            if (!$this->apiKey) {
                return [
                    'success' => false,
                    'error' => 'Gemini API key not configured',
                    'model' => $model,
                ];
            }

            // Placeholder for actual API call
            // In production, use proper HTTP client with GuzzleHttp
            return [
                'success' => true,
                'content' => 'AI-generated response for: ' . substr($prompt, 0, 50) . '...',
                'model' => $model,
                'finish_reason' => 'STOP',
            ];
        } catch (\Exception $e) {
            \Log::error("Gemini API error: " . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'model' => $model,
            ];
        }
    }

    public function queryWithFallback(string $prompt): array
    {
        $result = $this->generateContent($prompt, 'gemini-pro');

        if (!$result['success']) {
            \Log::warning("Falling back to alternative model due to Gemini failure");
            $result = $this->generateContent($prompt, 'gpt-4-turbo');
        }

        return $result;
    }

    public function isEnabled(): bool
    {
        $features = $this->university->features_enabled ?? [];
        return $features['ai_assistant'] ?? false;
    }
}
