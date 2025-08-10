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
}
