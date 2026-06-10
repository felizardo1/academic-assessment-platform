<?php

namespace Tests\Unit;

use App\Models\Exam;
use App\Services\PdfGeneratorService;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PdfGeneratorServiceTest extends TestCase
{
    protected PdfGeneratorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake();
        $this->bindFakePdf();
        $this->service = new PdfGeneratorService();
    }

    #[Test]
    public function generate_exam_returns_correct_storage_path(): void
    {
        $exam = $this->fakeExam();
        $path = $this->service->generateExam($exam);

        $this->assertEquals("exams/exam_{$exam->id}.pdf", $path);
    }

    #[Test]
    public function generate_exam_stores_file_in_storage(): void
    {
        $exam = $this->fakeExam();
        $path = $this->service->generateExam($exam);

        Storage::assertExists($path);
    }

    #[Test]
    public function generate_exam_stores_file_inside_exams_directory(): void
    {
        $exam = $this->fakeExam();
        $path = $this->service->generateExam($exam);

        $this->assertStringStartsWith('exams/', $path);
    }

    #[Test]
    public function generate_correction_guide_returns_correct_storage_path(): void
    {
        $exam = $this->fakeExam();
        $path = $this->service->generateCorrectionGuide($exam);

        $this->assertEquals("exams/correction_{$exam->id}.pdf", $path);
    }

    #[Test]
    public function generate_correction_guide_stores_file_in_storage(): void
    {
        $exam = $this->fakeExam();
        $path = $this->service->generateCorrectionGuide($exam);

        Storage::assertExists($path);
    }

    #[Test]
    public function generate_correction_guide_stores_file_inside_exams_directory(): void
    {
        $exam = $this->fakeExam();
        $path = $this->service->generateCorrectionGuide($exam);

        $this->assertStringStartsWith('exams/', $path);
    }

    #[Test]
    public function exam_and_correction_guide_are_stored_with_different_filenames(): void
    {
        $exam      = $this->fakeExam();
        $examPath  = $this->service->generateExam($exam);
        $guidePath = $this->service->generateCorrectionGuide($exam);

        $this->assertNotEquals($examPath, $guidePath);
    }

    private function fakeExam(): Exam
    {
        $exam     = $this->createMock(Exam::class);
        $exam->id = 42;
        $exam->method('load')->willReturnSelf();

        return $exam;
    }

    private function bindFakePdf(): void
    {
        $fakePdf = new class {
            public function loadView(string $view, array $data = []): static
            {
                return $this;
            }
            public function output(): string
            {
                return '%PDF-fake-content';
            }
        };

        \Barryvdh\DomPDF\Facade\Pdf::swap($fakePdf);
    }
}
