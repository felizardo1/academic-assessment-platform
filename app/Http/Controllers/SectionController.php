<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Section;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\AnswerKey;
use App\Services\DeepSeekService;
use App\Services\DocumentProcessorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectionController extends Controller
{
    public function store(Request $request, Exam $exam)
    {
        $this->authorize('update', $exam);

        $validated = $request->validate([
            'name'          => 'nullable|string|max:255',
            'type'          => 'required|in:open,multiple_choice',
            'source'        => 'required|in:ai,manual',
            'num_questions' => 'required|integer|min:1|max:50',
            'points'        => 'required|numeric|min:0.5',
            'difficulty'    => 'required|in:easy,medium,hard',
            'scope'         => 'nullable|string',
            'questions'     => 'nullable|array',
            'questions.*.content'  => 'required_if:source,manual|string',
            'questions.*.answer'   => 'required_if:source,manual|string',
            'questions.*.options'  => 'nullable|array',
        ]);

        $order   = $exam->sections()->count() + 1;
        $section = $exam->sections()->create([
            'name'  => $validated['name'] ?? null,
            'order' => $order,
        ]);

        if ($validated['source'] === 'ai') {
            $this->generateAiQuestions($exam, $section, $validated);
        } else {
            $this->saveManualQuestions($section, $validated);
        }

        return redirect()->route('exams.show', $exam)
            ->with('success', 'Sessão adicionada com sucesso!');
    }

    public function destroy(Section $section)
    {
        $this->authorize('update', $section->exam);
        $section->delete();

        return back()->with('success', 'Sessão eliminada com sucesso!');
    }

    private function generateAiQuestions(Exam $exam, Section $section, array $config): void
    {
        $deepSeek = new DeepSeekService();
        $chunk    = '';

        if ($exam->materials->isNotEmpty()) {
            $material  = $exam->materials->first();
            $processor = new DocumentProcessorService();
            $chunks    = $processor->extractAndChunk(
                storage_path('app/private/' . $material->file_path),
                $material->file_type
            );
            $chunk = $chunks[0] ?? '';
        }

        $result    = $deepSeek->generateQuestions($config, $chunk);
        $questions = $result['questions'] ?? [];

        foreach ($questions as $index => $q) {
            $question = $section->questions()->create([
                'type'       => $q['question_type'],
                'source'     => 'ai',
                'content'    => $q['question_text'],
                'points'     => $q['question_value'],
                'difficulty' => $config['difficulty'],
                'order'      => $index + 1,
            ]);

            AnswerKey::create([
                'question_id'         => $question->id,
                'expected_answer'     => $q['answer'],
                'correction_criteria' => $q['avaliation_parameters'] ?? null,
            ]);

            if ($q['question_type'] === 'multiple_choice' && isset($q['options'])) {
                foreach ($q['options'] as $option) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $option['option_value'],
                        'is_correct'  => $option['option_is_correct'],
                    ]);
                }
            }
        }
    }

    private function saveManualQuestions(Section $section, array $config): void
    {
        $questions = $config['questions'] ?? [];

        foreach ($questions as $index => $q) {
            $question = $section->questions()->create([
                'type'       => $config['type'],
                'source'     => 'manual',
                'content'    => $q['content'],
                'points'     => $config['points'],
                'difficulty' => $config['difficulty'],
                'order'      => $index + 1,
            ]);

            AnswerKey::create([
                'question_id'     => $question->id,
                'expected_answer' => $q['answer'],
            ]);

            if ($config['type'] === 'multiple_choice' && isset($q['options'])) {
                foreach ($q['options'] as $option) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $option['option_value'],
                        'is_correct'  => $option['option_is_correct'],
                    ]);
                }
            }
        }
    }
}
