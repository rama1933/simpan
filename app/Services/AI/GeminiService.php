<?php

namespace App\Services\AI;

use App\Services\BaseService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GeminiService extends BaseService
{
    private string $apiKey;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key') ?? '';
    }

    public function generateContent(string $prompt, array $options = [])
    {
        if (empty($this->apiKey)) {
            return $this->error('Gemini API key tidak ditemukan. Silakan set GEMINI_API_KEY di .env file.');
        }
        
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => array_merge([
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1024,
                ], $options)
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                
                return $this->success('Content berhasil dibuat', [
                    'content' => $content,
                    'usage' => $data['usageMetadata'] ?? []
                ]);
            }

            return $this->error('Gagal generate content: ' . $response->body());
        } catch (\Exception $e) {
            return $this->error('Error: ' . $e->getMessage());
        }
    }

    public function analyzeKnowledge(string $content)
    {
        $prompt = "Analisis konten berikut dan berikan insight:\n\n{$content}\n\nBerikan analisis dalam bahasa Indonesia dengan format:\n1. Kategori yang sesuai\n2. Tag yang relevan\n3. Ringkasan singkat\n4. Saran perbaikan";
        
        return $this->generateContent($prompt, ['temperature' => 0.3]);
    }

    public function suggestTags(string $content)
    {
        $prompt = "Berdasarkan konten berikut, berikan 5-8 tag yang relevan dalam bahasa Indonesia:\n\n{$content}";
        
        return $this->generateContent($prompt, ['temperature' => 0.2]);
    }
}
