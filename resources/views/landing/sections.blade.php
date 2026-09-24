        <!-- Features Grid Section -->
        <section id="fitur" class="py-12 sm:py-20 border-t border-zinc-200 dark:border-zinc-800/80 bg-zinc-100/20 dark:bg-zinc-950/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center mb-10 sm:mb-16 space-y-2">
                    <h2 class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 tracking-widest uppercase">Kapasitas Sistem</h2>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-white tracking-tight">Keunggulan VexaHost Client</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Feature Card 1 -->
                    <div class="bg-white dark:bg-zinc-900 p-6 sm:p-8 rounded-2xl border border-zinc-200 dark:border-zinc-800 hover:border-emerald-600/30 dark:hover:border-emerald-500/40 hover:-translate-y-1 transition-all duration-300 shadow-sm hover:shadow-md">
                        <div class="w-11 h-11 sm:w-12 sm:h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl flex items-center justify-center text-emerald-600 dark:text-emerald-400 mb-5 sm:mb-6">
                            <span class="material-symbols-outlined text-[22px] sm:text-[24px]">dashboard_customize</span>
                        </div>
                        <h4 class="text-base sm:text-lg font-bold text-zinc-900 dark:text-white mb-2 sm:mb-3">Manajemen Terpusat</h4>
                        <p class="text-xs sm:text-sm text-zinc-650 dark:text-zinc-400 leading-relaxed">
                            Satu pintu untuk memantau pengerjaan proyek dan delegasi tugas. Visibilitas papan kerja terintegrasi langsung untuk seluruh tim teknis.
                        </p>
                    </div>

                    <!-- Feature Card 2 (Highlight variant) -->
                    <div class="bg-white dark:bg-zinc-900 p-6 sm:p-8 rounded-2xl border border-zinc-200 dark:border-zinc-800 hover:border-emerald-600/30 dark:hover:border-emerald-500/40 hover:-translate-y-1 transition-all duration-300 shadow-sm hover:shadow-md relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 sm:w-28 sm:h-28 bg-emerald-600/5 rounded-full blur-xl"></div>
                        <div class="w-11 h-11 sm:w-12 sm:h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl flex items-center justify-center text-emerald-600 dark:text-emerald-400 mb-5 sm:mb-6 relative z-10">
                            <span class="material-symbols-outlined text-[22px] sm:text-[24px]">monitoring</span>
                        </div>
                        <h4 class="text-base sm:text-lg font-bold text-zinc-900 dark:text-white mb-2 sm:mb-3 relative z-10">Pemantauan Otomatis</h4>
                        <p class="text-xs sm:text-sm text-zinc-650 dark:text-zinc-400 leading-relaxed relative z-10">
                            Lacak metrik KPI dan kepatuhan janji layanan (SLA Compliance) secara real-time. Sistem otomatis menghitung durasi tiket tanpa pelaporan manual.
                        </p>
                    </div>

                    <!-- Feature Card 3 -->
                    <div class="bg-white dark:bg-zinc-900 p-6 sm:p-8 rounded-2xl border border-zinc-200 dark:border-zinc-800 hover:border-emerald-600/30 dark:hover:border-emerald-500/40 hover:-translate-y-1 transition-all duration-300 shadow-sm hover:shadow-md">
                        <div class="w-11 h-11 sm:w-12 sm:h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl flex items-center justify-center text-emerald-600 dark:text-emerald-400 mb-5 sm:mb-6">
                            <span class="material-symbols-outlined text-[22px] sm:text-[24px]">rule_folder</span>
                        </div>
                        <h4 class="text-base sm:text-lg font-bold text-zinc-900 dark:text-white mb-2 sm:mb-3">Standarisasi Layanan</h4>
                        <p class="text-xs sm:text-sm text-zinc-650 dark:text-zinc-400 leading-relaxed">
                            Formulir pelaporan kendala IT terstandar untuk klien. Terhubung langsung dengan repositori file dan dokumen hasil pengerjaan.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Interactive RBAC Explorer Section -->
        <section id="rbac" class="py-12 sm:py-20 border-t border-zinc-200 dark:border-zinc-800/80">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center mb-10 sm:mb-12">
                    <h2 class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 tracking-widest uppercase mb-2">Keamanan & Kejelasan Tanggung Jawab</h2>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-white tracking-tight">Hak Akses Berbasis Peran (RBAC)</h3>
                    <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 mt-2 max-w-lg mx-auto">Sistem keamanan operasional multi-level untuk membagi tanggung jawab kerja dan menjaga integritas data.</p>
                </div>

                <!-- Interactive Tab Layout (Alpine.js) -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 sm:gap-8 items-start">
                    
                    <!-- Left Tab Buttons (4 Roles - Horizontally scrollable on mobile) -->
                    <div class="md:col-span-4 flex flex-row md:flex-col gap-2 overflow-x-auto no-scrollbar pb-3 md:pb-0 scroll-smooth -mx-4 px-4 md:mx-0 md:px-0">
                        <!-- PM Tab -->
                        <button @click="activeRole = 'pm'"
                                :class="activeRole === 'pm' ? 'bg-emerald-50/80 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 border-emerald-500 font-bold' : 'bg-transparent text-zinc-500 hover:bg-zinc-150 dark:hover:bg-zinc-900/50 border-transparent'"
                                class="flex items-center gap-2.5 px-4 py-3 rounded-xl border-l-2 text-xs sm:text-sm text-left transition-all shrink-0">
                            <span class="material-symbols-outlined text-[18px] sm:text-[20px] shrink-0">manage_accounts</span>
                            <span>Project Manager</span>
                        </button>
                        
                        <!-- Technician Tab -->
                        <button @click="activeRole = 'tech'"
                                :class="activeRole === 'tech' ? 'bg-emerald-50/80 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 border-emerald-500 font-bold' : 'bg-transparent text-zinc-500 hover:bg-zinc-150 dark:hover:bg-zinc-900/50 border-transparent'"
                                class="flex items-center gap-2.5 px-4 py-3 rounded-xl border-l-2 text-xs sm:text-sm text-left transition-all shrink-0">
                            <span class="material-symbols-outlined text-[18px] sm:text-[20px] shrink-0">terminal</span>
                            <span>Tim Teknis</span>
                        </button>

                        <!-- Client Tab -->
                        <button @click="activeRole = 'client'"
                                :class="activeRole === 'client' ? 'bg-emerald-50/80 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 border-emerald-500 font-bold' : 'bg-transparent text-zinc-500 hover:bg-zinc-150 dark:hover:bg-zinc-900/50 border-transparent'"
                                class="flex items-center gap-2.5 px-4 py-3 rounded-xl border-l-2 text-xs sm:text-sm text-left transition-all shrink-0">
                            <span class="material-symbols-outlined text-[18px] sm:text-[20px] shrink-0">support_agent</span>
                            <span>Klien / Pelapor</span>
                        </button>

                        <!-- CEO Tab -->
                        <button @click="activeRole = 'ceo'"
                                :class="activeRole === 'ceo' ? 'bg-emerald-50/80 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 border-emerald-500 font-bold' : 'bg-transparent text-zinc-500 hover:bg-zinc-150 dark:hover:bg-zinc-900/50 border-transparent'"
                                class="flex items-center gap-2.5 px-4 py-3 rounded-xl border-l-2 text-xs sm:text-sm text-left transition-all shrink-0">
                            <span class="material-symbols-outlined text-[18px] sm:text-[20px] shrink-0">corporate_fare</span>
                            <span>CEO / Direktur</span>
                        </button>
                    </div>

                    <!-- Right Display Screen -->
                    <div class="md:col-span-8 bg-white dark:bg-zinc-900 p-5 sm:p-8 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm min-h-[260px] transition-all">
                        
                        <!-- Content: PM -->
                        <div x-show="activeRole === 'pm'" class="space-y-3 sm:space-y-4" x-transition:enter="transition ease-out duration-200">
                            <div class="flex items-center gap-2.5">
                                <span class="p-2 rounded-lg bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 shrink-0">
                                    <span class="material-symbols-outlined text-[20px] sm:text-[22px] block">manage_accounts</span>
                                </span>
                                <h4 class="text-base sm:text-lg font-bold text-zinc-900 dark:text-white">Project Manager (Pusat Kontrol)</h4>
                            </div>
                            <p class="text-xs sm:text-sm text-zinc-655 dark:text-zinc-400 leading-relaxed">
                                Bertindak sebagai penanggung jawab utama operasional dan koordinasi proyek. Memiliki kapabilitas penuh untuk mendelegasikan tugas serta mengontrol tenggat waktu SLA.
                            </p>
                            <div class="pt-3.5 border-t border-zinc-100 dark:border-zinc-800/60 grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3">
                                <div class="flex items-center gap-2 text-[11px] sm:text-xs text-zinc-600 dark:text-zinc-350">
                                    <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 text-[16px] sm:text-[18px] shrink-0">check_box</span>
                                    <span class="truncate">Mendelegasikan tiket ke tim teknis</span>
                                </div>
                                <div class="flex items-center gap-2 text-[11px] sm:text-xs text-zinc-600 dark:text-zinc-350">
                                    <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 text-[16px] sm:text-[18px] shrink-0">check_box</span>
                                    <span class="truncate">Mengontrol & membuat proyek baru</span>
                                </div>
                                <div class="flex items-center gap-2 text-[11px] sm:text-xs text-zinc-600 dark:text-zinc-350">
                                    <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 text-[16px] sm:text-[18px] shrink-0">check_box</span>
                                    <span class="truncate">Melacak KPI kepatuhan SLA tim</span>
                                </div>
                                <div class="flex items-center gap-2 text-[11px] sm:text-xs text-zinc-600 dark:text-zinc-350">
                                    <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 text-[16px] sm:text-[18px] shrink-0">check_box</span>
                                    <span class="truncate">Mengelola hak akses repositori file</span>
                                </div>
                            </div>
                        </div>

                        <!-- Content: Technician -->
                        <div x-show="activeRole === 'tech'" class="space-y-3 sm:space-y-4" x-transition:enter="transition ease-out duration-200" style="display: none;">
                            <div class="flex items-center gap-2.5">
                                <span class="p-2 rounded-lg bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-450 shrink-0">
                                    <span class="material-symbols-outlined text-[20px] sm:text-[22px] block">terminal</span>
                                </span>
                                <h4 class="text-base sm:text-lg font-bold text-zinc-900 dark:text-white">Tim Teknis (Eksekutor Sistem)</h4>
                            </div>
                            <p class="text-xs sm:text-sm text-zinc-655 dark:text-zinc-400 leading-relaxed">
                                Anggota tim lapangan atau pengembang yang menerima penugasan langsung dari PM. Fokus pada penyelesaian masalah kendala sistem sesuai tenggat waktu yang ditentukan.
                            </p>
                            <div class="pt-3.5 border-t border-zinc-100 dark:border-zinc-800/60 grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3">
                                <div class="flex items-center gap-2 text-[11px] sm:text-xs text-zinc-600 dark:text-zinc-350">
                                    <span class="material-symbols-outlined text-emerald-500 text-[16px] sm:text-[18px] shrink-0">check_box</span>
                                    <span class="truncate">Menerima notifikasi tugas otomatis</span>
                                </div>
                                <div class="flex items-center gap-2 text-[11px] sm:text-xs text-zinc-600 dark:text-zinc-350">
                                    <span class="material-symbols-outlined text-emerald-500 text-[16px] sm:text-[18px] shrink-0">check_box</span>
                                    <span class="truncate">Mengubah progres status penugasan</span>
                                </div>
                                <div class="flex items-center gap-2 text-[11px] sm:text-xs text-zinc-600 dark:text-zinc-350">
                                    <span class="material-symbols-outlined text-emerald-500 text-[16px] sm:text-[18px] shrink-0">check_box</span>
                                    <span class="truncate">Mengunggah dokumen laporan kerja</span>
                                </div>
                                <div class="flex items-center gap-2 text-[11px] sm:text-xs text-zinc-600 dark:text-zinc-350">
                                    <span class="material-symbols-outlined text-emerald-500 text-[16px] sm:text-[18px] shrink-0">check_box</span>
                                    <span class="truncate">Menutup tiket yang telah diselesaikan</span>
                                </div>
                            </div>
                        </div>

                        <!-- Content: Client -->
                        <div x-show="activeRole === 'client'" class="space-y-3 sm:space-y-4" x-transition:enter="transition ease-out duration-200" style="display: none;">
                            <div class="flex items-center gap-2.5">
                                <span class="p-2 rounded-lg bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400 shrink-0">
                                    <span class="material-symbols-outlined text-[20px] sm:text-[22px] block">support_agent</span>
                                </span>
                                <h4 class="text-base sm:text-lg font-bold text-zinc-900 dark:text-white">Klien / Pelapor Kendala</h4>
                            </div>
                            <p class="text-xs sm:text-sm text-zinc-655 dark:text-zinc-400 leading-relaxed">
                                Pengguna eksternal atau perwakilan mitra yang melaporkan kendala operasional. Laporan kendala memicu inisiasi penghitung SLA secara otomatis.
                            </p>
                            <div class="pt-3.5 border-t border-zinc-100 dark:border-zinc-800/60 grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3">
                                <div class="flex items-center gap-2 text-[11px] sm:text-xs text-zinc-600 dark:text-zinc-350">
                                    <span class="material-symbols-outlined text-amber-550 text-[16px] sm:text-[18px] shrink-0">check_box</span>
                                    <span class="truncate">Membuat tiket pelaporan kendala baru</span>
                                </div>
                                <div class="flex items-center gap-2 text-[11px] sm:text-xs text-zinc-600 dark:text-zinc-350">
                                    <span class="material-symbols-outlined text-amber-550 text-[16px] sm:text-[18px] shrink-0">check_box</span>
                                    <span class="truncate">Melacak progres pengerjaan tiket</span>
                                </div>
                                <div class="flex items-center gap-2 text-[11px] sm:text-xs text-zinc-600 dark:text-zinc-350">
                                    <span class="material-symbols-outlined text-amber-550 text-[16px] sm:text-[18px] shrink-0">check_box</span>
                                    <span class="truncate">Mengunduh berkas lampiran pengerjaan</span>
                                </div>
                                <div class="flex items-center gap-2 text-[11px] sm:text-xs text-zinc-600 dark:text-zinc-350">
                                    <span class="material-symbols-outlined text-amber-550 text-[16px] sm:text-[18px] shrink-0">check_box</span>
                                    <span class="truncate">Memberikan penilaian CSAT layanan</span>
                                </div>
                            </div>
                        </div>

                        <!-- Content: CEO -->
                        <div x-show="activeRole === 'ceo'" class="space-y-3 sm:space-y-4" x-transition:enter="transition ease-out duration-200" style="display: none;">
                            <div class="flex items-center gap-2.5">
                                <span class="p-2 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-300 shrink-0">
                                    <span class="material-symbols-outlined text-[20px] sm:text-[22px] block">corporate_fare</span>
                                </span>
                                <h4 class="text-base sm:text-lg font-bold text-zinc-900 dark:text-white">CEO / Direktur (Pemantau Strategis)</h4>
                            </div>
                            <p class="text-xs sm:text-sm text-zinc-655 dark:text-zinc-400 leading-relaxed">
                                Memiliki visibilitas strategis (Read-Only) untuk memantau performa bisnis agregat, statistik kepatuhan SLA bulanan, serta kepuasan pelanggan secara menyeluruh.
                            </p>
                            <div class="pt-3.5 border-t border-zinc-100 dark:border-zinc-800/60 grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3">
                                <div class="flex items-center gap-2 text-[11px] sm:text-xs text-zinc-600 dark:text-zinc-350">
                                    <span class="material-symbols-outlined text-zinc-500 text-[16px] sm:text-[18px] shrink-0">check_box</span>
                                    <span class="truncate">Akses dasbor eksekutif agregat</span>
                                </div>
                                <div class="flex items-center gap-2 text-[11px] sm:text-xs text-zinc-600 dark:text-zinc-350">
                                    <span class="material-symbols-outlined text-zinc-500 text-[16px] sm:text-[18px] shrink-0">check_box</span>
                                    <span class="truncate">Memantau grafik kepatuhan SLA global</span>
                                </div>
                                <div class="flex items-center gap-2 text-[11px] sm:text-xs text-zinc-600 dark:text-zinc-350">
                                    <span class="material-symbols-outlined text-zinc-500 text-[16px] sm:text-[18px] shrink-0">check_box</span>
                                    <span class="truncate">Melihat evaluasi penilaian kepuasan klien</span>
                                </div>
                                <div class="flex items-center gap-2 text-[11px] sm:text-xs text-zinc-600 dark:text-zinc-350">
                                    <span class="material-symbols-outlined text-zinc-500 text-[16px] sm:text-[18px] shrink-0">check_box</span>
                                    <span class="truncate">Sistem read-only (keamanan data terjamin)</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </section>