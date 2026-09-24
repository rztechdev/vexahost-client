@php
    $metodeBayar = [
        ['QRIS', 'qris.svg'],
        ['Bank BCA', 'bca.svg'],
        ['Bank Mandiri', 'mandiri.svg'],
        ['Bank BNI', 'bni.svg'],
        ['Bank BRI', 'bri.svg'],
        ['Bank BSI', 'bsi.svg'],
        ['Bank Permata', 'permata.svg'],
        ['CIMB Niaga', 'cimb.svg'],
        ['GoPay', 'gopay.svg'],
        ['OVO', 'ovo.svg'],
        ['DANA', 'dana.svg'],
        ['ShopeePay', 'shopeepay.svg'],
        ['LinkAja', 'linkaja.svg'],
    ];
@endphp

<!-- Metode pembayaran tagihan (logo berjalan) -->
<section aria-label="Metode Pembayaran" class="relative overflow-hidden border-t border-zinc-200 dark:border-zinc-800/80 bg-gradient-to-r from-orange-50 via-orange-100/60 to-orange-50 dark:from-zinc-950 dark:via-zinc-900 dark:to-zinc-950 py-8 sm:py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-5 sm:mb-6 text-center space-y-1">
        <h2 class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 tracking-widest uppercase">Pembayaran Tagihan</h2>
        <p class="text-sm sm:text-base font-bold text-zinc-900 dark:text-white">Bayar invoice via transfer bank atau scan QRIS dari aplikasi apa pun</p>
    </div>

    <div class="vh-pay-marquee relative w-full overflow-hidden">
        <div class="pointer-events-none absolute left-0 top-0 bottom-0 z-10 w-10 sm:w-20 bg-gradient-to-r from-orange-50 dark:from-zinc-950 to-transparent"></div>
        <div class="pointer-events-none absolute right-0 top-0 bottom-0 z-10 w-10 sm:w-20 bg-gradient-to-l from-orange-50 dark:from-zinc-950 to-transparent"></div>

        <div class="vh-pay-track flex w-max items-center gap-3 sm:gap-4">
            @for ($i = 0; $i < 2; $i++)
                @foreach ($metodeBayar as [$label, $file])
                    <div class="flex h-10 sm:h-11 shrink-0 items-center justify-center rounded-xl border border-white/80 dark:border-zinc-700/60 bg-white/90 dark:bg-white px-3.5 sm:px-4 shadow-xs transition-transform duration-200 hover:scale-105 select-none" title="{{ $label }}" @if($i) aria-hidden="true" @endif>
                        <img src="{{ asset('images/payments/' . $file) }}" alt="{{ $label }}" loading="lazy" class="h-4 sm:h-5 w-auto max-w-[70px] sm:max-w-[82px] object-contain">
                    </div>
                @endforeach
            @endfor
        </div>
    </div>
</section>
