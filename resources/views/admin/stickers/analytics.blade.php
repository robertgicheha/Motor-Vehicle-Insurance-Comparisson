<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Reporting</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Sticker Analytics</h1>
                <p class="mt-0.5 text-xs text-slate-400">Company rankings, monthly trends, and sticker performance</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.stickers.tracker') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium border border-white/20 text-white/80 rounded-lg hover:bg-white/10 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Tracker
                </a>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="kpi-card p-4">
                <div class="accent-bar"></div>
                <p class="kpi-eyebrow">Total Generated</p>
                <p class="kpi-value mt-1">{{ number_format($total) }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">All stickers</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#34d399,#6ee7b7)"></div>
                <p class="kpi-eyebrow">Total Premium</p>
                <p class="kpi-value mt-1">KES {{ number_format($totalPremium) }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Combined value</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#818cf8,#a5b4fc)"></div>
                <p class="kpi-eyebrow">Total Downloads</p>
                <p class="kpi-value mt-1">{{ number_format($totalDownloads) }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">PDF downloads</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#fbbf24,#fcd34d)"></div>
                <p class="kpi-eyebrow">Top Company</p>
                <p class="kpi-value mt-1 text-sm">{{ $topCompany['name'] ?? 'N/A' }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">{{ $topCompany['count'] ?? 0 }} stickers</span>
            </div>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.stickers.analytics') }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
        <div class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[150px]">
                <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Company</label>
                <select name="company_id" class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">All Companies</option>
                    @foreach($companies as $c)
                        <option value="{{ $c->id }}" {{ $request->get('company_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[130px]">
                <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Period</label>
                <select name="period" class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">All Time</option>
                    <option value="today" {{ $request->get('period') === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ $request->get('period') === 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ $request->get('period') === 'month' ? 'selected' : '' }}>This Month</option>
                    <option value="quarter" {{ $request->get('period') === 'quarter' ? 'selected' : '' }}>This Quarter</option>
                    <option value="year" {{ $request->get('period') === 'year' ? 'selected' : '' }}>This Year</option>
                </select>
            </div>
            <div class="min-w-[130px]">
                <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">From</label>
                <input type="date" name="date_from" value="{{ $request->get('date_from') }}" class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div class="min-w-[130px]">
                <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">To</label>
                <input type="date" name="date_to" value="{{ $request->get('date_to') }}" class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <button type="submit" class="px-4 py-1.5 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Filter</button>
            <a href="{{ route('admin.stickers.analytics') }}" class="px-3 py-1.5 text-xs font-medium border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">Reset</a>
        </div>
    </form>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Company Rankings</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">Stickers generated per insurance company</p>
            </div>
            <div class="p-5">
                @forelse($byCompany as $idx => $item)
                    @php
                        $maxCount = $byCompany->max('count');
                        $barWidth = $maxCount > 0 ? ($item['count'] / $maxCount) * 100 : 0;
                        $colors = ['bg-blue-600', 'bg-indigo-600', 'bg-teal-600', 'bg-amber-600', 'bg-rose-600', 'bg-violet-600', 'bg-emerald-600', 'bg-cyan-600'];
                        $color = $colors[$idx % count($colors)];
                    @endphp
                    <div class="flex items-center gap-3 py-2.5 {{ !$loop->last ? 'border-b border-gray-50 dark:border-gray-700/50' : '' }}">
                        <span class="w-5 h-5 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-[9px] font-bold text-gray-500 dark:text-gray-400 shrink-0">{{ $idx + 1 }}</span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-semibold text-gray-800 dark:text-white truncate">{{ $item['name'] }}</span>
                                <span class="text-xs font-bold text-gray-800 dark:text-white ml-2">{{ $item['count'] }}</span>
                            </div>
                            <div class="w-full h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full {{ $color }} rounded-full transition-all duration-500" style="width:{{ $barWidth }}%"></div>
                            </div>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-[10px] text-gray-400">{{ $item['share_pct'] }}% share</span>
                                <span class="text-[10px] text-gray-400">KES {{ number_format($item['total_premium']) }} premium</span>
                                <span class="text-[10px] text-gray-400">{{ $item['active'] }} active</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 text-center py-8">No sticker data available.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Monthly Trends</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">Stickers generated per month</p>
            </div>
            <div class="p-5">
                <canvas id="monthlyTrendChart" height="220"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">By Cover Type</h2>
            </div>
            <div class="p-5">
                <canvas id="coverTypeChart" height="180"></canvas>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">By Status</h2>
            </div>
            <div class="p-5">
                <canvas id="statusChart" height="180"></canvas>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">By Creation Source</h2>
            </div>
            <div class="p-5">
                <canvas id="createdViaChart" height="180"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Email Delivery</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">Underwriter email status</p>
            </div>
            <div class="px-5 py-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-3 text-center">
                        <p class="text-lg font-bold text-emerald-700 dark:text-emerald-400">{{ number_format($emailStats['sent']) }}</p>
                        <p class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-500 uppercase">Sent</p>
                    </div>
                    <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-3 text-center">
                        <p class="text-lg font-bold text-red-700 dark:text-red-400">{{ number_format($emailStats['failed']) }}</p>
                        <p class="text-[10px] font-semibold text-red-600 dark:text-red-500 uppercase">Failed</p>
                    </div>
                    <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl p-3 text-center">
                        <p class="text-lg font-bold text-amber-700 dark:text-amber-400">{{ number_format($emailStats['pending']) }}</p>
                        <p class="text-[10px] font-semibold text-amber-600 dark:text-amber-500 uppercase">Pending</p>
                    </div>
                    <div class="bg-rose-50 dark:bg-rose-900/20 rounded-xl p-3 text-center">
                        <p class="text-lg font-bold text-rose-700 dark:text-rose-400">{{ number_format($emailStats['breached']) }}</p>
                        <p class="text-[10px] font-semibold text-rose-600 dark:text-rose-500 uppercase">SLA Breached</p>
                    </div>
                </div>
                <div class="mt-4">
                    <canvas id="emailChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Premium Distribution by Company</h2>
            </div>
            <div class="p-5">
                <canvas id="stickerPremiumChart" height="220"></canvas>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div>
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Monthly Breakdown by Company</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">Detailed view of stickers per company per month</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/40">
                        <th class="px-4 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Month</th>
                        @foreach($byCompany->take(6) as $co)
                            <th class="px-4 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">{{ $co['name'] }}</th>
                        @endforeach
                        <th class="px-4 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($byMonth as $m)
                        @php
                            $monthStickers = $stickers->filter(fn($s) => $s->created_at->format('Y-m') === $m['month']);
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-4 py-2.5 font-semibold text-gray-800 dark:text-white">{{ $m['label'] }}</td>
                            @foreach($byCompany->take(6) as $co)
                                @php
                                    $monthCoCount = $stickers->filter(fn($s) => $s->created_at->format('Y-m') === $m['month'] && $s->company->name === $co['name'])->count();
                                @endphp
                                <td class="px-4 py-2.5 text-right font-medium text-gray-700 dark:text-gray-300">{{ $monthCoCount ?: '-' }}</td>
                            @endforeach
                            <td class="px-4 py-2.5 text-right font-bold text-gray-800 dark:text-white">{{ $m['count'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ min($byCompany->count(), 6) + 2 }}" class="px-5 py-8 text-center text-xs text-gray-400">No monthly data available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
const chartColors = ['#1756c8','#7c3aed','#0f7a65','#d4860e','#c0392b','#818cf8','#34d399','#fbbf24'];

new Chart(document.getElementById('monthlyTrendChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($byMonth->pluck('label')) !!},
        datasets: [{
            label: 'Stickers',
            data: {!! json_encode($byMonth->pluck('count')) !!},
            borderColor: '#1756c8',
            backgroundColor: 'rgba(23,86,200,0.1)',
            fill: true,
            tension: 0.4,
            borderWidth: 2,
            pointRadius: 3,
            pointBackgroundColor: '#1756c8',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { font: { size: 10 } } },
            x: { grid: { display: false }, ticks: { font: { size: 10 } } }
        }
    }
});

new Chart(document.getElementById('coverTypeChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($byCoverType->pluck('type')) !!},
        datasets: [{
            data: {!! json_encode($byCoverType->pluck('count')) !!},
            backgroundColor: chartColors.slice(0, {{ $byCoverType->count() }}),
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { font: { size: 10 }, padding: 12 } } }
    }
});

new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($byStatus->pluck('status')) !!},
        datasets: [{
            data: {!! json_encode($byStatus->pluck('count')) !!},
            backgroundColor: ['#34d399','#fbbf24','#818cf8','#f87171','#6ee7b7','#a5b4fc'].slice(0, {{ $byStatus->count() }}),
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { font: { size: 10 }, padding: 12 } } }
    }
});

new Chart(document.getElementById('createdViaChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($byCreatedVia->pluck('via')) !!},
        datasets: [{
            data: {!! json_encode($byCreatedVia->pluck('count')) !!},
            backgroundColor: chartColors.slice(0, {{ $byCreatedVia->count() }}),
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { font: { size: 10 }, padding: 12 } } }
    }
});

new Chart(document.getElementById('emailChart'), {
    type: 'bar',
    data: {
        labels: ['Sent', 'Failed', 'Pending', 'SLA Breached'],
        datasets: [{
            data: [
                {{ $emailStats['sent'] }},
                {{ $emailStats['failed'] }},
                {{ $emailStats['pending'] }},
                {{ $emailStats['breached'] }}
            ],
            backgroundColor: ['#34d399','#f87171','#fbbf24','#c0392b'],
            borderWidth: 0,
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { font: { size: 9 } } },
            x: { grid: { display: false }, ticks: { font: { size: 9 } } }
        }
    }
});

new Chart(document.getElementById('stickerPremiumChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($byCompany->take(8)->pluck('name')) !!},
        datasets: [{
            label: 'Total Premium (KES)',
            data: {!! json_encode($byCompany->take(8)->pluck('total_premium')->map(fn($v) => round($v, 2))) !!},
            backgroundColor: chartColors.slice(0, {{ min($byCompany->count(), 8) }}),
            borderWidth: 0,
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: 'y',
        plugins: { legend: { display: false } },
        scales: {
            x: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { font: { size: 9 }, callback: function(v) { return 'KES ' + Number(v).toLocaleString(); } } },
            y: { grid: { display: false }, ticks: { font: { size: 10 } } }
        }
    }
});
</script>
@endpush
</x-app-layout>
