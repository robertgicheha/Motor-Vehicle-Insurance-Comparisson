<x-app-layout>
<div class="max-w-7xl mx-auto space-y-6 pt-10 fade-in">

    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Insurance Documents</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Stickers</h1>
                <p class="mt-0.5 text-xs text-slate-400">Track all generated insurance stickers across companies</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.reports.index', ['tab' => 'stickers']) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium border border-white/20 text-white/80 rounded-lg hover:bg-white/10 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    View Report
                </a>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-3">
            <a href="{{ route('admin.stickers.index') }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar"></div>
                <p class="kpi-eyebrow">Total Generated</p>
                <p class="kpi-value mt-1">{{ number_format($stats['total']) }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">All stickers</span>
            </a>
            <a href="{{ route('admin.stickers.index') }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#34d399,#6ee7b7)"></div>
                <p class="kpi-eyebrow">Today</p>
                <p class="kpi-value mt-1">{{ number_format($stats['today']) }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Generated today</span>
            </a>
            <a href="{{ route('admin.stickers.index') }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#818cf8,#a5b4fc)"></div>
                <p class="kpi-eyebrow">This Week</p>
                <p class="kpi-value mt-1">{{ number_format($stats['this_week']) }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Weekly total</span>
            </a>
            <a href="{{ route('admin.reports.index') }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#fbbf24,#fcd34d)"></div>
                <p class="kpi-eyebrow">This Month</p>
                <p class="kpi-value mt-1">{{ number_format($stats['this_month']) }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Monthly total</span>
            </a>
            <a href="{{ route('admin.stickers.settings') }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#34d399,#6ee7b7)"></div>
                <p class="kpi-eyebrow">Emails Sent</p>
                <p class="kpi-value mt-1">{{ number_format($stats['emails_sent'] ?? 0) }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">To underwriters</span>
            </a>
            <a href="{{ route('admin.stickers.settings') }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#f87171,#fca5a5)"></div>
                <p class="kpi-eyebrow">Emails Failed</p>
                <p class="kpi-value mt-1">{{ number_format($stats['emails_failed'] ?? 0) }}</p>
                <span class="kpi-delta down mt-1.5 inline-block">Needs follow-up</span>
            </a>
            <a href="{{ route('admin.stickers.tracker') }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#fbbf24,#fcd34d)"></div>
                <p class="kpi-eyebrow">Pending Response</p>
                <p class="kpi-value mt-1">{{ number_format($stats['pending_responses'] ?? 0) }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Awaiting underwriter</span>
            </a>
        </div>
    </div>

    {{-- ─── REPORTS & DATA VISUALIZATION ─────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Stickers &mdash; Last 14 Days</h2>
                    <p class="text-[10px] text-gray-400 mt-0.5">Daily generation trend</p>
                </div>
                <div class="p-4">
                    <canvas id="stickerTrendChart" height="180"></canvas>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Share by Company</h2>
                    <p class="text-[10px] text-gray-400 mt-0.5">Stickers generated</p>
                </div>
                <div class="p-4">
                    <canvas id="stickerCompanyChart" height="180"></canvas>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Generate Report</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">Filter and export sticker data</p>
            </div>
            <form method="GET" id="stickerReportForm" class="p-5 space-y-3">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Company</label>
                    <select name="company_id" class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Companies</option>
                        @foreach(\App\Models\Company::active()->get() as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">From</label>
                        <input type="date" name="date_from" class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">To</label>
                        <input type="date" name="date_to" class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-2 pt-1">
                    <button type="submit" formaction="{{ route('admin.reports.stickers.export-csv') }}"
                            class="px-2 py-2 text-[11px] font-semibold border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition">
                        CSV
                    </button>
                    <button type="submit" formaction="{{ route('admin.reports.stickers.export-excel') }}"
                            class="px-2 py-2 text-[11px] font-semibold border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition">
                        Excel
                    </button>
                    <button type="submit" formaction="{{ route('admin.reports.stickers.export-pdf') }}"
                            class="px-2 py-2 text-[11px] font-semibold border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition">
                        PDF
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(($recentEmails ?? collect())->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Recent Email Activity</h2>
                    <p class="text-[10px] text-gray-400 mt-0.5">Last 10 emails sent to underwriters</p>
                </div>
                <a href="{{ route('admin.stickers.settings') }}" class="text-[10px] font-medium text-blue-600 hover:text-blue-800">View All Logs</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/40">
                            <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Sticker</th>
                            <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Recipient</th>
                            <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Subject</th>
                            <th class="px-5 py-2.5 text-center font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Status</th>
                            <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Sent At</th>
                            <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Sent By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($recentEmails as $email)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                <td class="px-5 py-2.5 font-mono text-[10px] font-bold text-gray-800 dark:text-white">{{ $email->sticker->sticker_number }}</td>
                                <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">{{ $email->recipient }}</td>
                                <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400 max-w-xs truncate">{{ $email->subject }}</td>
                                <td class="px-5 py-2.5 text-center">
                                    @php
                                        $color = match($email->status) {
                                            'sent', 'delivered', 'opened' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                            'failed', 'bounced' => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                            default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $color }}">
                                        {{ ucfirst($email->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">{{ $email->sent_at?->format('d M Y, H:i') ?? '-' }}</td>
                                <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">{{ $email->triggeredBy->name ?? 'System' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-6 text-center text-xs text-gray-400">No email activity yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div>
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Performance by Company</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">Generated</p>
            </div>
        </div>
        <div class="px-5 py-4">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/40">
                        <th class="px-4 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Company</th>
                        <th class="px-4 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Generated</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @php $maxCount = $byCompany->max('count'); @endphp
                    @foreach($byCompany as $item)
                        @php
                            $count = $item['count'];
                            $barWidth = $maxCount > 0 ? ($count / $maxCount) * 100 : 0;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-4 py-2.5 font-medium text-gray-800 dark:text-white">{{ $item['name'] }}</td>
                            <td class="px-4 py-2.5">
                                <div class="flex items-center justify-end gap-2">
                                    <span class="font-semibold text-gray-800 dark:text-white">{{ $count }}</span>
                                    <div class="w-20 h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-blue-600 rounded-full" style="width:{{ $barWidth }}%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div>
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">All Stickers</h2>
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
                        <th class="px-5 py-2.5 text-center font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Email Status</th>
                        <th class="px-5 py-2.5 text-center font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Via</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Date</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($stickers as $sticker)
                        @php
                            // A sticker only reads as "Pending" once an underwriter email has actually
                            // gone out — before that it's still in the documents/intake stage.
                            $hasBeenEmailed = !is_null($sticker->email_sent_at);
                            $displayStatus = match(true) {
                                $sticker->status === 'completed' => 'Completed',
                                $hasBeenEmailed => 'Pending',
                                default => 'Awaiting Documents',
                            };
                            $displayColor = match(true) {
                                $sticker->status === 'completed' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                $hasBeenEmailed => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-5 py-2.5 font-mono text-[10px] font-bold text-gray-800 dark:text-white">{{ $sticker->sticker_number }}</td>
                            <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">{{ $sticker->customer_name }}</td>
                            <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">{{ $sticker->car_make }} {{ $sticker->car_model }} ({{ $sticker->car_year }})</td>
                            <td class="px-5 py-2.5 font-medium text-gray-800 dark:text-white">{{ $sticker->company->name }}</td>
                            <td class="px-5 py-2.5 text-right font-semibold text-gray-800 dark:text-white">KES {{ number_format($sticker->premium) }}</td>
                            <td class="px-5 py-2.5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $displayColor }}">
                                    {{ $displayStatus }}
                                </span>
                            </td>
                            <td class="px-5 py-2.5 text-center">
                                @if($sticker->last_email_status)
                                    @php
                                        $emailColor = match($sticker->last_email_status) {
                                            'sent', 'delivered', 'opened' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                            'failed', 'bounced' => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                            default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $emailColor }}">
                                        {{ ucfirst($sticker->last_email_status) }}
                                    </span>
                                    @if($sticker->email_sent_at)
                                        <span class="block text-[10px] text-gray-400">{{ $sticker->email_sent_at->format('d M H:i') }}</span>
                                    @endif
                                @else
                                    <span class="text-[10px] text-gray-400">Not sent</span>
                                @endif
                            </td>
                            <td class="px-5 py-2.5 text-center">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                    {{ strtoupper($sticker->created_via) }}
                                </span>
                            </td>
                            <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">{{ $sticker->created_at->format('d M Y, h:i A') }}</td>
                            <td class="px-5 py-2.5 text-right">
                                <a href="{{ route('stickers.show', $sticker) }}" class="text-[10px] font-medium text-blue-600 hover:text-blue-800">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="px-5 py-10 text-center text-xs text-gray-400">No stickers generated yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($stickers, 'hasPages') && $stickers->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">{{ $stickers->links() }}</div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') return;

    const trend = @json($trend);
    const byCompany = @json($byCompany);

    Chart.defaults.font.family = "'Inter', -apple-system, BlinkMacSystemFont, sans-serif";
    Chart.defaults.font.size = 11;
    Chart.defaults.color = '#7a8baa';

    new Chart(document.getElementById('stickerTrendChart'), {
        type: 'line',
        data: {
            labels: trend.labels,
            datasets: [{
                label: 'Stickers',
                data: trend.counts,
                borderColor: '#1756c8',
                backgroundColor: 'rgba(23,86,200,0.1)',
                fill: true,
                tension: 0.35,
                pointRadius: 2,
            }],
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
        },
    });

    new Chart(document.getElementById('stickerCompanyChart'), {
        type: 'bar',
        data: {
            labels: byCompany.map(c => c.name),
            datasets: [{
                label: 'Stickers',
                data: byCompany.map(c => c.count),
                backgroundColor: '#1756c8',
                borderRadius: 4,
            }],
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
        },
    });
});
</script>
@endpush
</x-app-layout>
