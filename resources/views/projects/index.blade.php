<x-app-layout>
    <div class="w-full space-y-6">

            <!-- Header & Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-white tracking-tight">Daftar Proyek</h1>
                    <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 mt-1">Kelola dan pantau progress pengerjaan seluruh proyek website &amp; digital Anda.</p>
                </div>
                @can('create', App\Models\Project::class)
                    <a href="{{ route('projects.create') }}"
                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-xs hover:shadow active:scale-95">
                        <span class="material-symbols-outlined text-[18px]">add_circle</span>
                        <span>Buat Proyek Baru</span>
                    </a>
                @endcan
            </div>

            @if(session('success'))
                <div class="p-4 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900/50 text-emerald-800 dark:text-emerald-300 rounded-xl flex items-start gap-3 shadow-xs">
                    <span class="material-symbols-outlined text-[20px] text-emerald-600 dark:text-emerald-400 shrink-0">check_circle</span>
                    <div>
                        <span class="font-bold text-xs">Berhasil!</span>
                        <p class="text-xs mt-0.5">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            {{-- Stats Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $statuses = [
                        'pending' => ['label'=>'Pending', 'badge'=>'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 border border-amber-200/50'],
                        'active' => ['label'=>'Aktif', 'badge'=>'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-400 border border-sky-200/50'],
                        'completed' => ['label'=>'Selesai', 'badge'=>'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200/50'],
                        'archived' => ['label'=>'Arsip', 'badge'=>'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400 border border-zinc-200/50']
                    ];
                @endphp
                @foreach($statuses as $key => $s)
                    <div class="bg-white dark:bg-zinc-900 rounded-xl p-4 border border-zinc-200/80 dark:border-zinc-800 shadow-xs flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ $s['label'] }}</span>
                            <div class="text-2xl font-black text-zinc-900 dark:text-white mt-0.5">
                                {{ $projects->where('work_status', $key)->count() }}
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $s['badge'] }}">
                            {{ $s['label'] }}
                        </span>
                    </div>
                @endforeach
            </div>

            {{-- Project Grid --}}
            @if($projects->isEmpty())
                <div class="text-center py-16 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200/80 dark:border-zinc-800 p-6">
                    <div class="inline-flex p-3 bg-zinc-100 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-500 rounded-xl mb-3">
                        <span class="material-symbols-outlined text-[32px]">folder_off</span>
                    </div>
                    <p class="text-sm font-bold text-zinc-700 dark:text-zinc-300">Belum ada proyek</p>
                    <p class="text-xs text-zinc-400 mt-1">Mulai dengan membuat proyek baru atau menghubungkan tiket layanan.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($projects as $project)
                        @php
                            $total = $project->tasks->count();
                            $done  = $project->tasks->where('status','done')->count();
                            $pct   = $project->progress_percentage;
                            $statusBadges = [
                                'pending' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 border border-amber-200/50',
                                'active' => 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-400 border border-sky-200/50',
                                'completed' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200/50',
                                'archived' => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400',
                            ];
                            $barColor = match(true) {
                                $pct >= 100 => 'bg-emerald-600',
                                $pct >= 80  => 'bg-amber-500',
                                $pct >= 40  => 'bg-sky-500',
                                default     => 'bg-zinc-400',
                            };
                        @endphp
                        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs flex flex-col p-5 space-y-4 hover:border-emerald-500/50 transition-colors">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h3 class="font-bold text-sm text-zinc-900 dark:text-white leading-snug">{{ $project->name }}</h3>
                                    <p class="text-xs text-zinc-400 line-clamp-2 mt-1">{{ $project->description ?? 'Tidak ada deskripsi.' }}</p>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider shrink-0 {{ $statusBadges[$project->work_status] ?? 'bg-zinc-100 text-zinc-600' }}">
                                    {{ $project->work_status_label }}
                                </span>
                            </div>

                            {{-- Progress Bar --}}
                            <div class="space-y-1.5">
                                <div class="flex justify-between text-[11px] text-zinc-500 dark:text-zinc-400">
                                    <span>Progress Proyek</span>
                                    <span class="font-mono font-bold">{{ $done }}/{{ $total }} tuntas ({{ $pct }}%)</span>
                                </div>
                                <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-2 overflow-hidden">
                                    <div class="{{ $barColor }} h-2 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>

                            {{-- Details --}}
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 space-y-1 pt-2 border-t border-zinc-100 dark:border-zinc-800/80">
                                <div class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[15px] text-zinc-400">person</span>
                                    <span>Klien: <b>{{ $project->client->name }}</b></span>
                                </div>
                                @if($project->ticket)
                                    <div class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[15px] text-emerald-600">confirmation_number</span>
                                        <span>Tiket: #{{ str_pad($project->ticket->id, 5, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                @endif
                                @if($project->end_date)
                                    <div class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[15px] text-zinc-400">event</span>
                                        <span>Deadline: {{ \Carbon\Carbon::parse($project->end_date)->format('d M Y') }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="pt-2 mt-auto">
                                <a href="{{ route('projects.show', $project) }}"
                                   class="w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-zinc-100 dark:bg-zinc-800 hover:bg-emerald-600 hover:text-white dark:hover:bg-emerald-600 dark:hover:text-white text-xs font-bold text-zinc-800 dark:text-zinc-200 transition-colors">
                                    <span>Buka Detail Proyek</span>
                                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-8">{{ $projects->links() }}</div>
            @endif

        </div>
</x-app-layout>


