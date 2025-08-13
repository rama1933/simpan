<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'context' => 'nullable|array'
        ]);

        try {
            // Menggunakan API key gratis Gemini
            $apiKey = env('GEMINI_API_KEY');
            
            if (!$apiKey) {
                return response()->json([
                    'error' => 'API key tidak dikonfigurasi'
                ], 500);
            }

            $context = $request->input('context', []);
            $userMessage = $request->input('message');
            
            // Buat prompt dengan konteks pengetahuan
            $prompt = $this->buildPrompt($userMessage, $context);
            
            // Panggil Gemini API
            $model = env('GEMINI_MODEL', 'gemini-1.5-flash');
            $response = Http::timeout(30)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => $prompt
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => 1024,
                    ]
                ]);

            if (!$response->successful()) {
                Log::error('Gemini API Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return response()->json([
                    'response' => 'Maaf, layanan AI sedang tidak tersedia. Silakan coba lagi nanti.'
                ]);
            }

            $data = $response->json();
            
            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                $aiResponse = $data['candidates'][0]['content']['parts'][0]['text'];
                
                return response()->json([
                    'response' => $aiResponse
                ]);
            }
            
            return response()->json([
                'response' => 'Maaf, tidak dapat memproses permintaan Anda saat ini.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Gemini Chat Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'response' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ]);
        }
    }
    
    private function buildPrompt($userMessage, $context)
    {
        // Cek apakah ini untuk halaman public knowledge (seluruh pengetahuan)
        $isPublicKnowledge = isset($context['knowledge_content']) && 
                           $context['knowledge_content'] === 'Seluruh pengetahuan yang tersedia di aplikasi';
        
        if ($isPublicKnowledge) {
            // Prompt untuk AI Assistant di halaman public knowledge
            $prompt = "Anda adalah AI Assistant untuk sistem manajemen pengetahuan.\n\n";
            
            $prompt .= "PERAN ANDA:\n";
            $prompt .= "1. Membantu pengguna memahami dan menjelajahi sistem manajemen pengetahuan\n";
            $prompt .= "2. Memberikan informasi umum tentang manajemen pengetahuan\n";
            $prompt .= "3. Membantu pengguna menemukan dan memahami konsep-konsep pengetahuan\n";
            $prompt .= "4. Memberikan saran tentang cara mengorganisir dan mengelola pengetahuan\n\n";
            
            $prompt .= "KEMAMPUAN ANDA:\n";
            $prompt .= "- Menjelaskan konsep manajemen pengetahuan\n";
            $prompt .= "- Memberikan tips pengorganisasian dokumen\n";
            $prompt .= "- Membantu pencarian informasi\n";
            $prompt .= "- Menjawab pertanyaan umum tentang sistem\n\n";
            
            $prompt .= "=== PERTANYAAN PENGGUNA ===\n{$userMessage}\n\n";
            $prompt .= "Jawab dalam bahasa Indonesia dengan ramah dan informatif. ";
            $prompt .= "Fokus pada membantu pengguna memahami dan menggunakan sistem manajemen pengetahuan dengan efektif.";
        } else {
            // Prompt untuk dokumen spesifik (halaman detail)
            $prompt = "Anda adalah AI Assistant khusus untuk membantu pengguna memahami dokumen pengetahuan tertentu.\n\n";
            
            $prompt .= "ATURAN PENTING:\n";
            $prompt .= "1. Anda HANYA boleh menjawab pertanyaan yang berkaitan dengan dokumen pengetahuan yang diberikan\n";
            $prompt .= "2. Jika pertanyaan tidak berkaitan dengan dokumen ini, jawab: 'Maaf, saya hanya dapat membantu menjawab pertanyaan terkait dokumen pengetahuan ini. Silakan ajukan pertanyaan yang berkaitan dengan konten dokumen.'\n";
            $prompt .= "3. Gunakan HANYA informasi dari dokumen yang diberikan untuk menjawab\n";
            $prompt .= "4. Jika informasi tidak tersedia dalam dokumen, katakan bahwa informasi tersebut tidak tersedia dalam dokumen ini\n\n";
            
            if (!empty($context['knowledge_title'])) {
                $prompt .= "JUDUL DOKUMEN: {$context['knowledge_title']}\n";
            }
            
            if (!empty($context['knowledge_description'])) {
                $prompt .= "DESKRIPSI DOKUMEN: {$context['knowledge_description']}\n";
            }
            
            if (!empty($context['knowledge_content'])) {
                // Batasi konten untuk menghindari token limit
                $content = substr($context['knowledge_content'], 0, 2000);
                $prompt .= "KONTEN DOKUMEN:\n{$content}\n";
            }
            
            $prompt .= "\n=== PERTANYAAN PENGGUNA ===\n{$userMessage}\n\n";
            $prompt .= "Jawab dalam bahasa Indonesia dan pastikan jawaban Anda hanya berdasarkan dokumen di atas. ";
            $prompt .= "Jika pertanyaan tidak berkaitan dengan dokumen ini, tolak dengan sopan sesuai aturan nomor 2.";
        }
        
        return $prompt;
    }
}