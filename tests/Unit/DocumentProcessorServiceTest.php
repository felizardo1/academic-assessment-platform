<?php

namespace Tests\Unit;

use App\Services\DocumentProcessorService;
use Tests\TestCase;

class DocumentProcessorServiceTest extends TestCase
{
    protected DocumentProcessorService $service;
    protected string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DocumentProcessorService();
        $this->tempDir = sys_get_temp_dir();
    }

    // -------------------------------------------------------------------------
    // extract — TXT
    // -------------------------------------------------------------------------

    /** @test */
    public function extract_returns_content_from_txt_file(): void
    {
        $path = $this->tempDir . '/test_extract.txt';
        file_put_contents($path, 'Conteúdo de teste para extracção.');

        $result = $this->service->extract($path, 'txt');

        $this->assertEquals('Conteúdo de teste para extracção.', $result);

        unlink($path);
    }

    /** @test */
    public function extract_is_case_insensitive_for_file_type(): void
    {
        $path = $this->tempDir . '/test_case.txt';
        file_put_contents($path, 'Teste maiúsculas.');

        $result = $this->service->extract($path, 'TXT');

        $this->assertEquals('Teste maiúsculas.', $result);

        unlink($path);
    }

    /** @test */
    public function extract_throws_exception_for_unsupported_file_type(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/não suportado/');

        $this->service->extract('/fake/path/file.xyz', 'xyz');
    }

    // -------------------------------------------------------------------------
    // chunk
    // -------------------------------------------------------------------------

    /** @test */
    public function chunk_returns_single_chunk_for_short_text(): void
    {
        $text   = 'Este é um texto curto.';
        $chunks = $this->service->chunk($text);

        $this->assertCount(1, $chunks);
        $this->assertEquals($text, $chunks[0]);
    }

    /** @test */
    public function chunk_splits_long_text_into_multiple_chunks(): void
    {
        // Gera texto com mais de 3000 caracteres
        $text   = implode(' ', array_fill(0, 600, 'palavra'));
        $chunks = $this->service->chunk($text);

        $this->assertGreaterThan(1, count($chunks));
    }

    /** @test */
    public function chunk_respects_maximum_chunk_size(): void
    {
        $text   = implode(' ', array_fill(0, 600, 'palavra'));
        $chunks = $this->service->chunk($text);

        foreach ($chunks as $chunk) {
            $this->assertLessThanOrEqual(3000, strlen($chunk));
        }
    }

    /** @test */
    public function chunk_does_not_truncate_words_between_chunks(): void
    {
        $text   = implode(' ', array_fill(0, 600, 'palavra'));
        $chunks = $this->service->chunk($text);

        foreach ($chunks as $chunk) {
            // Cada chunk deve começar e terminar com uma palavra completa
            $this->assertMatchesRegularExpression('/^\S/', $chunk);
            $this->assertMatchesRegularExpression('/\S$/', $chunk);
        }
    }

    /** @test */
    public function chunk_normalises_multiple_spaces_in_text(): void
    {
        $text   = 'Palavra1   Palavra2     Palavra3';
        $chunks = $this->service->chunk($text);

        $this->assertStringNotContainsString('  ', $chunks[0]);
    }

    /** @test */
    public function chunk_returns_empty_array_for_empty_string(): void
    {
        $chunks = $this->service->chunk('');

        $this->assertIsArray($chunks);
        $this->assertCount(0, $chunks);
    }

    /** @test */
    public function chunk_trims_whitespace_from_each_chunk(): void
    {
        $text   = implode(' ', array_fill(0, 600, 'palavra'));
        $chunks = $this->service->chunk($text);

        foreach ($chunks as $chunk) {
            $this->assertEquals(trim($chunk), $chunk);
        }
    }

    // -------------------------------------------------------------------------
    // extractAndChunk
    // -------------------------------------------------------------------------

    /** @test */
    public function extract_and_chunk_returns_array_of_chunks_from_txt(): void
    {
        $content = implode(' ', array_fill(0, 600, 'palavra'));
        $path    = $this->tempDir . '/test_chunk.txt';
        file_put_contents($path, $content);

        $chunks = $this->service->extractAndChunk($path, 'txt');

        $this->assertIsArray($chunks);
        $this->assertGreaterThan(0, count($chunks));

        unlink($path);
    }

    /** @test */
    public function extract_and_chunk_returns_single_chunk_for_short_txt(): void
    {
        $path = $this->tempDir . '/test_short.txt';
        file_put_contents($path, 'Texto curto.');

        $chunks = $this->service->extractAndChunk($path, 'txt');

        $this->assertCount(1, $chunks);

        unlink($path);
    }
}
