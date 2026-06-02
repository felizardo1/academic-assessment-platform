<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DeepSeekService
{
    protected string $apiKey;
    protected string $apiUrl = 'https://api.deepseek.com/v1/chat/completions';
    protected string $model = 'deepseek-chat';

    public function __construct()
    {
        $this->apiKey = config('services.deepseek.key');
    }

    public function generateQuestions(array $sessionConfig, string $documentChunk = ''): array
    {
        $prompt = $this->buildPrompt($sessionConfig, $documentChunk);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type'  => 'application/json',
        ])->post($this->apiUrl, [
            'model'    => $this->model,
            'messages' => [
                [
                    'role'    => 'system',
                    'content' => 'És um assistente especializado em criação de avaliações académicas. Responde SEMPRE em JSON válido, sem texto adicional, sem markdown, sem backticks.',
                ],
                [
                    'role'    => 'user',
                    'content' => $prompt,
                ],
            ],
            'temperature' => 0.7,
        ]);

        $content = $response->json('choices.0.message.content');
        return $this->parseResponse($content);
    }

    public function buildPrompt(array $config, string $chunk = ''): string
    {
        $context = $chunk ? "Com base no seguinte conteúdo académico:\n\n{$chunk}\n\n" : '';

        $typeLabel = $config['type'] === 'multiple_choice' ? 'escolha múltipla (com 4 opções cada)' : 'abertas';

        return "{$context}Gera {$config['num_questions']} questões do tipo {$typeLabel},
        com nível de dificuldade {$config['difficulty']},
        cada questão vale {$config['points']} pontos,
        sobre o seguinte tema: {$config['scope']}.

        Para cada questão inclui a resposta esperada e critérios de avaliação detalhados.

        Retorna APENAS um JSON válido com esta estrutura exata:
        {
        \"questions\": [
            {
            \"question_number\": 1,
            \"question_type\": \"open\",
            \"question_text\": \"...\",
            \"question_value\": {$config['points']},
            \"answer\": \"...\",
            \"avaliation_parameters\": \"...\"
            },
            {
            \"question_number\": 2,
            \"question_type\": \"multiple_choice\",
            \"question_text\": \"...\",
            \"question_value\": {$config['points']},
            \"answer\": \"...\",
            \"avaliation_parameters\": \"...\",
            \"options\": [
                {\"option_value\": \"...\", \"option_is_correct\": true},
                {\"option_value\": \"...\", \"option_is_correct\": false},
                {\"option_value\": \"...\", \"option_is_correct\": false},
                {\"option_value\": \"...\", \"option_is_correct\": false}
            ]
            }
        ]
        }";
    }

    public function parseResponse(string $content): array
    {
        $clean = preg_replace('/```json|```/', '', $content);
        $clean = trim($clean);
        return json_decode($clean, true) ?? ['questions' => []];
    }
}
