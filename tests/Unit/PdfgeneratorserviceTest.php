<?php

namespace Tests\Unit;

use App\Models\Exam;
use App\Services\PdfGeneratorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PdfGeneratorServiceTest extends TestCase
{
    protected PdfGeneratorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PdfGeneratorService();
    }

    // -------------------------------------------------------------------------
    // generateExam
    // -------------------------------------------------------------------------

    /** @test */
    public function generate_exam_returns_correct_storage_path(): void
    {
        Storage::fake();
        $this->mockPdf();

        $exam = $this->fakeExam();

        $path = $this->service->generateExam($exam);

        $this->assertEquals("exams/exam_{$exam->id}.pdf", $path);
    }

    /** @test */
    public function generate_exam_stores_file_in_storage(): void
    {
        Storage::fake();
        $this->mockPdf();

        $exam = $this->fakeExam();

        $path = $this->service->generateExam($exam);

        Storage::assertExists($path);
    }

    /** @test */
    public function generate_exam_stores_file_inside_exams_directory(): void
    {
        Storage::fake();
        $this->mockPdf();

        $exam = $this->fakeExam();
        $path = $this->service->generateExam($exam);

        $this->assertStringStartsWith('exams/', $path);
    }

    // -------------------------------------------------------------------------
    // generateCorrectionGuide
    // -------------------------------------------------------------------------

    /** @test */
    public function generate_correction_guide_returns_correct_storage_path(): void
    {
        Storage::fake();
        $this->mockPdf();

        $exam = $this->fakeExam();
        $path = $this->service->generateCorrectionGuide($exam);

        $this->assertEquals("exams/correction_{$exam->id}.pdf", $path);
    }

    /** @test */
    public function generate_correction_guide_stores_file_in_storage(): void
    {
        Storage::fake();
        $this->mockPdf();

        $exam = $this->fakeExam();
        $path = $this->service->generateCorrectionGuide($exam);

        Storage::assertExists($path);
    }

    /** @test */
    public function generate_correction_guide_stores_file_inside_exams_directory(): void
    {
        Storage::fake();
        $this->mockPdf();

        $exam = $this->fakeExam();
        $path = $this->service->generateCorrectionGuide($exam);

        $this->assertStringStartsWith('exams/', $path);
    }

    /** @test */
    public function exam_and_correction_guide_are_stored_with_different_filenames(): void
    {
        Storage::fake();
        $this->mockPdf();

        $exam      = $this->fakeExam();
        $examPath  = $this->service->generateExam($exam);
        $guidePath = $this->service->generateCorrectionGuide($exam);

        $this->assertNotEquals($examPath, $guidePath);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function fakeExam(): Exam
    {
        $exam = $this->createMock(Exam::class);

        $exam->id = 42;

        // load() não deve lançar erro durante os testes
        $exam->method('load')->willReturnSelf();

        return $exam;
    }

    private function mockPdf(): void
    {
        $pdfMock = \Mockery::mock('overload:' . \Barryvdh\DomPDF\PDF::class);
        $pdfMock->shouldReceive('output')->andReturn('%PDF fake content');

        Pdf::shouldReceive('loadView')->andReturn($pdfMock);
    }
}
