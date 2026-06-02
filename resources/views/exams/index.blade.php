<x-layouts::app.sidebar>
    <flux:main>
        <div class="mb-6 flex items-center justify-between">
            <div>
                <flux:heading size="xl">Os meus exames</flux:heading>
                <flux:text class="mt-1 text-zinc-500">Todos os exames que criaste.</flux:text>
            </div>
            <flux:button href="{{ route('exams.create') }}" variant="primary" wire:navigate>
                + Novo exame
            </flux:button>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 px-4 py-3 text-sm text-green-700 dark:text-green-300">
                {{ session('success') }}
            </div>
        @endif

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
            @php $exams = auth()->user()->exams()->latest()->get(); @endphp

            @if($exams->isEmpty())
                <div class="px-5 py-16 text-center">
                    <flux:icon name="document-text" class="w-12 h-12 text-zinc-300 mx-auto mb-4" />
                    <flux:text class="text-zinc-400">Ainda não criaste nenhum exame.</flux:text>
                    <div class="mt-4">
                        <flux:button href="{{ route('exams.create') }}" variant="primary" wire:navigate>
                            Criar primeiro exame
                        </flux:button>
                    </div>
                </div>
            @else
                <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach($exams as $exam)
                        <div class="flex items-center gap-4 px-5 py-4 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                <flux:icon name="document-text" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-zinc-900 dark:text-white truncate">{{ $exam->title }}</div>
                                <div class="text-xs text-zinc-500 mt-0.5">
                                    {{ $exam->institution }} · {{ $exam->subject }} · {{ \Carbon\Carbon::parse($exam->date)->format('d/m/Y') }} · {{ $exam->total_points }} pts · {{ $exam->duration }} min
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                                    {{ $exam->sections()->count() }} sessões
                                </span>
                                <flux:button href="{{ route('exams.show', $exam) }}" size="sm" wire:navigate>
                                    Ver
                                </flux:button>
                                <flux:button href="{{ route('exams.edit', $exam) }}" size="sm" variant="ghost" wire:navigate>
                                    Editar
                                </flux:button>
                                <form method="POST" action="{{ route('exams.destroy', $exam) }}">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button type="submit" size="sm" variant="danger"
                                        onclick="return confirm('Tens a certeza que queres eliminar este exame?')">
                                        Eliminar
                                    </flux:button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </flux:main>
</x-layouts::app.sidebar>
