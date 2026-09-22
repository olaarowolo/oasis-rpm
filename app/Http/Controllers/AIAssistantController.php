<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AIAssistantController extends BaseController
{
    public function query(Request $request)
    {
        $validated = $request->validate([
            'query' => 'required|string|max:2000',
            'context' => 'sometimes|string|nullable',
            'query_type' => 'sometimes|in:research,methodology,literature,writing',
        ]);

        $university = \App\Models\University::find(session('university_id'));

        // Check if AI is enabled
        $features = $university->features_enabled;
        if (!$features['ai_assistant'] ?? false) {
            return $this->error('AI Assistant is not enabled for this university', 403);
        }

        // Placeholder response (in production, call Gemini API)
        $response = [
            'success' => true,
            'content' => 'AI-generated response for: ' . $validated['query'],
            'model' => 'gemini-pro',
            'finish_reason' => 'STOP',
        ];

        return $this->success([
            'response' => $response['content'],
            'model_used' => $response['model'],
            'finish_reason' => $response['finish_reason'],
        ], 'AI response generated successfully');
    }

    public function listModels(Request $request)
    {
        $models = [
            ['id' => 'gemini-pro', 'name' => 'Gemini Pro', 'description' => 'Latest Gemini model for general queries'],
            ['id' => 'gpt-4-turbo', 'name' => 'GPT-4 Turbo', 'description' => 'Fallback advanced model'],
        ];

        return $this->success($models, 'Available models retrieved successfully');
    }

    public function getQueryHistory(Request $request)
    {
        return $this->success([], 'Query history retrieved successfully');
    }
}
