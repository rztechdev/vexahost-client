<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('projects.show', $project) }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Edit Proyek: {{ $project->name }}</h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                <div class="h-1.5 bg-gradient-to-r from-amber-400 via-orange-500 to-red-500"></div>
                <div class="p-8">
                    <form action="{{ route('projects.update', $project) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Nama Proyek <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name', $project->name) }}" required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
                            <textarea id="description" name="description" rows="4"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500">{{ old('description', $project->description) }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="work_status" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Status <span class="text-red-500">*</span></label>
                                <select id="work_status" name="work_status" required
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                                    @foreach(['pending'=>'Pending','active'=>'Aktif','completed'=>'Selesai','archived'=>'Arsip'] as $val => $label)
                                        <option value="{{ $val }}" {{ old('work_status', $project->work_status) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="client_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Client <span class="text-red-500">*</span></label>
                                <select id="client_id" name="client_id" required
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ old('client_id', $project->client_id) == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="manager_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Manager / PIC</label>
                            <select id="manager_id" name="manager_id"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">-- Tidak ada --</option>
                                @foreach($managers as $manager)
                                    <option value="{{ $manager->id }}" {{ old('manager_id', $project->manager_id) == $manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="start_date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Tanggal Mulai</label>
                                <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $project->start_date) }}"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Tanggal Selesai</label>
                                <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $project->end_date) }}"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <a href="{{ route('projects.show', $project) }}" class="py-2 px-5 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition font-medium">Batal</a>
                            <button type="submit" class="py-2 px-6 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>

                    <!-- Delete Form (Outside main form to prevent nested form issues) -->
                    <div class="mt-8 pt-6 border-t border-gray-150 dark:border-gray-700 flex justify-between items-center">
                        <div>
                            <h4 class="text-sm font-bold text-red-600 dark:text-red-400">Zona Bahaya</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Proyek dan semua tugas di dalamnya akan dihapus secara permanen.</p>
                        </div>
                        <form action="{{ route('projects.destroy', $project) }}" method="POST" x-data x-ref="deleteProjectForm">
                            @csrf
                            @method('DELETE')
                            <button type="button" @click="VhSwal.confirmDelete('Hapus proyek ini?', $refs.deleteProjectForm)" class="py-2 px-4 bg-red-50 hover:bg-red-100 text-red-600 dark:bg-red-950/20 dark:hover:bg-red-950/40 dark:text-red-400 font-semibold text-sm rounded-lg transition">
                                Hapus Proyek
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

