<x-app-layout>
    <div class="space-y-6 w-full" x-data>

            <!-- Flash Alert -->
            <x-flash />

            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-white tracking-tight">Riwayat Pesan WhatsApp</h1>
                    <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 mt-1">Seluruh log percakapan, template pesan, dan riwayat interaksi WhatsApp klien.</p>
                </div>
                @if($messages->total() > 0)
                    <form method="POST" action="{{ route('admin.messages.destroy-all') }}" x-ref="deleteAllMessages">
                        @csrf
                        @method('DELETE')
                        <button type="button" @click="VhSwal.confirmDelete('Hapus seluruh {{ $messages->total() }} riwayat pesan? Data yang dihapus tidak bisa dikembalikan.', $refs.deleteAllMessages)" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-rose-200 dark:border-rose-800 text-xs font-semibold bg-white dark:bg-zinc-900 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors whitespace-nowrap">
                            <span class="material-symbols-outlined text-[18px]">delete_sweep</span>
                            <span>Hapus Semua Pesan</span>
                        </button>
                    </form>
                @endif
            </div>

            <!-- Gateway Status Notice (Direct WA Mode Active) -->
            <div class="rounded-xl border border-sky-200 dark:border-sky-900/50 bg-sky-50/50 dark:bg-sky-950/20 p-4 text-xs text-sky-900 dark:text-sky-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="flex items-start sm:items-center gap-3">
                    <span class="material-symbols-outlined text-[22px] text-sky-600 dark:text-sky-400 shrink-0">info</span>
                    <div>
                        <span class="font-bold text-sm">Mode Direct WhatsApp (0 MB RAM / Tanpa Gateway) Aktif</span>
                        <p class="text-[11px] text-sky-700/80 dark:text-sky-400/80 mt-0.5">Seluruh tombol WhatsApp di CRM ini langsung membuka chat resmi ke klien via aplikasi WhatsApp tanpa perlu langganan API berbayar atau background worker server.</p>
                    </div>
                </div>
                <button @click="$dispatch('open-quick-snippets')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-sky-300 dark:border-sky-800 text-xs font-semibold bg-white dark:bg-zinc-900 text-sky-700 dark:text-sky-300 hover:bg-sky-50 dark:hover:bg-zinc-800 transition-colors whitespace-nowrap">
                    <span class="material-symbols-outlined text-[16px]">content_paste</span>
                    <span>Buka Template Chat</span>
                </button>
            </div>

            <!-- Filter Row -->
            <form method="GET" action="{{ route('admin.messages.index') }}" class="bg-white dark:bg-zinc-900 p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 shadow-sm flex flex-col md:flex-row items-center gap-3">
                <div class="relative flex-1 w-full">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 text-[20px]">search</span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari isi pesan, nomor telepon, nama klien..." 
                           class="w-full pl-10 pr-4 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                </div>

                <div class="flex items-center gap-2 w-full md:w-auto">
                    <select name="arah" onchange="this.form.submit()" 
                            class="w-full md:w-44 px-3 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-700 dark:text-zinc-300 focus:ring-emerald-500">
                        <option value="">Semua Arah</option>
                        <option value="masuk" {{ request('arah') == 'masuk' ? 'selected' : '' }}>Pesan Masuk (Klien)</option>
                        <option value="keluar" {{ request('arah') == 'keluar' ? 'selected' : '' }}>Pesan Keluar (VexaHost)</option>
                    </select>

                    <select name="tipe" onchange="this.form.submit()" 
                            class="w-full md:w-44 px-3 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-700 dark:text-zinc-300 focus:ring-emerald-500">
                        <option value="">Semua Tipe Pesan</option>
                        <option value="invoice_dp" {{ request('tipe') == 'invoice_dp' ? 'selected' : '' }}>Invoice DP</option>
                        <option value="project_selesai" {{ request('tipe') == 'project_selesai' ? 'selected' : '' }}>Project Selesai</option>
                        <option value="reminder_maintenance" {{ request('tipe') == 'reminder_maintenance' ? 'selected' : '' }}>Reminder Maintenance</option>
                        <option value="manual" {{ request('tipe') == 'manual' ? 'selected' : '' }}>Manual Chat</option>
                        <option value="webhook_masuk" {{ request('tipe') == 'webhook_masuk' ? 'selected' : '' }}>Balasan Webhook</option>
                    </select>
                </div>
            </form>

            <!-- Messages Table Card (Desktop: Table, Mobile: Cards) -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs overflow-hidden">
                <!-- Desktop Table (md:block) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-zinc-50/70 dark:bg-zinc-950/50 border-b border-zinc-200/80 dark:border-zinc-800 text-[11px] font-bold font-mono uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                            <tr>
                                <th class="px-6 py-4">Arah</th>
                                <th class="px-6 py-4">Klien / Kontak</th>
                                <th class="px-6 py-4">Tipe Pesan</th>
                                <th class="px-6 py-4">Isi Pesan</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Waktu</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60 text-xs">
                            @forelse($messages as $msg)
                                <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/30 transition-colors">
                                    <td class="px-6 py-4">
                                        @if($msg->arah === 'keluar')
                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 dark:text-emerald-400">
                                                <span class="material-symbols-outlined text-[16px]">arrow_outward</span>
                                                <span>Keluar</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-sky-600 dark:text-sky-400">
                                                <span class="material-symbols-outlined text-[16px]">call_received</span>
                                                <span>Masuk</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($msg->lead)
                                            <a href="{{ route('admin.leads.show', $msg->lead) }}" class="font-bold text-zinc-900 dark:text-white hover:text-emerald-600">
                                                {{ $msg->lead->nama_usaha }}
                                            </a>
                                        @else
                                            <span class="text-zinc-500 italic">Klien Belum Terdaftar</span>
                                        @endif
                                        <p class="text-zinc-400 font-mono text-[11px] mt-0.5">{{ $msg->kontak_wa }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-[10px] font-mono uppercase font-semibold">
                                            {{ str_replace('_', ' ', $msg->tipe_pesan) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 max-w-md">
                                        <p class="line-clamp-2 leading-relaxed text-zinc-700 dark:text-zinc-300">
                                            {{ $msg->isi_pesan }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-mono uppercase font-bold {{ $msg->status_kirim === 'sent' || $msg->status_kirim === 'received' ? 'bg-emerald-50 text-emerald-700' : 'bg-zinc-100 text-zinc-600' }}">
                                            {{ $msg->status_kirim }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-mono text-zinc-400 text-[11px] whitespace-nowrap">
                                        {{ $msg->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <form method="POST" action="{{ route('admin.messages.destroy', $msg) }}" x-ref="deleteMsgDesktop{{ $msg->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" @click="VhSwal.confirmDelete('Hapus riwayat pesan ini?', $refs['deleteMsgDesktop{{ $msg->id }}'])" class="p-1.5 rounded-lg text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors" title="Hapus Pesan">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-zinc-400 text-xs">
                                        Belum ada riwayat pesan WhatsApp.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards (md:hidden) -->
                <div class="md:hidden divide-y divide-zinc-100 dark:divide-zinc-800/60">
                    @forelse($messages as $msg)
                        <div class="p-4 space-y-2.5">
                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold {{ $msg->arah === 'keluar' ? 'text-emerald-600' : 'text-sky-600' }}">
                                    <span class="material-symbols-outlined text-[14px]">{{ $msg->arah === 'keluar' ? 'arrow_outward' : 'call_received' }}</span>
                                    <span>{{ $msg->arah === 'keluar' ? 'Pesan Keluar' : 'Pesan Masuk' }}</span>
                                </span>
                                <span class="font-mono text-[10px] text-zinc-400">{{ $msg->created_at->format('d M H:i') }}</span>
                            </div>

                            <div>
                                @if($msg->lead)
                                    <a href="{{ route('admin.leads.show', $msg->lead) }}" class="font-bold text-xs text-zinc-900 dark:text-white">
                                        {{ $msg->lead->nama_usaha }}
                                    </a>
                                @else
                                    <span class="text-xs text-zinc-500 font-medium">Klien Belum Terdaftar</span>
                                @endif
                                <p class="text-[11px] font-mono text-zinc-400">{{ $msg->kontak_wa }}</p>
                            </div>

                            <p class="text-xs text-zinc-700 dark:text-zinc-300 bg-zinc-50 dark:bg-zinc-950 p-2.5 rounded-lg border border-zinc-100 dark:border-zinc-800 leading-relaxed">
                                {{ $msg->isi_pesan }}
                            </p>

                            <div class="flex items-center justify-between text-[10px]">
                                <span class="px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 font-mono uppercase">
                                    {{ str_replace('_', ' ', $msg->tipe_pesan) }}
                                </span>
                                <div class="flex items-center gap-2">
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $msg->kontak_wa) }}" target="_blank" class="inline-flex items-center gap-1 font-bold text-emerald-600">
                                        <span class="material-symbols-outlined text-[14px]">chat</span>
                                        <span>Buka WA</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.messages.destroy', $msg) }}" x-ref="deleteMsgMobile{{ $msg->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" @click="VhSwal.confirmDelete('Hapus riwayat pesan ini?', $refs['deleteMsgMobile{{ $msg->id }}'])" class="p-1 rounded-lg text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400" title="Hapus Pesan">
                                            <span class="material-symbols-outlined text-[16px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-zinc-400 text-xs">
                            Belum ada riwayat pesan WhatsApp.
                        </div>
                    @endforelse
                </div>

                @if($messages->hasPages())
                    <div class="p-4 border-t border-zinc-200/80 dark:border-zinc-800">
                        {{ $messages->links() }}
                    </div>
                @endif
            </div>

        </div>
</x-app-layout>
