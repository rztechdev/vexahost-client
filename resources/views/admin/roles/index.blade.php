<x-app-layout>
    <div class="w-full space-y-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-white tracking-tight">Role &amp; Hak Akses</h1>
                    <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 mt-1">Tentukan kelompok izin akses (RBAC) untuk mengatur hak akses seluruh sistem.</p>
                </div>
                <a href="{{ route('admin.roles.create') }}"
                   class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-2.5 px-4 rounded-xl shadow-xs transition active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    <span>Tambah Role</span>
                </a>
            </div>

            <x-flash />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($roles as $role)
                    @php $isCore = in_array($role->name, $protectedRoles, true); @endphp
                    <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs p-5 flex flex-col">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $role->name === 'admin' ? 'bg-purple-100 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400' }}">
                                    <span class="material-symbols-outlined text-[22px]">{{ $role->name === 'admin' ? 'shield_person' : 'badge' }}</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-bold text-sm text-zinc-900 dark:text-white capitalize">{{ str_replace('_', ' ', $role->name) }}</h3>
                                        @if($isCore)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200/50">Inti</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-0.5 font-mono">
                                        {{ $role->name === 'admin' ? 'Seluruh izin' : $role->permissions_count . ' izin' }} • {{ $role->users_count }} pengguna
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.roles.edit', $role) }}"
                                   class="p-1.5 rounded-lg text-zinc-400 hover:text-emerald-600 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition" title="Ubah">
                                    <span class="material-symbols-outlined text-[18px] block">edit</span>
                                </a>
                                @unless($isCore)
                                    <form method="POST" action="{{ route('admin.roles.destroy', $role) }}"
                                          x-data x-ref="deleteRole{{ $role->id }}">
                                        @csrf @method('DELETE')
                                        <button type="button" @click="VhSwal.confirmDelete('Hapus role {{ $role->name }}?', $refs.deleteRole{{ $role->id }})" class="p-1.5 rounded-lg text-zinc-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition" title="Hapus">
                                            <span class="material-symbols-outlined text-[18px] block">delete</span>
                                        </button>
                                    </form>
                                @endunless
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
</x-app-layout>

