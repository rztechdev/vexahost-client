<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('projects.show', $task->project) }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Detail Tugas: {{ $task->name }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $task->project->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                <div class="p-8">
                    <div class="flex flex-col md:flex-row gap-8">
                        <div class="flex-1 space-y-6">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Deskripsi</h3>
                                <p class="text-gray-800 dark:text-gray-200">{{ $task->description ?: 'Tidak ada deskripsi.' }}</p>
                            </div>
                        </div>

                        <div class="w-full md:w-64 space-y-4">
                            @php
                                $statusColors = ['todo'=>'gray','in_progress'=>'blue','review'=>'yellow','done'=>'green'];
                                $sc = $statusColors[$task->status] ?? 'gray';
                                $statusLabels = ['todo'=>'To Do','in_progress'=>'In Progress','review'=>'Review','done'=>'Done'];
                            @endphp
                            <div>
                                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Status</h3>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-medium bg-{{ $sc }}-100 text-{{ $sc }}-800 dark:bg-{{ $sc }}-900 dark:text-{{ $sc }}-200">
                                    {{ $statusLabels[$task->status] }}
                                </span>
                            </div>

                            @php
                                $priorityColors = ['high'=>'red','medium'=>'yellow','low'=>'green'];
                                $pc = $priorityColors[$task->priority] ?? 'gray';
                                $priorityLabels = ['low'=>'Rendah','medium'=>'Sedang','high'=>'Tinggi'];
                            @endphp
                            <div>
                                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Prioritas</h3>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-sm font-medium bg-{{ $pc }}-50 text-{{ $pc }}-700 dark:bg-{{ $pc }}-900/30 dark:text-{{ $pc }}-400 border border-{{ $pc }}-200 dark:border-{{ $pc }}-800">
                                    <span class="w-2 h-2 rounded-full bg-{{ $pc }}-500"></span>
                                    {{ $priorityLabels[$task->priority] }}
                                </span>
                            </div>

                            <div>
                                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Assignee</h3>
                                <p class="text-gray-800 dark:text-gray-200 font-medium">
                                    {{ $task->assignee ? $task->assignee->name : 'Tidak ditugaskan' }}
                                </p>
                            </div>

                            <div>
                                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Deadline</h3>
                                <p class="text-gray-800 dark:text-gray-200 font-medium">
                                    {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M Y') : '-' }}
                                </p>
                            </div>

                            @if(auth()->user()->hasRole('admin'))
                            <div class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
                                <a href="{{ route('tasks.edit', $task) }}" class="block w-full text-center bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                                    Edit Tugas
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Documents Section --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6">
                <h3 class="font-bold text-gray-800 dark:text-white text-lg mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Dokumen Tugas
                </h3>

                <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="mb-6 p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl hover:border-emerald-400 transition-colors">
                    @csrf
                    <input type="hidden" name="documentable_type" value="task">
                    <input type="hidden" name="documentable_id" value="{{ $task->id }}">
                    <div class="flex items-center gap-4">
                        <label class="flex-1 flex items-center gap-3 cursor-pointer">
                            <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Upload Dokumen</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">PDF, DOC, XLS, PNG, JPG — Maks. 20MB</p>
                            </div>
                            <input type="file" name="file" id="file" class="hidden" required onchange="document.getElementById('file-name').textContent = this.files[0].name">
                        </label>
                        <p id="file-name" class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs"></p>
                        <button type="submit" class="shrink-0 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold py-2 px-5 rounded-lg transition">Upload</button>
                    </div>
                </form>

                @if($task->documents->isEmpty())
                    <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">Belum ada dokumen diupload.</p>
                @else
                <div class="space-y-2">
                    @foreach($task->documents as $doc)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/40 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900/40 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $doc->file_name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Diupload oleh {{ $doc->uploader->name }} • {{ $doc->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('documents.download', $doc) }}" class="text-sm text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 font-medium flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Download
                            </a>
                            @if(auth()->user()->hasRole('admin') || $doc->uploaded_by === auth()->id())
                            <form action="{{ route('documents.destroy', $doc) }}" method="POST" x-data x-ref="deleteTaskDoc{{ $doc->id }}">
                                @csrf @method('DELETE')
                                <button type="button" @click="VhSwal.confirmDelete('Hapus dokumen ini?', $refs.deleteTaskDoc{{ $doc->id }})" class="text-red-500 hover:text-red-700 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

