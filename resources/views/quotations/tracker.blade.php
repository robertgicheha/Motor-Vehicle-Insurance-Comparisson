<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Quotations</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Quotation Tracker</h1>
                <p class="mt-0.5 text-xs text-slate-400">Track the status of all your insurance quotations</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('quotations.create') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    New Quotation
                </a>
            </div>
        </div>

        {{-- State cards --}}
        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#3b82f6,#60a5fa)"></div>
                <p class="kpi-eyebrow">Processing</p>
                <p class="kpi-value mt-1">{{ $stats['processing'] ?? 0 }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Awaiting completion</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#34d399,#6ee7b7)"></div>
                <p class="kpi-eyebrow">Completed</p>
                <p class="kpi-value mt-1">{{ $stats['completed'] ?? 0 }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Ready for closure</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#9ca3af,#d1d5db)"></div>
                <p class="kpi-eyebrow">Closed</p>
                <p class="kpi-value mt-1">{{ $stats['closed'] ?? 0 }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Finalised</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#f59e0b,#fbbf24)"></div>
                <p class="kpi-eyebrow">Insuring Sticker</p>
                <p class="kpi-value mt-1">{{ $stats['insuring_sticker'] ?? 0 }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Certificate issued</span>
            </div>
        </div>
    </div>

    {{-- Quotations with timeline --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div>
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Quotation Timeline</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">Track progress of each quotation</p>
            </div>
            <span class="text-[10px] text-gray-400">{{ $quotations->total() }} total</span>
        </div>

        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($quotations as $q)
                @php
                    $statuses = ['processing', 'completed', 'closed', 'insuring_sticker'];
                    $currentIdx = array_search($q->status, $statuses);
                    if ($currentIdx === false) $currentIdx = -1;

                    $stateColors = [
                        'processing' => ['bg' => 'bg-blue-500', 'text' => 'text-blue-700 dark:text-blue-400', 'ring' => 'ring-blue-500'],
                        'completed' => ['bg' => 'bg-emerald-500', 'text' => 'text-emerald-700 dark:text-emerald-400', 'ring' => 'ring-emerald-500'],
                        'closed' => ['bg' => 'bg-gray-400', 'text' => 'text-gray-500 dark:text-gray-400', 'ring' => 'ring-gray-400'],
                        'insuring_sticker' => ['bg' => 'bg-amber-500', 'text' => 'text-amber-700 dark:text-amber-400', 'ring' => 'ring-amber-500'],
                    ];
                    $color = $stateColors[$q->status] ?? ['bg' => 'bg-gray-400', 'text' => 'text-gray-500', 'ring' => 'ring-gray-400'];
                @endphp
                <div class="px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                    <div class="flex items-start gap-4">
                        {{-- Timeline circles --}}
                        <div class="flex flex-col items-center shrink-0 pt-1">
                            <div class="flex items-center">
                                @foreach($statuses as $i => $s)
                                    <div class="flex items-center">
                                        <div class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-bold
                                            {{ $i <= $currentIdx ? ($stateColors[$s]['bg'] . ' text-white shadow-sm') : 'bg-gray-200 dark:bg-gray-600 text-gray-400 dark:text-gray-500' }}
                                            {{ $i === $currentIdx ? 'ring-2 ring-offset-1 ' . $stateColors[$s]['ring'] : '' }}">
                                            {{ $i + 1 }}
                                        </div>
                                        @if($i < count($statuses) - 1)
                                            <div class="w-6 h-0.5 {{ $i < $currentIdx ? 'bg-blue-400' : 'bg-gray-200 dark:bg-gray-600' }}"></div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <div class="flex mt-1 gap-1">
                                @foreach($statuses as $i => $s)
                                    <span class="text-[7px] uppercase tracking-wider {{ $i <= $currentIdx ? 'text-gray-500 dark:text-gray-400 font-semibold' : 'text-gray-300 dark:text-gray-600' }}"
                                          style="width:32px;text-align:center;{{ $i > 0 ? 'margin-left:0' : '' }}">{{ str_replace('_', ' ', $s) }}</span>
                                @endforeach
                            </div>
                        </div>

                        {{-- Quotation details --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <div>
                                    <a href="{{ route('quotations.show', $q) }}" class="font-medium text-blue-600 hover:text-blue-800 text-xs">{{ $q->quotation_number }}</a>
                                    <span class="text-gray-400 text-[10px] ml-2">{{ $q->company->name }}</span>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $color['text'] }} bg-gray-100 dark:bg-gray-700">
                                    {{ ucfirst(str_replace('_', ' ', $q->status)) }}
                                </span>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-0.5">{{ $q->car_make }} {{ $q->car_model }} ({{ $q->car_year }}) · KES {{ number_format($q->total_premium, 0) }}</p>
                            <p class="text-[10px] text-gray-400">{{ $q->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-5 py-10 text-center">
                    <div class="flex flex-col items-center gap-2">
                        <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <p class="text-xs text-gray-400">No quotations yet.</p>
                        <a href="{{ route('quotations.create') }}" class="text-xs text-blue-600 hover:underline">Generate your first quotation</a>
                    </div>
                </div>
            @endforelse
        </div>

        @if($quotations->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">{{ $quotations->links() }}</div>
        @endif
    </div>

    {{-- Legacy stats summary --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Total</p>
            <p class="text-lg font-bold text-gray-800 dark:text-white mt-1">{{ $stats['total'] }}</p>
            <span class="text-[10px] text-gray-400">All quotations</span>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Active</p>
            <p class="text-lg font-bold text-gray-800 dark:text-white mt-1">{{ $stats['active'] }}</p>
            <span class="text-[10px] text-gray-400">Currently valid</span>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Pending</p>
            <p class="text-lg font-bold text-gray-800 dark:text-white mt-1">{{ $stats['pending'] }}</p>
            <span class="text-[10px] text-gray-400">Awaiting processing</span>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Expired</p>
            <p class="text-lg font-bold text-gray-800 dark:text-white mt-1">{{ $stats['expired'] }}</p>
            <span class="text-[10px] text-gray-400">No longer valid</span>
        </div>
    </div>

</div>
</x-app-layout>
