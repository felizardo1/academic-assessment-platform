<x-layouts::app.sidebar>
    <flux:main>
        {{-- Header --}}
        <div class="mb-6 flex items-start justify-between">
            <div>
                <flux:heading size="xl">{{ $exam->title }}</flux:heading>
                <flux:text class="mt-1 text-zinc-500">
                    {{ $exam->institution }} · {{ $exam->subject }} ·
                    {{ \Carbon\Carbon::parse($exam->date)->format('d/m/Y') }} · {{ $exam->duration }} min ·
                    {{ $exam->total_points }} pts
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:button href="{{ route('exams.download.exam', $exam) }}" variant="primary" size="sm">
                    <flux:icon name="arrow-down-tray" class="w-4 h-4 mr-1" />
                    Exam PDF
                </flux:button>
                <flux:button href="{{ route('exams.download.correction', $exam) }}" variant="ghost" size="sm">
                    <flux:icon name="arrow-down-tray" class="w-4 h-4 mr-1" />
                    Correction guide
                </flux:button>
                <flux:button href="{{ route('exams.edit', $exam) }}" variant="ghost" size="sm" wire:navigate>
                    <flux:icon name="pencil" class="w-4 h-4" />
                </flux:button>
            </div>
        </div>

        @if (session('success'))
            <div
                class="mb-4 rounded-lg bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 px-4 py-3 text-sm text-green-700 dark:text-green-300">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div
                class="mb-4 rounded-lg bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 px-4 py-3 text-sm text-red-700 dark:text-red-300">
                {{ session('error') }}
            </div>
        @endif

        {{-- Sessões existentes --}}
        @if ($exam->sections->isNotEmpty())
            <div class="mb-6 space-y-4">
                @foreach ($exam->sections as $index => $section)
                    <div
                        class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden">
                        <div
                            class="flex items-center justify-between px-5 py-3 bg-blue-50 dark:bg-blue-900 border-b border-blue-100 dark:border-blue-800">
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-semibold text-blue-700 dark:text-blue-300">
                                    Session {{ $index + 1 }}
                                    @if ($section->name)
                                        — {{ $section->name }}
                                    @endif
                                </span>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-800 dark:text-blue-200">
                                    {{ $section->questions->count() }} questions
                                </span>
                            </div>
                            <form method="POST" action="{{ route('sections.destroy', $section) }}">
                                @csrf
                                @method('DELETE')
                                <flux:button type="submit" size="xs" variant="danger"
                                    onclick="return confirm('Eliminar esta sessão?')">
                                    Delete
                                </flux:button>
                            </form>
                        </div>

                        <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach ($section->questions as $qIndex => $question)
                                <div class="flex gap-4 px-5 py-4">
                                    <div
                                        class="flex-shrink-0 w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-semibold">
                                        {{ $qIndex + 1 }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between gap-4">
                                            <p class="text-sm text-zinc-800 dark:text-zinc-200">
                                                {{ $question->content }}
                                            </p>
                                            <div class="flex gap-2 flex-shrink-0">
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                    @if ($question->difficulty === 'easy') bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300
                                                    @elseif($question->difficulty === 'medium') bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300
                                                    @else bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300 @endif">
                                                    @if ($question->difficulty === 'easy')
                                                        Fácil
                                                    @elseif($question->difficulty === 'medium')
                                                        Médio
                                                    @else
                                                        Difícil
                                                    @endif
                                                </span>
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                                                    {{ $question->points }} pts
                                                </span>
                                            </div>
                                        </div>

                                        @if ($question->type === 'multiple_choice' && $question->options->isNotEmpty())
                                            <div class="mt-2 space-y-1">
                                                @foreach ($question->options as $oIndex => $option)
                                                    <div
                                                        class="flex items-center gap-2 text-xs {{ $option->is_correct ? 'text-green-600 dark:text-green-400 font-medium' : 'text-zinc-500' }}">
                                                        <span>{{ chr(65 + $oIndex) }})</span>
                                                        <span>{{ $option->option_text }}</span>
                                                        @if ($option->is_correct)
                                                            <flux:icon name="check-circle" class="w-3 h-3" />
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Formulário para adicionar sessão --}}
        <div
            class="rounded-xl border-2 border-dashed border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 p-6">
            <flux:heading size="lg" class="mb-4">
                <flux:icon name="plus-circle" class="w-5 h-5 inline mr-2 text-blue-500" />
                Add new Session
            </flux:heading>

            @if ($errors->any())
                <div
                    class="mb-4 rounded-lg bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 px-4 py-3 text-sm text-red-700 dark:text-red-300">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('sections.store', $exam) }}" id="section-form">
                @csrf

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <flux:label for="name">Session Name</flux:label>
                        <flux:input id="name" name="name" type="text"
                            placeholder="Ex: Conceitos Gerais (opcional)" value="{{ old('name') }}" />
                    </div>

                    <div>
                        <flux:label for="type">Question type</flux:label>
                        <flux:select id="type" name="type">
                            <option value="open" {{ old('type') === 'open' ? 'selected' : '' }}>Open questions
                            </option>
                            <option value="multiple_choice" {{ old('type') === 'multiple_choice' ? 'selected' : '' }}>
                                Multiple choice</option>
                        </flux:select>
                    </div>

                    <div>
                        <flux:label for="source">Source</flux:label>
                        <flux:select id="source" name="source" onchange="toggleSource(this.value)">
                            <option value="ai" {{ old('source') === 'ai' ? 'selected' : '' }}>Generate with AI</option>
                            <option value="manual" {{ old('source') === 'manual' ? 'selected' : '' }}>Insert manually
                            </option>
                        </flux:select>
                    </div>

                    <div>
                        <flux:label for="num_questions">Number of questions</flux:label>
                        <flux:input id="num_questions" name="num_questions" type="number" placeholder="5"
                            min="1" max="50" value="{{ old('num_questions') }}" required />
                    </div>

                    <div>
                        <flux:label for="points">Points for question</flux:label>
                        <flux:input id="points" name="points" type="number" placeholder="10" min="0.5"
                            step="0.5" value="{{ old('points') }}" required />
                    </div>

                    <div>
                        <flux:label for="difficulty">Difficulty level</flux:label>
                        <flux:select id="difficulty" name="difficulty">
                            <option value="easy" {{ old('difficulty') === 'easy' ? 'selected' : '' }}>Fácil</option>
                            <option value="medium" {{ old('difficulty') === 'medium' ? 'selected' : '' }}>Médio
                            </option>
                            <option value="hard" {{ old('difficulty') === 'hard' ? 'selected' : '' }}>Difícil
                            </option>
                        </flux:select>
                    </div>
                </div>

                {{-- Scope / content --}}
                <div id="ai-fields" class="mt-4">
                    <flux:label for="scope">Scope / content</flux:label>
                    <flux:textarea id="scope" name="scope" rows="3"
                        placeholder="Example: History of IT focusing on key dates and events. All discussed in unit 2.3.">
                        {{ old('scope') }}</flux:textarea>
                </div>

                {{-- Manual questions --}}
                <div id="manual-fields" class="mt-4 hidden">
                    <flux:heading size="sm" class="mb-3">Manual questions</flux:heading>
                    <div id="manual-questions-container" class="space-y-4"></div>
                    <flux:button type="button" variant="ghost" size="sm" class="mt-3"
                        onclick="addManualQuestion()">
                        + Add question
                    </flux:button>
                </div>

                <div class="mt-6 flex justify-end">
                    <flux:button type="submit" variant="primary">
                        <flux:icon name="plus" class="w-4 h-4 mr-1" />
                        Add Session
                    </flux:button>
                </div>
            </form>
        </div>

    </flux:main>
</x-layouts::app.sidebar>

<script>
    let questionCount = 0;

    function toggleSource(value) {
        const aiFields = document.getElementById('ai-fields');
        const manualFields = document.getElementById('manual-fields');
        if (value === 'manual') {
            aiFields.classList.add('hidden');
            manualFields.classList.remove('hidden');
        } else {
            aiFields.classList.remove('hidden');
            manualFields.classList.add('hidden');
        }
    }

    function addManualQuestion() {
        const container = document.getElementById('manual-questions-container');
        const index = questionCount++;
        const type = document.getElementById('type').value;

        const div = document.createElement('div');
        div.className = 'rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 space-y-3';
        div.innerHTML = `
                <div>
                    <label class="text-sm text-zinc-500 block mb-1">Question ${index + 1}</label>
                    <textarea name="questions[${index}][content]" rows="2" required
                        class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm px-3 py-2"
                        placeholder="Write the statement of the question...."></textarea>
                </div>
                <div>
                    <label class="text-sm text-zinc-500 block mb-1">Expected answer</label>
                    <textarea name="questions[${index}][answer]" rows="2" required
                        class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm px-3 py-2"
                        placeholder="write the expected answer..."></textarea>
                </div>
                ${type === 'multiple_choice' ? `
                <div>
                    <label class="text-sm text-zinc-500 block mb-1">Options (mark the correct one)</label>
                    ${[0,1,2,3].map(i => `
                        <div class="flex items-center gap-2 mb-1">
                            <input type="radio" name="questions[${index}][correct]" value="${i}" ${i === 0 ? 'checked' : ''}>
                            <input type="text" name="questions[${index}][options][${i}][option_value]" required
                                class="flex-1 rounded border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm px-2 py-1"
                                placeholder="Opção ${String.fromCharCode(65+i)}">
                            <input type="hidden" name="questions[${index}][options][${i}][option_is_correct]" value="0">
                        </div>
                    `).join('')}
                </div>` : ''}
            `;
        container.appendChild(div);
    }
</script>
