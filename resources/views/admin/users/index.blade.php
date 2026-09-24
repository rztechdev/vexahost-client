<x-app-layout>
    <div class="w-full space-y-6">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-white tracking-tight">Manajemen Pengguna</h1>
                    <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 mt-1">Kelola akun pengguna dan tetapkan role untuk mengatur hak akses sistem.</p>
                </div>
                <a href="{{ route('admin.users.create') }}"
                   class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-2.5 px-4 rounded-xl shadow-xs transition active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">person_add</span>
                    <span>Tambah Pengguna</span>
                </a>
            </div>

            <x-flash />

            {{-- Search --}}
            <form method="GET" class="max-w-md">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 text-[20px]">search</span>
                    <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama atau email..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 dark:text-white text-xs shadow-xs focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </form>

            {{-- Table Card (Desktop: Table, Mobile: Cards) --}}
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs overflow-hidden">
                <!-- Desktop Table (md:block) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-zinc-50/70 dark:bg-zinc-950/50 text-left text-[11px] uppercase tracking-wider text-zinc-400 dark:text-zinc-500 font-mono font-semibold border-b border-zinc-200/80 dark:border-zinc-800">
                                <th class="px-6 py-3.5">Pengguna</th>
                                <th class="px-6 py-3.5">Role Akses</th>
                                <th class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 text-xs">
                            @forelse($users as $user)
                                <tr class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/30 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 flex items-center justify-center font-bold text-xs">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-zinc-900 dark:text-white">{{ $user->name }}</div>
                                                <div class="text-[11px] text-zinc-400 font-mono">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @forelse($user->roles as $role)
                                            <span class="inline-block px-2 py-0.5 mr-1 rounded text-[10px] font-bold uppercase tracking-wider
                                                {{ $role->name === 'admin' ? 'bg-purple-50 text-purple-700 dark:bg-purple-950/40 dark:text-purple-300 border border-purple-200/50' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300' }}">
                                                {{ ucfirst($role->name) }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-zinc-400 italic">Tanpa role</span>
                                        @endforelse
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('admin.users.edit', $user) }}"
                                               class="p-1.5 rounded-lg text-zinc-400 hover:text-emerald-600 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition" title="Ubah">
                                                <span class="material-symbols-outlined text-[18px] block">edit</span>
                                            </a>
                                            @if($user->id !== auth()->id())
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                      x-data x-ref="deleteUser{{ $user->id }}">
                                                    @csrf @method('DELETE')
                                                    <button type="button" @click="VhSwal.confirmDelete('Hapus pengguna {{ $user->name }}?', $refs.deleteUser{{ $user->id }})" class="p-1.5 rounded-lg text-zinc-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition" title="Hapus">
                                                        <span class="material-symbols-outlined text-[18px] block">delete</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-zinc-400 text-xs">
                                        Tidak ada pengguna yang cocok.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards (md:hidden) -->
                <div class="md:hidden divide-y divide-zinc-100 dark:divide-zinc-800/60">
                    @forelse($users as $user)
                        <div class="p-4 space-y-3">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 flex items-center justify-center font-bold text-xs">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-xs text-zinc-900 dark:text-white">{{ $user->name }}</div>
                                        <div class="text-[11px] font-mono text-zinc-400">{{ $user->email }}</div>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($user->roles as $role)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                                            {{ ucfirst($role->name) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-2 border-t border-zinc-100 dark:border-zinc-800/80 text-[11px]">
                                <a href="{{ route('admin.users.edit', $user) }}" class="px-2.5 py-1 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-bold">
                                    Edit
                                </a>
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" x-data x-ref="deleteUserMobile{{ $user->id }}">
                                        @csrf @method('DELETE')
                                        <button type="button" @click="VhSwal.confirmDelete('Hapus {{ $user->name }}?', $refs.deleteUserMobile{{ $user->id }})" class="px-2.5 py-1 rounded bg-rose-50 text-rose-600 font-bold">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-zinc-400 text-xs">
                            Tidak ada pengguna yang cocok.
                        </div>
                    @endforelse
                </div>
            </div>

            @if($users->hasPages())
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
</x-app-layout>


