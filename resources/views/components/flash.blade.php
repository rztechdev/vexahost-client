@if(session('success'))
    <div class="mb-6 flex items-start gap-3 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/50 text-emerald-800 dark:text-emerald-300 px-4 py-3 rounded-xl">
        <span class="material-symbols-outlined text-[20px] shrink-0">check_circle</span>
        <span class="text-sm font-medium">{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="mb-6 flex items-start gap-3 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800/50 text-rose-800 dark:text-rose-300 px-4 py-3 rounded-xl">
        <span class="material-symbols-outlined text-[20px] shrink-0">error</span>
        <span class="text-sm font-medium">{{ session('error') }}</span>
    </div>
@endif

@if($errors->any())
    <div class="mb-6 flex items-start gap-3 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800/50 text-rose-800 dark:text-rose-300 px-4 py-3 rounded-xl">
        <span class="material-symbols-outlined text-[20px] shrink-0">error</span>
        <div class="text-sm">
            <p class="font-semibold mb-1">Periksa kembali isian berikut:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
