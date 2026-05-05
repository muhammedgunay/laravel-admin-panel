<x-filament-widgets::widget>
    <div class="rounded-lg border border-amber-300 dark:border-amber-700 bg-amber-50 dark:bg-amber-900/20 px-4 py-2">
        <div class="flex items-center justify-between gap-3 flex-wrap">

            {{-- Sol: ikon + mesaj --}}
            <div class="flex items-center gap-2 min-w-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 flex-shrink-0 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <span class="text-xs font-medium text-amber-800 dark:text-amber-200 truncate">
                    <span class="font-semibold">{{ $currentUserName }}</span> olarak işlem yapıyorsunuz
                    <span class="text-amber-600 dark:text-amber-400 font-normal">&mdash; orijinal hesap: {{ $impersonatorName }}</span>
                </span>
            </div>

            {{-- Sağ: geri dön butonu --}}
            <form action="{{ route('impersonate.leave') }}" method="POST" class="flex-shrink-0">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-md
                           bg-amber-600 hover:bg-amber-700 text-white
                           dark:bg-amber-500 dark:hover:bg-amber-600 dark:text-amber-950
                           transition-colors duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                    </svg>
                    Geri Dön
                </button>
            </form>

        </div>
    </div>
</x-filament-widgets::widget>

