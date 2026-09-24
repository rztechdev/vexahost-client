@php
    $editing = isset($role);
    $selected = old('permissions', $rolePermissions ?? []);
    $lockName = ($isProtected ?? false);
    $lockPermissions = ($isAdmin ?? false);
@endphp

<div>
    <label for="name" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Nama Role <span class="text-rose-500">*</span></label>
    <input type="text" id="name" name="name" value="{{ old('name', $editing ? $role->name : '') }}" required
           {{ $lockName ? 'readonly' : '' }}
           class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-sm {{ $lockName ? 'opacity-60 cursor-not-allowed' : '' }}"
           placeholder="mis. supervisor">
    @if($lockName)
        <p class="text-xs text-amber-600 dark:text-amber-400 mt-1 flex items-center gap-1">
            <span class="material-symbols-outlined text-[14px]">lock</span> Nama role inti dikunci, namun izinnya tetap bisa diatur.
        </p>
    @else
        <p class="text-xs text-zinc-400 mt-1">Gunakan huruf kecil; spasi akan diubah menjadi garis bawah.</p>
    @endif
</div>

<div>
    <div class="flex items-center justify-between mb-3">
        <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300">Hak Akses (Permission)</label>
        @unless($lockPermissions)
            <div class="flex gap-3 text-xs">
                <button type="button" onclick="document.querySelectorAll('.perm-cb').forEach(c=>c.checked=true)" class="text-emerald-600 hover:underline font-semibold">Pilih semua</button>
                <button type="button" onclick="document.querySelectorAll('.perm-cb').forEach(c=>c.checked=false)" class="text-zinc-400 hover:underline font-semibold">Kosongkan</button>
            </div>
        @endunless
    </div>

    @if($lockPermissions)
        <div class="flex items-start gap-3 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/50 text-emerald-800 dark:text-emerald-300 px-4 py-3 rounded-xl">
            <span class="material-symbols-outlined text-[20px] shrink-0">shield_person</span>
            <span class="text-sm">Role <strong>admin</strong> adalah super admin dan selalu memiliki seluruh hak akses. Izinnya tidak dapat dibatasi.</span>
        </div>
    @else
        <div class="space-y-4">
            @foreach($permissionGroups as $groupName => $permissions)
                <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden">
                    <div class="px-4 py-2.5 bg-zinc-50 dark:bg-zinc-950/40 text-[11px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ $groupName }}</div>
                    <div class="p-3 grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach($permissions as $permName => $permLabel)
                            <label class="flex items-center gap-2.5 p-2.5 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800/40 cursor-pointer transition has-[:checked]:bg-emerald-50 dark:has-[:checked]:bg-emerald-950/20">
                                <input type="checkbox" name="permissions[]" value="{{ $permName }}"
                                       {{ in_array($permName, $selected, true) ? 'checked' : '' }}
                                       class="perm-cb rounded border-zinc-300 dark:border-zinc-600 text-emerald-600 focus:ring-emerald-500">
                                <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $permLabel }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
