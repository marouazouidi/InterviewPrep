<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class GroqService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl = 'https://api.groq.com/openai/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key') ?? '';
        $this->model  = config('services.groq.model') ?? '';
    }

    public function generateQuestions(string $title, string $explanation): array
    {
        if (empty($this->apiKey)) {
            throw new \Exception('Groq API key is not configured. Set GROQ_API_KEY in your .env file.');
        }

        if (empty($this->model)) {
            throw new \Exception('Groq model is not configured. Set GROQ_MODEL in your .env file.');
        }

        $prompt = $this->buildPrompt($title, $explanation);

        try {
            $response = Http::timeout(30)
                ->withToken($this->apiKey)
                ->post($this->baseUrl, [
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role'    => 'system',
                            'content' => 'You are a strict JSON generator. Return ONLY valid JSON. No markdown. No surrounding text.',
                        ],
                        [
                            'role'    => 'user',
                            'content' => $prompt,
                        ],
                    ],
                    'temperature' => 0.3,
                    'max_tokens'  => 1000,
                ]);
        } catch (ConnectionException $e) {
            throw new \Exception('Could not connect to the Groq API. Check your internet connection and try again.');
        }

        if (! $response->successful()) {
            $body = $response->body();
            throw new \Exception('Groq API responded with an error: ' . $this->friendlyApiError($response->status(), $body));
        }

        $content = $response->json('choices.0.message.content');

        if (empty($content)) {
            throw new \Exception('Groq API returned an empty response.');
        }

        return $this->parseQuestions($content);
    }

    private function buildPrompt(string $title, string $explanation): string
    {
        return <<<EOT
Generate exactly 5 technical interview questions about the following concept.

Concept: {$title}

Explanation:
{$explanation}

Return ONLY a valid JSON array of 5 strings. Example:
["Question 1?", "Question 2?", "Question 3?", "Question 4?", "Question 5?"]

No markdown. No code fences. No surrounding text. ONLY the JSON array.
EOT;
    }

    private function parseQuestions(string $content): array
    {
        $clean = trim($content);

        $clean = preg_replace('/^```(?:json|JSON)\s*\n?/', '', $clean);
        $clean = preg_replace('/\n?\s*```$/', '', $clean);
        $clean = trim($clean);

        $decoded = json_decode($clean, true);

        if (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }

        if (! is_array($decoded)) {
            throw new \Exception('The AI response was not valid JSON. Please try again.');
        }

        $decoded = array_values($decoded);

        foreach ($decoded as $i => $item) {
            if (! is_string($item)) {
                $decoded[$i] = (string) $item;
            }
        }

        if (count($decoded) < 3) {
            throw new \Exception('The AI returned too few questions. Please try again.');
        }

        return array_slice($decoded, 0, 5);
    }

    private function friendlyApiError(int $status, string $body): string
    {
        $bodyLower = mb_strtolower($body);

        if ($status === 401) {
            return 'Invalid API key. Check GROQ_API_KEY in your .env file.';
        }
        if ($status === 429) {
            return 'Rate limit reached. Wait a moment and try again.';
        }
        if ($status === 400 && str_contains($bodyLower, 'decommissioned')) {
            return 'The configured model is no longer available. Update GROQ_MODEL in your .env file.';
        }

        return $body;
    }
}
