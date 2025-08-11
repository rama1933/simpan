<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\BaseController;
use App\Services\AI\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AIController extends BaseController
{
    public function __construct(
        private GeminiService $geminiService
    ) {
    }

    public function index()
    {
        return $this->renderPage('AI/Index', [
            'user' => auth()->user()
        ]);
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'content' => 'required|string|min:10'
        ]);

        $result = $this->geminiService->analyzeKnowledge($request->content);

        if ($result['success']) {
            return $this->jsonSuccess($result['data'], $result['message']);
        }

        return $this->jsonError($result['message']);
    }

    public function suggestTags(Request $request)
    {
        $request->validate([
            'content' => 'required|string|min:10'
        ]);

        $result = $this->geminiService->suggestTags($request->content);

        if ($result['success']) {
            return $this->jsonSuccess($result['data'], $result['message']);
        }

        return $this->jsonError($result['message']);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|min:5',
            'options' => 'array'
        ]);

        $result = $this->geminiService->generateContent(
            $request->prompt,
            $request->options ?? []
        );

        if ($result['success']) {
            return $this->jsonSuccess($result['data'], $result['message']);
        }

        return $this->jsonError($result['message']);
    }

    public function draftFromTitle(Request $request)
    {
        $request->validate([
            'title' => 'required|string|min:5'
        ]);

        $title = trim($request->input('title'));
        $prompt = "Buatkan draft konten pengetahuan berdasarkan judul berikut:\n\nJudul: {$title}\n\nBalas HANYA dalam format JSON valid tanpa teks tambahan dengan schema:\n{\n  \"description\": string,  // 1-2 kalimat ringkas Bahasa Indonesia\n  \"content\": string,      // 5-10 paragraf markdown Bahasa Indonesia\n  \"tags\": string[]         // 5-8 tag relevan satu kata/frasa pendek\n}\n\nPastikan output hanya JSON valid.";

        $result = $this->geminiService->generateContent($prompt, ['temperature' => 0.35]);
        if (!$result['success']) {
            return $this->jsonError($result['message']);
        }

        $raw = (string)($result['data']['content'] ?? '');
        // Coba decode JSON langsung
        $json = null;
        // Jika model mengembalikan dalam blok code, ambil isi di dalamnya
        if (preg_match('/\{[\s\S]*\}/', $raw, $m)) {
            $jsonStr = $m[0];
            $json = json_decode($jsonStr, true);
        } else {
            $json = json_decode($raw, true);
        }

        if (!is_array($json)) {
            // Fallback ekstraksi sederhana
            $desc = mb_substr($raw, 0, 300);
            $tags = collect(preg_split('/[,\n\-•\u2022]/u', $raw))
                ->map(fn ($t) => trim(preg_replace('/^\d+\.?\s*/', '', (string)$t)))
                ->filter()
                ->unique()
                ->take(8)
                ->values()
                ->all();
            $json = [
                'description' => $desc,
                'content' => $raw,
                'tags' => $tags,
            ];
        }

        // Normalisasi field
        $data = [
            'description' => (string)($json['description'] ?? ''),
            'content' => (string)($json['content'] ?? ''),
            'tags' => array_values(array_unique(array_map('strval', (array)($json['tags'] ?? []))))
        ];

        return $this->jsonSuccess($data, 'Draft dibuat');
    }
}
