<x-layouts::app.sidebar :title="__('Dashboard')">
    <flux:main>
        <div class="mb-6">
            <flux:heading size="xl">Welcome, {{ auth()->user()->name }}!</flux:heading>
            <flux:text class="mt-1 text-zinc-500">Here is a summary of your activity.</flux:text>
        </div>



        {{-- Stats --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3 mb-8 w-full ">
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
                <flux:text class="text-sm text-zinc-500">Total number of exams</flux:text>
                <div class="mt-1 text-3xl font-semibold text-blue-600">{{ auth()->user()->exams()->count() }}</div>
            </div>
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
                <flux:text class="text-sm text-zinc-500">Questions generated</flux:text>
                <div class="mt-1 text-3xl font-semibold text-green-600">
                    {{ auth()->user()->exams()->with('sections.questions')->get()->sum(fn($e) => $e->sections->sum(fn($s) => $s->questions->count())) }}
                </div>
            </div>
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
                <flux:text class="text-sm text-zinc-500">Exams this month</flux:text>
                <div class="mt-1 text-3xl font-semibold text-amber-600">
                    {{ auth()->user()->exams()->whereMonth('created_at', now()->month)->count() }}
                </div>
            </div>
        </div>

        {{-- Recent exams --}}
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
            <div class="flex items-center justify-between px-5 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <flux:heading size="lg">Recent exams</flux:heading>
                <flux:button href="{{ route('exams.create') }}" variant="primary" size="sm" wire:navigate>
                    + New exam
                </flux:button>
            </div>

            @php $exams = auth()->user()->exams()->latest()->take(5)->get(); @endphp

            @if ($exams->isEmpty())
                <div class="px-5 py-10 text-center">
                    <flux:text class="text-zinc-400">You haven't created any exams yet.</flux:text>
                    <div class="mt-3">
                        <flux:button href="{{ route('exams.create') }}" variant="primary" wire:navigate>
                            Create first exam
                        </flux:button>
                    </div>
                </div>
            @else
                <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach ($exams as $exam)
                        <div
                            class="flex items-center gap-4 px-5 py-4 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition">
                            <div
                                class="flex-shrink-0 w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                <flux:icon name="document-text" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-zinc-900 dark:text-white truncate">
                                    {{ $exam->title }}</div>
                                <div class="text-xs text-zinc-500 mt-0.5">{{ $exam->subject }} ·
                                    {{ \Carbon\Carbon::parse($exam->date)->format('d/m/Y') }} ·
                                    {{ $exam->total_points }} pts</div>
                            </div>
                            <div class="flex gap-2">
                                <flux:button href="{{ route('exams.show', $exam) }}" size="sm" wire:navigate>See
                                </flux:button>
                                <flux:button href="{{ route('exams.download.exam', $exam) }}" size="sm"
                                    variant="ghost">PDF</flux:button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </flux:main>
</x-layouts::app.sidebar>
