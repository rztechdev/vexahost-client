<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('projects.show', $task->project) }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Edit Tugas</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $task->project->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                <div class="h-1.5 bg-gradient-to-r from-amber-400 via-orange-500 to-red-500"></div>
                <div class="p-8">
                    <form action="{{ route('tasks.update', $task) }}" method="POST" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Nama Tugas <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name', $task->name) }}" required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
                            <textarea id="description" name="description" rows="3"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500">{{ old('description', $task->description) }}</textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="status" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Status <span class="text-red-500">*</span></label>
                                <select id="status" name="status" required
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                                    @foreach(['todo'=>'To Do','in_progress'=>'In Progress','review'=>'Review','done'=>'Done'] as $val => $label)
                                        <option value="{{ $val }}" {{ old('status', $task->status) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="priority" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Prioritas <span class="text-red-500">*</span></label>
                                <select id="priority" name="priority" required
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                                    @foreach(['low'=>'Rendah','medium'=>'Sedang','high'=>'Tinggi'] as $val => $label)
                                        <option value="{{ $val }}" {{ old('priority', $task->priority) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="assignee_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Assignee</label>
                                <select id="assignee_id" name="assignee_id"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="">-- Tidak ditugaskan --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('assignee_id', $task->assignee_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="due_date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Tenggat Waktu</label>
                                <input type="date" id="due_date" name="due_date" value="{{ old('due_date', $task->due_date) }}"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-2">
                            <a href="{{ route('projects.show', $task->project) }}" class="py-2 px-5 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition font-medium">Batal</a>
                            <button type="submit" class="py-2 px-6 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Documents Section for Task --}}
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-2xl shadow p-6">
                <h3 class="font-bold text-gray-800 dark:text-white text-base mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Dokumen Tugas
                </h3>
                <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="mb-4 flex items-center gap-3">
                    @csrf
                    <input type="hidden" name="documentable_type" value="task">
                    <input type="hidden" name="documentable_id" value="{{ $task->id }}">
                    <input type="file" name="file" required class="flex-1 text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-emerald-900/30 dark:file:text-emerald-300">
                    <button type="submit" class="shrink-0 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold py-2 px-5 rounded-lg transition">Upload</button>
                </form>
                @if($task->documents->isEmpty())
                    <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-2">Belum ada dokumen.</p>
                @else
                <div class="space-y-2">
                    @foreach($task->documents as $doc)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/40 rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $doc->file_name }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('documents.download', $doc) }}" class="text-sm text-emerald-600 dark:text-emerald-400 hover:underline">Download</a>
                            <form action="{{ route('documents.destroy', $doc) }}" method="POST" x-data x-ref="deleteEditDoc{{ $doc->id }}">
                                @csrf @method('DELETE')
                                <button type="button" @click="VhSwal.confirmDelete('Hapus?', $refs.deleteEditDoc{{ $doc->id }})" class="text-red-500 hover:text-red-700 text-sm">Hapus</button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

