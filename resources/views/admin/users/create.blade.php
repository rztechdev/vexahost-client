<x-app-layout>
    <div class="py-10">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <div class="flex items-center gap-3 mb-6">
                <a href="{{ route('admin.users.index') }}" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition">
                    <span class="material-symbols-outlined block">arrow_back</span>
                </a>
                <h2 class="font-bold text-2xl text-zinc-800 dark:text-zinc-100">Tambah Pengguna</h2>
            </div>

            <x-flash />

            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 shadow-sm overflow-hidden">
                <div class="h-1.5 bg-gradient-to-r from-emerald-500 to-teal-400"></div>
                <div class="p-8">
                    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
                        @csrf
                        @include('admin.users._fields', ['userRoles' => []])

                        <div class="flex justify-end gap-3 pt-2">
                            <a href="{{ route('admin.users.index') }}" class="py-2.5 px-5 rounded-xl border border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition font-medium text-sm">Batal</a>
                            <button type="submit" class="py-2.5 px-6 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-sm transition text-sm">Simpan Pengguna</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
