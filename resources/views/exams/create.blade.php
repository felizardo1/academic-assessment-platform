<x-layouts::app.sidebar>
    <flux:main>
        <div class="mb-6">
            <flux:heading size="xl">Novo exame</flux:heading>
            <flux:text class="mt-1 text-zinc-500">Preenche os dados gerais do exame.</flux:text>
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
            <form method="POST" action="{{ route('exams.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <flux:label for="title">Título do exame</flux:label>
                        <flux:input id="title" name="title" type="text"
                            placeholder="Ex: Avaliação Final — Introdução à TI" value="{{ old('title') }}" required />
                    </div>

                    <div>
                        <flux:label for="institution">Instituição</flux:label>
                        <flux:input id="institution" name="institution" type="text"
                            placeholder="Ex: IU Internacional" value="{{ old('institution') }}" required />
                    </div>

                    <div>
                        <flux:label for="subject">Disciplina</flux:label>
                        <flux:input id="subject" name="subject" type="text" placeholder="Ex: Introdução à TI"
                            value="{{ old('subject') }}" required />
                    </div>

                    <div>
                        <flux:label for="date">Data do exame</flux:label>
                        <flux:input id="date" name="date" type="date" value="{{ old('date') }}" required />
                    </div>

                    <div>
                        <flux:label for="duration">Duração (minutos)</flux:label>
                        <flux:input id="duration" name="duration" type="number" placeholder="90" min="1"
                            value="{{ old('duration') }}" required />
                    </div>

                    <div>
                        <flux:label for="total_points">Nota total</flux:label>
                        <flux:input id="total_points" name="total_points" type="number" placeholder="100"
                            min="1" step="0.5" value="{{ old('total_points') }}" required />
                    </div>
                </div>

                <div class="mt-5">
                    <flux:label for="material">Material didático (opcional)</flux:label>
                    <flux:text class="text-xs text-zinc-400 mb-2">
                        Faz upload do material da unidade curricular (PDF, DOCX ou TXT). A IA usará este conteúdo para
                        gerar as questões.
                    </flux:text>
                    <div
                        class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg hover:border-blue-400 transition">
                        <div class="space-y-1 text-center">
                            <flux:icon name="arrow-up-tray" class="mx-auto h-10 w-10 text-zinc-400" />
                            <div class="text-sm text-zinc-500">
                                <label for="material"
                                    class="cursor-pointer text-blue-600 hover:text-blue-500 font-medium">
                                    Clica para fazer upload
                                </label>
                                <span> ou arrasta o ficheiro aqui</span>
                            </div>
                            <flux:text class="text-xs text-zinc-400">PDF, DOCX ou TXT até 10MB</flux:text>
                            <input id="material" name="material" type="file" accept=".pdf,.docx,.txt"
                                class="sr-only">
                        </div>
                    </div>
                    <div id="file-name" class="mt-2 text-xs text-zinc-500 hidden"></div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <flux:button href="{{ route('exams.index') }}" variant="ghost" wire:navigate>
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        Criar exame e adicionar sessões
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:main>

    <script>
        document.getElementById('material').addEventListener('change', function() {
            const fileNameDiv = document.getElementById('file-name');
            if (this.files.length > 0) {
                fileNameDiv.textContent = 'Ficheiro selecionado: ' + this.files[0].name;
                fileNameDiv.classList.remove('hidden');
            }
        });
    </script>
</x-layouts::app.sidebar>
