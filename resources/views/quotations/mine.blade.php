<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    {{-- ─── HERO ──────────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">MIC Global Risks · Insurance Management</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">My Quotations</h1>
                <p class="mt-0.5 text-xs text-slate-400">All the quotations you have ever generated, in one place.</p>
            </div>
            <a href="{{ route('quotations.create') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                New Quotation
            </a>
        </div>

        {{-- KPI row --}}
        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="kpi-card p-4">
                <div class="accent-bar"></div>
                <p class="kpi-eyebrow">Total Quotations</p>
                <p class="kpi-value mt-1">{{ $analytics['total'] }}</p>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#34d399,#6ee7b7)"></div>
                <p class="kpi-eyebrow">Today</p>
                <p class="kpi-value mt-1">{{ $analytics['today'] }}</p>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#818cf8,#a5b4fc)"></div>
                <p class="kpi-eyebrow">This Week</p>
                <p class="kpi-value mt-1">{{ $analytics['this_week'] }}</p>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#fb7185,#fda4af)"></div>
                <p class="kpi-eyebrow">This Month</p>
                <p class="kpi-value mt-1">{{ $analytics['this_month'] }}</p>
            </div>
        </div>
    </div>

    {{-- ─── TABLE ──────────────────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">All My Quotations</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">{{ $quotations->total() }} records</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/40">
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">#</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Underwriter</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Vehicle</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Valuation</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Premium</th>
                        <th class="px-5 py-2.5 text-center font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Status</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Date</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($quotations as $q)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-5 py-2.5">
                                <a href="{{ route('quotations.show', $q) }}" class="font-medium text-blue-600 hover:text-blue-800">{{ $q->quotation_number }}</a>
                            </td>
                            <td class="px-5 py-2.5 font-medium text-gray-800 dark:text-white">{{ $q->company->name }}</td>
                            <td class="px-5 py-2.5">
                                <span class="font-medium text-gray-800 dark:text-white">{{ $q->car_make }} {{ $q->car_model }}</span>
                                <p class="text-[10px] text-gray-400">{{ $q->car_year }}</p>
                            </td>
                            <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400 font-mono">{{ number_format($q->car_valuation, 0) }}</td>
                            <td class="px-5 py-2.5 text-right font-bold text-gray-800 dark:text-white font-mono">{{ number_format($q->total_premium, 2) }}</td>
                            <td class="px-5 py-2.5 text-center">
                                @php
                                    $statusColors = [
                                        'completed'       => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                        'generated'       => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $statusColors[$q->status] ?? 'bg-gray-50 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                    {{ ucfirst(str_replace('_', ' ', $q->status)) }}
                                </span>
                            </td>
                            <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">{{ $q->created_at->format('d M Y') }}</td>
                            <td class="px-5 py-2.5 text-right">
                                <a href="{{ route('quotations.show', $q) }}"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-[10px] font-semibold bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 rounded-lg hover:bg-blue-100 transition">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-xs text-gray-400">No quotations yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($quotations->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">{{ $quotations->links() }}</div>
        @endif
    </div>
</div>
</x-app-layout>
