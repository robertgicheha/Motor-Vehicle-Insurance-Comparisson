<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    {{-- ─── HERO ──────────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">LOLC Microfinance Bank · Insurance Management</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Recent Quotations</h1>
                <p class="mt-0.5 text-xs text-slate-400">Your most recently generated quotations, newest first.</p>
            </div>
            <a href="{{ route('my.quotations') }}"
                class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium border border-white/20 text-white/80 rounded-lg hover:bg-white/10 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                View All
            </a>
        </div>
    </div>

    {{-- ─── LIST ──────────────────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Recent Quotations</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">{{ $quotations->count() }} most recent</p>
            </div>
            <a href="{{ route('quotations.create') }}"
                class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                New Quotation
            </a>
        </div>

        @forelse($quotations as $q)
            <a href="{{ route('quotations.show', $q) }}"
                class="flex items-center gap-4 px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 truncate">{{ $q->quotation_number }}</p>
                    <p class="text-[10px] text-gray-400 mt-0.5 truncate">
                        {{ $q->car_make }} {{ $q->car_model }} ({{ $q->car_year }})
                        &middot; {{ $q->company->name }}
                    </p>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-xs font-bold text-gray-800 dark:text-white">KES {{ number_format($q->total_premium, 2) }}</p>
                    <p class="text-[10px] text-gray-400">{{ $q->created_at->format('d M Y, h:i A') }}</p>
                </div>
            </a>
        @empty
            <div class="px-5 py-10 text-center text-xs text-gray-400">No quotations generated yet.</div>
        @endforelse
    </div>
</div>
</x-app-layout>
