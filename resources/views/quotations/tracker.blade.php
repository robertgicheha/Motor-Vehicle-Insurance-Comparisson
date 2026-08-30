<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Quotations</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Quotation Tracker</h1>
                <p class="mt-0.5 text-xs text-slate-400">Track all your generated quotations and issued insurance certificates</p>
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
        <div class="mt-6 grid grid-cols-2 md:grid-cols-3 gap-3">
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#3b82f6,#60a5fa)"></div>
                <p class="kpi-eyebrow">Total</p>
                <p class="kpi-value mt-1">{{ $stats['total'] ?? 0 }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">All quotations</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#3b82f6,#60a5fa)"></div>
                <p class="kpi-eyebrow">Generated</p>
                <p class="kpi-value mt-1">{{ $stats['generated'] ?? 0 }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Awaiting certificate</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#34d399,#6ee7b7)"></div>
                <p class="kpi-eyebrow">Completed</p>
                <p class="kpi-value mt-1">{{ $stats['completed'] ?? 0 }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Certificate issued</span>
            </div>
        </div>
    </div>

    {{-- ─── ALL QUOTATIONS TABLE ─────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div>
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">All Generated Quotations</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">{{ $quotations->total() }} total</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/40">
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">#</th>
                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin'))
                            <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">User</th>
                        @endif
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Underwriter</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Vehicle</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Premium</th>
                        <th class="px-5 py-2.5 text-center font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Status</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Date</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($quotations as $q)
                        @php
                            $statusColors = [
                                'generated' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                'completed' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-5 py-2.5 text-gray-400">{{ $loop->iteration }}</td>
                            @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin'))
                                <td class="px-5 py-2.5 font-medium text-gray-800 dark:text-white">{{ $q->customer_name }}</td>
                            @endif
                            <td class="px-5 py-2.5 font-medium text-gray-800 dark:text-white">{{ $q->company->name }}</td>
                            <td class="px-5 py-2.5">
                                <span class="font-medium text-gray-800 dark:text-white">{{ $q->car_make }} {{ $q->car_model }}</span>
                                <p class="text-[10px] text-gray-400">{{ $q->car_year }}</p>
                            </td>
                            <td class="px-5 py-2.5 text-right font-bold text-gray-800 dark:text-white font-mono">KES {{ number_format($q->total_premium, 2) }}</td>
                            <td class="px-5 py-2.5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $statusColors[$q->status] ?? 'bg-gray-50 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                    {{ ucfirst($q->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">{{ $q->created_at->format('d M Y') }}</td>
                            <td class="px-5 py-2.5 text-right">
                                <a href="{{ route('quotations.show', $q) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-[10px] font-semibold bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 rounded-lg hover:bg-blue-100 transition">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                    </div>
                                    <p class="text-xs text-gray-400">No quotations yet.</p>
                                    <a href="{{ route('quotations.create') }}" class="text-xs text-blue-600 hover:underline">Generate your first quotation</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($quotations->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">{{ $quotations->links() }}</div>
        @endif
    </div>

    {{-- ─── GROUPED BY COMPANY ───────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Quotations by Underwriter</h2>
            <p class="text-[10px] text-gray-400 mt-0.5">Number of quotations generated per insurance company</p>
        </div>

        @forelse($byCompany as $c)
            <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 last:border-b-0 flex items-center gap-4">
                <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center shrink-0">
                    <span class="text-blue-600 dark:text-blue-400 text-xs font-bold">{{ strtoupper(substr($c['company'], 0, 2)) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-800 dark:text-white">{{ $c['company'] }}</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $c['generated'] }} generated &middot; {{ $c['completed'] }} completed</p>
                </div>
                <div class="w-40 shrink-0">
                    <div class="flex items-center gap-2">
                        <div class="flex-1 h-1.5 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                            <div class="h-full rounded-full bg-blue-500" style="width:{{ $c['count'] > 0 ? min(100, round($c['generated'] / max(1, $c['count']) * 100)) : 0 }}%"></div>
                        </div>
                        <span class="text-[10px] font-semibold text-gray-500 dark:text-gray-400">{{ $c['count'] }}</span>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-xs font-bold text-gray-800 dark:text-white">KES {{ number_format($c['total_premium'], 0) }}</p>
                    <p class="text-[10px] text-gray-400">Total premium</p>
                </div>
            </div>
        @empty
            <div class="px-5 py-10 text-center">
                <p class="text-xs text-gray-400">No quotations to group.</p>
            </div>
        @endforelse
    </div>

    {{-- ─── CERTIFICATES ISSUED ──────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div>
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Issued Insurance Certificates</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">Quotations that have been completed with a certificate</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ $certified->count() }} issued
            </span>
        </div>

        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($certified as $cq)
                @php $sticker = $cq->stickers->first(); @endphp
                <a href="{{ route('quotations.show', $cq) }}" class="px-5 py-3.5 flex items-center gap-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                    <div class="w-9 h-9 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-blue-600 dark:text-blue-400">{{ $cq->quotation_number }}</p>
                        <p class="text-[10px] text-gray-400 mt-0.5 truncate">{{ $cq->car_make }} {{ $cq->car_model }} ({{ $cq->car_year }}) &middot; {{ $cq->company->name }}</p>
                    </div>
                    @if($sticker)
                        <div class="text-right shrink-0">
                            <p class="text-[10px] font-semibold text-gray-500 dark:text-gray-400">{{ $sticker->sticker_number }}</p>
                            <p class="text-[10px] text-gray-400">{{ $sticker->generated_at?->format('d M Y') ?? $sticker->created_at->format('d M Y') }}</p>
                        </div>
                    @endif
                    <div class="text-right shrink-0">
                        <p class="text-xs font-bold text-gray-800 dark:text-white">KES {{ number_format($cq->total_premium, 2) }}</p>
                    </div>
                </a>
            @empty
                <div class="px-5 py-10 text-center">
                    <div class="flex flex-col items-center gap-2">
                        <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        </div>
                        <p class="text-xs text-gray-400">No certificates issued yet.</p>
                        <p class="text-[10px] text-gray-400">Once a certificate is generated for a quotation, it will appear here as Completed.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

</div>
</x-app-layout>
