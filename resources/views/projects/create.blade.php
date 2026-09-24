<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('projects.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Buat Proyek Baru</h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                <div class="h-1.5 bg-gradient-to-r from-emerald-500 via-purple-500 to-pink-500"></div>
                <div class="p-8">
                    @if($ticket)
                        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-xl flex items-center gap-3">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <span class="font-bold">Membuat Proyek dari Tiket #{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</span>
                                <p class="text-xs text-emerald-600 mt-0.5">Sistem telah mengisi otomatis nama, deskripsi, dan client berdasarkan tiket.</p>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('projects.store') }}" method="POST" class="space-y-6">
                        @csrf

                        @if($ticket)
                            <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                        @endif

                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Nama Proyek <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name', $ticket ? 'Proyek: ' . $ticket->title : '') }}" required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500 @error('name') border-red-500 @enderror">
                            @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
                            <textarea id="description" name="description" rows="4"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500">{{ old('description', $ticket ? $ticket->description : '') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="work_status" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Status <span class="text-red-500">*</span></label>
                                <select id="work_status" name="work_status" required
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                                    @foreach(['pending'=>'Pending','active'=>'Aktif','completed'=>'Selesai','archived'=>'Arsip'] as $val => $label)
                                        <option value="{{ $val }}" {{ old('work_status', $ticket ? 'active' : 'pending') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="client_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Client <span class="text-red-500">*</span></label>
                                <select id="client_id" name="client_id" required
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="">-- Pilih Client --</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ old('client_id', $ticket ? $ticket->client_id : '') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                                    @endforeach
                                </select>
                                @error('client_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div>
                            <label for="manager_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Manager / PIC <span class="text-emerald-500 font-normal">(wajib untuk notifikasi teknisi)</span></label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Pilih teknisi penanggung jawab agar klien dan teknisi mendapat notifikasi serta tiket tampil di daftar &quot;Ditugaskan ke Saya&quot;.</p>
                            <select id="manager_id" name="manager_id"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">-- Tidak ada --</option>
                                @foreach($managers as $manager)
                                    <option value="{{ $manager->id }}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="start_date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Tanggal Mulai</label>
                                <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Tanggal Selesai</label>
                                <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <a href="{{ route('projects.index') }}" class="py-2 px-5 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition font-medium">Batal</a>
                            <button type="submit" class="py-2 px-6 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition">
                                Buat Proyek
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

