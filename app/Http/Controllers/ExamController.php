<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamMaterial;
use App\Services\DocumentProcessorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Auth::user()->exams()->latest()->get();
        return view('exams.index', compact('exams'));
    }

    public function create()
    {
        return view('exams.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'institution'  => 'required|string|max:255',
            'subject'      => 'required|string|max:255',
            'date'         => 'required|date',
            'duration'     => 'required|integer|min:1',
            'total_points' => 'required|numeric|min:1',
            'material'     => 'nullable|file|mimes:pdf,docx,txt|max:10240',
        ]);

        $exam = Auth::user()->exams()->create($validated);

        if ($request->hasFile('material')) {
            $file     = $request->file('material');
            $filename = $file->getClientOriginalName();
            $fileType = $file->getClientOriginalExtension();
            $path     = $file->store("materials/{$exam->id}", 'local');

            ExamMaterial::create([
                'exam_id'   => $exam->id,
                'filename'  => $filename,
                'file_path' => $path,
                'file_type' => $fileType,
            ]);
        }

        return redirect()->route('exams.show', $exam)
            ->with('success', 'Exame criado com sucesso! Agora adiciona as sessões.');
    }

    public function show(Exam $exam)
    {
        $this->authorize('view', $exam);
        $exam->load('sections.questions.options', 'materials');
        return view('exams.show', compact('exam'));
    }

    public function edit(Exam $exam)
    {
        $this->authorize('update', $exam);
        return view('exams.edit', compact('exam'));
    }

    public function update(Request $request, Exam $exam)
    {
        $this->authorize('update', $exam);

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'institution'  => 'required|string|max:255',
            'subject'      => 'required|string|max:255',
            'date'         => 'required|date',
            'duration'     => 'required|integer|min:1',
            'total_points' => 'required|numeric|min:1',
        ]);

        $exam->update($validated);

        return redirect()->route('exams.show', $exam)
            ->with('success', 'Exame atualizado com sucesso!');
    }

    public function destroy(Exam $exam)
    {
        $this->authorize('delete', $exam);
        $exam->delete();

        return redirect()->route('exams.index')
            ->with('success', 'Exame eliminado com sucesso!');
    }
}
