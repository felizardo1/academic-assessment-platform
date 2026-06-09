<?php

namespace Tests\Unit;

use App\Services\DeepSeekService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DeepSeekServiceTest extends TestCase
{
    protected DeepSeekService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DeepSeekService();
    }

    // -------------------------------------------------------------------------
    // buildPrompt
    // -------------------------------------------------------------------------

    /** @test */
    public function build_prompt_includes_chunk_when_provided(): void
    {
        $config = $this->baseConfig();
        $chunk  = 'Conteúdo académico de teste.';

        $prompt = $this->service->buildPrompt($config, $chunk);

        $this->assertStringContainsString($chunk, $prompt);
        $this->assertStringContainsString('Com base no seguinte conteúdo académico', $prompt);
    }

    /** @test */
    public function build_prompt_excludes_context_when_chunk_is_empty(): void
    {
        $config = $this->baseConfig();

        $prompt = $this->service->buildPrompt($config, '');

        $this->assertStringNotContainsString('Com base no seguinte conteúdo académico', $prompt);
    }

    /** @test */
    public function build_prompt_includes_multiple_choice_label_for_correct_type(): void
    {
        $config         = $this->baseConfig();
        $config['type'] = 'multiple_choice';

        $prompt = $this->service->buildPrompt($config);

        $this->assertStringContainsString('escolha múltipla', $prompt);
    }

    /** @test */
    public function build_prompt_includes_open_label_for_open_type(): void
    {
        $config         = $this->baseConfig();
        $config['type'] = 'open';

        $prompt = $this->service->buildPrompt($config);

        $this->assertStringContainsString('abertas', $prompt);
    }

    /** @test */
    public function build_prompt_includes_difficulty_and_scope(): void
    {
        $config = $this->baseConfig();

        $prompt = $this->service->buildPrompt($config);

        $this->assertStringContainsString($config['difficulty'], $prompt);
        $this->assertStringContainsString($config['scope'], $prompt);
    }

    // -------------------------------------------------------------------------
    // parseResponse
    // -------------------------------------------------------------------------

    /** @test */
    public function parse_response_returns_valid_array_from_clean_json(): void
    {
        $json = json_encode(['questions' => [['question_number' => 1, 'question_text' => 'Teste']]]);

        $result = $this->service->parseResponse($json);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('questions', $result);
        $this->assertCount(1, $result['questions']);
    }

    /** @test */
    public function parse_response_strips_markdown_backticks_before_parsing(): void
    {
        $json    = json_encode(['questions' => [['question_number' => 1]]]);
        $wrapped = "```json\n{$json}\n```";

        $result = $this->service->parseResponse($wrapped);

        $this->assertArrayHasKey('questions', $result);
    }

    /** @test */
    public function parse_response_returns_empty_questions_array_for_invalid_json(): void
    {
        $result = $this->service->parseResponse('isto não é json válido');

        $this->assertEquals(['questions' => []], $result);
    }

    /** @test */
    public function parse_response_returns_empty_questions_array_for_empty_string(): void
    {
        $result = $this->service->parseResponse('');

        $this->assertEquals(['questions' => []], $result);
    }

    // -------------------------------------------------------------------------
    // generateQuestions (com mock HTTP)
    // -------------------------------------------------------------------------

    /** @test */
    public function generate_questions_returns_parsed_questions_on_successful_api_response(): void
    {
        $fakeQuestions = [
            'questions' => [
                [
                    'question_number'       => 1,
                    'question_type'         => 'open',
                    'question_text'         => 'O que é um LLM?',
                    'question_value'        => 2,
                    'answer'                => 'Um modelo de linguagem de grande escala.',
                    'avaliation_parameters' => 'Clareza e precisão da resposta.',
                ],
            ],
        ];

        Http::fake([
            'api.deepseek.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => json_encode($fakeQuestions)]],
                ],
            ], 200),
        ]);

        $result = $this->service->generateQuestions($this->baseConfig(), 'Chunk de teste.');

        $this->assertArrayHasKey('questions', $result);
        $this->assertCount(1, $result['questions']);
        $this->assertEquals('O que é um LLM?', $result['questions'][0]['question_text']);
    }

    /** @test */
    public function generate_questions_returns_empty_array_when_api_returns_invalid_json(): void
    {
        Http::fake([
            'api.deepseek.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'resposta inválida']],
                ],
            ], 200),
        ]);

        $result = $this->service->generateQuestions($this->baseConfig());

        $this->assertEquals(['questions' => []], $result);
    }

    // -------------------------------------------------------------------------
    // Helper
    // -------------------------------------------------------------------------

    private function baseConfig(): array
    {
        return [
            'type'          => 'open',
            'num_questions' => 3,
            'difficulty'    => 'medium',
            'points'        => 2,
            'scope'         => 'Introdução aos LLMs',
        ];
    }
}
