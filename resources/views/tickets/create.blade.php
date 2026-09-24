<x-app-layout>
    <div class="w-full space-y-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- Back Action -->
            <div>
                <a href="{{ route('tickets.index') }}" class="inline-flex items-center text-xs font-bold text-zinc-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors group">
                    <span class="material-symbols-outlined text-[18px] mr-1 group-hover:-translate-x-1 transition-transform">arrow_back</span>
                    <span>Kembali ke Daftar Tiket</span>
                </a>
            </div>

            <!-- Form Card -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs overflow-hidden">
                <div class="px-6 py-5 border-b border-zinc-100 dark:border-zinc-800">
                    <h4 class="font-extrabold text-zinc-900 dark:text-white text-base">Buat Tiket Layanan / Bantuan</h4>
                    <p class="text-xs text-zinc-400 mt-1">Isi formulir keluhan kendala agar teknisi kami dapat segera merespons.</p>
                </div>

                <form action="{{ route('tickets.store') }}" method="POST" class="p-6 space-y-5">
                    @csrf

                    <!-- Title -->
                    <div class="space-y-1.5">
                        <label for="title" class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider font-mono">
                            Judul / Keluhan Utama <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" 
                            placeholder="Contoh: Kendala SSL / Update fitur website"
                            class="w-full px-4 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white placeholder-zinc-400 text-xs focus:ring-emerald-500 focus:border-emerald-500 @error('title') border-rose-400 @enderror"
                            required>
                        @error('title')
                            <p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Priority -->
                    <div class="space-y-1.5">
                        <label for="priority" class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider font-mono">
                            Tingkat Prioritas <span class="text-rose-500">*</span>
                        </label>
                        <select name="priority" id="priority" 
                            class="w-full px-4 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white text-xs focus:ring-emerald-500 focus:border-emerald-500 @error('priority') border-rose-400 @enderror"
                            required>
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low (Biasa / Tidak mendesak)</option>
                            <option value="medium" {{ old('priority') == 'medium' || !old('priority') ? 'selected' : '' }}>Medium (Sedang / Standar)</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High (Kritis / Menghentikan sistem)</option>
                        </select>
                        @error('priority')
                            <p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="space-y-1.5">
                        <label for="description" class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider font-mono">
                            Deskripsi Lengkap Kendala <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="description" id="description" rows="5" 
                            placeholder="Jelaskan kendala Anda secara rinci..."
                            class="w-full px-4 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white placeholder-zinc-400 text-xs focus:ring-emerald-500 focus:border-emerald-500 @error('description') border-rose-400 @enderror"
                            required>{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-end gap-2">
                        <a href="{{ route('tickets.index') }}" class="px-4 py-2 rounded-xl border border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 font-bold text-xs hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition active:scale-95">
                            Kirim Tiket
                        </button>
                    </div>
                </form>
            </div>

        </div>
</x-app-layout>


