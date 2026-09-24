@php
    $editing = isset($user);
    $selectedRoles = old('roles', $userRoles ?? []);
@endphp

<div>
    <label for="name" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
    <input type="text" id="name" name="name" value="{{ old('name', $editing ? $user->name : '') }}" required
           class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-sm">
</div>

<div>
    <label for="email" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Email <span class="text-rose-500">*</span></label>
    <input type="email" id="email" name="email" value="{{ old('email', $editing ? $user->email : '') }}" required
           class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-sm">
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label for="password" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">
            Kata Sandi @unless($editing)<span class="text-rose-500">*</span>@endunless
        </label>
        <input type="password" id="password" name="password" {{ $editing ? '' : 'required' }} autocomplete="new-password"
               class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-sm">
        @if($editing)<p class="text-xs text-zinc-400 mt-1">Kosongkan bila tidak ingin mengubah.</p>@endif
    </div>
    <div>
        <label for="password_confirmation" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Konfirmasi Sandi</label>
        <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
               class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-sm">
    </div>
</div>

<div>
    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Role / Hak Akses</label>
    <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-3">Role menentukan apa yang bisa diakses pengguna. Atur detail izin tiap role di menu <span class="font-semibold">Role & Akses</span>.</p>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
        @foreach($roles as $role)
            <label class="flex items-center gap-2.5 p-3 rounded-xl border border-zinc-200 dark:border-zinc-700 hover:border-emerald-400 dark:hover:border-emerald-600 cursor-pointer transition has-[:checked]:bg-emerald-50 dark:has-[:checked]:bg-emerald-950/30 has-[:checked]:border-emerald-500">
                <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                       {{ in_array($role->name, $selectedRoles, true) ? 'checked' : '' }}
                       class="rounded border-zinc-300 dark:border-zinc-600 text-emerald-600 focus:ring-emerald-500">
                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ ucfirst($role->name) }}</span>
            </label>
        @endforeach
    </div>
</div>
