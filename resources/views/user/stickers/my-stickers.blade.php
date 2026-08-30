<x-app-layout>
<div class="max-w-7xl mx-auto space-y-6 pt-10 fade-in">

    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Insurance Documents</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">My Stickers</h1>
                <p class="mt-0.5 text-xs text-slate-400">All insurance stickers you have issued</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('stickers.issue') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium border border-white/20 text-white/80 rounded-lg hover:bg-white/10 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Create New Sticker
                </a>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="kpi-card p-4">
                <div class="accent-bar"></div>
                <p class="kpi-eyebrow">Total Issued</p>
                <p class="kpi-value mt-1">{{ number_format($stats['total']) }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">All time</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#fbbf24,#fcd34d)"></div>
                <p class="kpi-eyebrow">Pending</p>
                <p class="kpi-value mt-1">{{ number_format($stats['pending']) }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Awaiting underwriter</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#34d399,#6ee7b7)"></div>
                <p class="kpi-eyebrow">Completed</p>
                <p class="kpi-value mt-1">{{ number_format($stats['completed']) }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Ready to download</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#818cf8,#a5b4fc)"></div>
                <p class="kpi-eyebrow">Total Premium</p>
                <p class="kpi-value mt-1" style="font-size:20px">KES {{ number_format($stats['total_premium']) }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Across your stickers</span>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div>
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">My Stickers</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">{{ $stickers->total() }} total</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/40">
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Number</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Customer</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Vehicle</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Company</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Premium</th>
                        <th class="px-5 py-2.5 text-center font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Status</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Date</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($stickers as $sticker)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-5 py-2.5 font-mono text-[10px] font-bold text-gray-800 dark:text-white">{{ $sticker->sticker_number }}</td>
                            <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">{{ $sticker->customer_name }}</td>
                            <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">{{ $sticker->car_make }} {{ $sticker->car_model }} ({{ $sticker->car_year }})</td>
                            <td class="px-5 py-2.5 font-medium text-gray-800 dark:text-white">{{ $sticker->company->name ?? '—' }}</td>
                            <td class="px-5 py-2.5 text-right font-semibold text-gray-800 dark:text-white">KES {{ number_format($sticker->premium) }}</td>
                            <td class="px-5 py-2.5 text-center">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium
                                    {{ $sticker->status === 'completed' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">
                                    <span class="w-1 h-1 rounded-full {{ $sticker->status === 'completed' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                    {{ $sticker->status === 'completed' ? 'Completed' : 'Pending' }}
                                </span>
                            </td>
                            <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">{{ $sticker->created_at->format('d M Y, h:i A') }}</td>
                            <td class="px-5 py-2.5 text-right">
                                <a href="{{ route('stickers.show', $sticker) }}" class="text-[10px] font-medium text-blue-600 hover:text-blue-800">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-5 py-10 text-center text-xs text-gray-400">You haven't issued any stickers yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($stickers, 'hasPages') && $stickers->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">{{ $stickers->links() }}</div>
        @endif
    </div>
</div>
</x-app-layout>
