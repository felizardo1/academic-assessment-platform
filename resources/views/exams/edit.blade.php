<x-layouts::app.sidebar title="Editar Exame">
    <flux:main>
        <div class="mb-6">
            <flux:heading size="xl">Editar exame</flux:heading>
            <flux:text class="mt-1 text-zinc-500">Atualiza os dados gerais do exame.</flux:text>
        </div>

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

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6">
            <form method="POST" action="{{ route('exams.update', $exam) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <flux:label for="title">Título do exame</flux:label>
                        <flux:input id="title" name="title" type="text"
                            value="{{ old('title', $exam->title) }}" required />
                    </div>

                    <div>
                        <flux:label for="institution">Instituição</flux:label>
                        <flux:input id="institution" name="institution" type="text"
                            value="{{ old('institution', $exam->institution) }}" required />
                    </div>

                    <div>
                        <flux:label for="subject">Disciplina</flux:label>
                        <flux:input id="subject" name="subject" type="text"
                            value="{{ old('subject', $exam->subject) }}" required />
                    </div>

                    <div>
                        <flux:label for="date">Data do exame</flux:label>
                        <flux:input id="date" name="date" type="date" value="{{ old('date', $exam->date) }}"
                            required />
                    </div>

                    <div>
                        <flux:label for="duration">Duração (minutos)</flux:label>
                        <flux:input id="duration" name="duration" type="number"
                            value="{{ old('duration', $exam->duration) }}" min="1" required />
                    </div>

                    <div>
                        <flux:label for="total_points">Nota total</flux:label>
                        <flux:input id="total_points" name="total_points" type="number"
                            value="{{ old('total_points', $exam->total_points) }}" min="1" step="0.5"
                            required />
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <flux:button href="{{ route('exams.show', $exam) }}" variant="ghost" wire:navigate>
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        Guardar alterações
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:main>
</x-layouts::app.sidebar>
