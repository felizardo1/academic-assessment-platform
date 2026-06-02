<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Services\PdfGeneratorService;
use Illuminate\Support\Facades\Storage;

class PdfController extends Controller
{
    public function generate(Exam $exam)
    {
        $this->authorize('view', $exam);

        $pdfService = new PdfGeneratorService();
        $pdfService->generateExam($exam);
        $pdfService->generateCorrectionGuide($exam);

        return redirect()->route('exams.show', $exam)
            ->with('success', 'PDFs gerados com sucesso!');
    }

    public function downloadExam(Exam $exam)
    {
        $this->authorize('view', $exam);

        $path = "exams/exam_{$exam->id}.pdf";

        if (!Storage::exists($path)) {
            $pdfService = new PdfGeneratorService();
            $pdfService->generateExam($exam);
        }

        return Storage::download($path, "{$exam->title}_exame.pdf");
    }

    public function downloadCorrection(Exam $exam)
    {
        $this->authorize('view', $exam);

        $path = "exams/correction_{$exam->id}.pdf";

        if (!Storage::exists($path)) {
            $pdfService = new PdfGeneratorService();
            $pdfService->generateCorrectionGuide($exam);
        }

        return Storage::download($path, "{$exam->title}_correcao.pdf");
    }
}
