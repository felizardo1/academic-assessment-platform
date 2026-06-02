<?php

namespace App\Services;

use App\Models\Exam;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfGeneratorService
{
    public function generateExam(Exam $exam): string
    {
        $exam->load('sections.questions.options');

        $pdf  = Pdf::loadView('pdf.exam', compact('exam'));
        $path = "exams/exam_{$exam->id}.pdf";

        Storage::put($path, $pdf->output());

        return $path;
    }

    public function generateCorrectionGuide(Exam $exam): string
    {
        $exam->load('sections.questions.options.question', 'sections.questions.answerKey');

        $pdf  = Pdf::loadView('pdf.correction_guide', compact('exam'));
        $path = "exams/correction_{$exam->id}.pdf";

        Storage::put($path, $pdf->output());

        return $path;
    }
}
