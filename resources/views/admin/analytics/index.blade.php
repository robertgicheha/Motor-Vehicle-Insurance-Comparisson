<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    {{-- ===================== HERO ===================== --}}
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Insights</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Analytics</h1>
                <p class="mt-0.5 text-xs text-slate-400">A live, data-driven view of users, underwriters, quotations and stickers</p>
            </div>
            <div class="flex items-center gap-2 text-[10px] text-slate-400">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/5 border border-white/10">
                    <span class="relative flex h-1.5 w-1.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-400"></span>
                    </span>
                    Live
                </span>
                <span id="analytics-updated">Updated {{ now()->format('H:i:s') }}</span>
            </div>
        </div>

        {{-- KPI tiles: 4 x 2 symmetrical grid --}}
        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-3">
            <a href="{{ route('admin.users.index') }}" class="kpi-card p-4 block hover:opacity-90 transition">
                <div class="accent-bar"></div>
                <p class="kpi-eyebrow">Total Users</p>
                <p class="kpi-value mt-1" id="kpi-total_users">{{ number_format($kpis['total_users']) }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block"><span id="kpi-active_users">{{ number_format($kpis['active_users']) }}</span> active &middot; <span id="kpi-online_users">{{ number_format($kpis['online_users']) }}</span> online</span>
            </a>
            <a href="{{ route('admin.reports.index') }}" class="kpi-card p-4 block hover:opacity-90 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#34d399,#6ee7b7)"></div>
                <p class="kpi-eyebrow">Quotations</p>
                <p class="kpi-value mt-1" id="kpi-total_quotations">{{ number_format($kpis['total_quotations']) }}</p>
                <span class="kpi-delta up mt-1.5 inline-block"><span id="kpi-quotations_month">{{ number_format($kpis['quotations_month']) }}</span> this month</span>
            </a>
            <a href="{{ route('admin.stickers.index') }}" class="kpi-card p-4 block hover:opacity-90 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#818cf8,#a5b4fc)"></div>
                <p class="kpi-eyebrow">Stickers</p>
                <p class="kpi-value mt-1" id="kpi-total_stickers">{{ number_format($kpis['total_stickers']) }}</p>
                <span class="kpi-delta up mt-1.5 inline-block"><span id="kpi-stickers_month">{{ number_format($kpis['stickers_month']) }}</span> this month</span>
            </a>
            <a href="{{ route('admin.companies.index') }}" class="kpi-card p-4 block hover:opacity-90 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#fb7185,#fda4af)"></div>
                <p class="kpi-eyebrow">Underwriters</p>
                <p class="kpi-value mt-1" id="kpi-total_companies">{{ number_format($kpis['total_companies']) }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block"><span id="kpi-active_companies">{{ number_format($kpis['active_companies']) }}</span> active</span>
            </a>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#f59e0b,#fcd34d)"></div>
                <p class="kpi-eyebrow">Total Premium (KES)</p>
                <p class="kpi-value mt-1 text-amber-500" id="kpi-total_premium">{{ number_format($kpis['total_premium'], 0) }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Quotes <span id="kpi-quotation_premium">{{ number_format($kpis['quotation_premium'], 0) }}</span> &middot; Stickers <span id="kpi-sticker_premium">{{ number_format($kpis['sticker_premium'], 0) }}</span></span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#0891b2,#67e8f9)"></div>
                <p class="kpi-eyebrow">Conversion Rate</p>
                <p class="kpi-value mt-1 text-cyan-500" id="kpi-conversion_rate">{{ $kpis['conversion_rate'] }}%</p>
                <span class="kpi-delta up mt-1.5 inline-block">Quotations to certificates</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#7c3aed,#a78bfa)"></div>
                <p class="kpi-eyebrow">Rate Items</p>
                <p class="kpi-value mt-1 text-violet-500" id="kpi-total_rates">{{ number_format($kpis['total_rates']) }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Pricing rules configured</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#0f7a65,#6ee7b7)"></div>
                <p class="kpi-eyebrow">Certificate Downloads</p>
                <p class="kpi-value mt-1" id="kpi-total_downloads">{{ number_format($kpis['total_downloads']) }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Total downloads</span>
            </div>
        </div>
    </div>

    {{-- ===================== CHARTS ===================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex flex-col">
            <div class="mb-3">
                <h3 class="text-xs font-semibold text-gray-800 dark:text-white">Quotations by Underwriter</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Share of quotations per active underwriter</p>
            </div>
            <div class="h-[260px]"><canvas id="analyticsCompanyChart"></canvas></div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex flex-col">
            <div class="mb-3">
                <h3 class="text-xs font-semibold text-gray-800 dark:text-white">Monthly Quotation Trend</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Quotations created over the last 12 months</p>
            </div>
            <div class="h-[260px]"><canvas id="analyticsMonthlyChart"></canvas></div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex flex-col">
            <div class="mb-3">
                <h3 class="text-xs font-semibold text-gray-800 dark:text-white">Quotations by Status</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Lifecycle distribution of quotations</p>
            </div>
            <div class="h-[260px]"><canvas id="statusChart"></canvas></div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex flex-col">
            <div class="mb-3">
                <h3 class="text-xs font-semibold text-gray-800 dark:text-white">Sticker Trend</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Certificates issued over the last 12 months</p>
            </div>
            <div class="h-[260px]"><canvas id="stickerChart"></canvas></div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex flex-col">
            <div class="mb-3">
                <h3 class="text-xs font-semibold text-gray-800 dark:text-white">Premium by Underwriter</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Written premium contribution (KES)</p>
            </div>
            <div class="h-[260px]"><canvas id="premiumChart"></canvas></div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex flex-col">
            <div class="mb-3">
                <h3 class="text-xs font-semibold text-gray-800 dark:text-white">Users by Role</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Account distribution across roles</p>
            </div>
            <div class="h-[260px]"><canvas id="usersRoleChart"></canvas></div>
        </div>
    </div>

    {{-- ===================== UNDERWRITER PERFORMANCE TABLE ===================== --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div>
                <h3 class="text-xs font-semibold text-gray-800 dark:text-white">Underwriter Performance &amp; Availability</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Pricing coverage and activity per underwriter</p>
            </div>
            <a href="{{ route('admin.reports.index') }}" class="text-[10px] font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400">Open reports</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/40">
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Underwriter</th>
                        <th class="px-5 py-2.5 text-center font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Availability</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Rates</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Quotations</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Certificates</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Premium (KES)</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px] w-40">Share</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @php $__maxPremium = max(1, (float) $underwriterRows->max('premium')); @endphp
                    @forelse($underwriterRows as $u)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-5 py-2.5 font-medium text-gray-800 dark:text-white">{{ $u['name'] }}</td>
                            <td class="px-5 py-2.5 text-center">
                                @if($u['is_active'])
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Available</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400">Unavailable</span>
                                @endif
                            </td>
                            <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">{{ $u['rates_count'] }}</td>
                            <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">{{ $u['quotations_count'] }}</td>
                            <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">{{ $u['stickers_count'] }}</td>
                            <td class="px-5 py-2.5 text-right font-medium text-gray-800 dark:text-white">{{ number_format($u['premium'], 0) }}</td>
                            <td class="px-5 py-2.5">
                                <div class="h-1.5 w-full rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                    <div class="h-full rounded-full bg-blue-500" style="width: {{ max(2, ($u['premium'] / $__maxPremium) * 100) }}%"></div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-8 text-center text-xs text-gray-400">No underwriters</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===================== FEED + TOP USERS + USER DISTRIBUTION ===================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Live activity feed --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-semibold text-gray-800 dark:text-white">Real-time Activity</h3>
                    <p class="text-[10px] text-gray-400 mt-0.5">Latest system actions, refreshing automatically</p>
                </div>
                <span class="inline-flex items-center gap-1.5 text-[10px] text-emerald-600 dark:text-emerald-400">
                    <span class="relative flex h-1.5 w-1.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                    </span>
                    Live
                </span>
            </div>
            <div id="activityFeed" class="divide-y divide-gray-100 dark:divide-gray-700 max-h-[420px] overflow-y-auto">
                @forelse($recentActivities as $log)
                    <div class="px-5 py-2.5 flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-[9px] font-bold flex-shrink-0">{{ strtoupper(substr($log['user'], 0, 2)) }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-700 dark:text-gray-200 truncate">{{ ucfirst(str_replace('_', ' ', $log['action'])) }}@if($log['model']) &middot; <span class="text-gray-400">{{ class_basename($log['model']) }}</span>@endif</p>
                            <p class="text-[10px] text-gray-400">{{ $log['user'] }}</p>
                        </div>
                        <span class="text-[10px] text-gray-400 flex-shrink-0">{{ $log['time'] }}</span>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-xs text-gray-400">No recent activity</div>
                @endforelse
            </div>
        </div>

        {{-- Top users --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-xs font-semibold text-gray-800 dark:text-white">Top Users by Quotations</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Most active accounts</p>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-[420px] overflow-y-auto">
                @php $__maxQuotes = max(1, (int) ($topUsers->max('quotations_count') ?? 1)); @endphp
                @forelse($topUsers as $user)
                    <div class="px-5 py-2.5 flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-[9px] font-bold flex-shrink-0">{{ strtoupper(substr($user->name ?? 'NA', 0, 2)) }}</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-800 dark:text-white truncate">{{ $user->name }}</p>
                            <div class="mt-1 h-1 w-full rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                <div class="h-full rounded-full bg-indigo-500" style="width: {{ max(2, ($user->quotations_count / $__maxQuotes) * 100) }}%"></div>
                            </div>
                        </div>
                        <span class="text-[10px] font-semibold text-blue-600 dark:text-blue-400 flex-shrink-0">{{ $user->quotations_count }} quotes</span>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-xs text-gray-400">No data</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ===================== USER + STATUS BREAKDOWNS ===================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-xs font-semibold text-gray-800 dark:text-white">Account Status</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Active vs disabled accounts</p>
            </div>
            <div class="p-5 space-y-3">
                @php $__totalAccounts = max(1, array_sum($userStatus)); @endphp
                @foreach($userStatus as $label => $count)
                    <div>
                        <div class="flex items-center justify-between text-[11px] mb-1">
                            <span class="text-gray-500 dark:text-gray-400">{{ ucfirst($label) }}</span>
                            <span class="font-semibold text-gray-800 dark:text-white">{{ number_format($count) }} ({{ number_format(($count / $__totalAccounts) * 100, 0) }}%)</span>
                        </div>
                        <div class="h-1.5 w-full rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                            <div class="h-full rounded-full {{ $label === 'active' ? 'bg-emerald-500' : 'bg-rose-500' }}" style="width: {{ ($count / $__totalAccounts) * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-xs font-semibold text-gray-800 dark:text-white">Quotation Status</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Lifecycle distribution</p>
            </div>
            <div class="p-5 space-y-3">
                @php $__totalQs = max(1, (int) $quotationsByStatus->sum()); @endphp
                @forelse($quotationsByStatus as $status => $count)
                    <div>
                        <div class="flex items-center justify-between text-[11px] mb-1">
                            <span class="text-gray-500 dark:text-gray-400">{{ ucfirst($status ?: 'unknown') }}</span>
                            <span class="font-semibold text-gray-800 dark:text-white">{{ number_format($count) }} ({{ number_format(($count / $__totalQs) * 100, 0) }}%)</span>
                        </div>
                        <div class="h-1.5 w-full rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                            <div class="h-full rounded-full bg-blue-500" style="width: {{ ($count / $__totalQs) * 100 }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 text-center py-6">No quotations yet</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-xs font-semibold text-gray-800 dark:text-white">Users by Branch</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Account distribution across branches</p>
            </div>
            <div class="p-5 space-y-3">
                @php $__totalBranch = max(1, (int) $usersByBranch->sum()); @endphp
                @forelse($usersByBranch->take(6) as $branch => $count)
                    <div>
                        <div class="flex items-center justify-between text-[11px] mb-1">
                            <span class="text-gray-500 dark:text-gray-400 truncate">{{ $branch }}</span>
                            <span class="font-semibold text-gray-800 dark:text-white">{{ number_format($count) }}</span>
                        </div>
                        <div class="h-1.5 w-full rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                            <div class="h-full rounded-full bg-indigo-500" style="width: {{ ($count / $__totalBranch) * 100 }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 text-center py-6">No data</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ===================== INSIGHTS ===================== --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-xs font-semibold text-gray-800 dark:text-white">Data-driven Insights</h3>
            <p class="text-[10px] text-gray-400 mt-0.5">Automated observations across the whole platform</p>
        </div>
        <ul class="px-5 py-4 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2.5 text-xs text-gray-600 dark:text-gray-300">
            @foreach($insights as $insight)
                <li class="flex items-start gap-2">
                    <svg class="w-3.5 h-3.5 mt-0.5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ $insight }}</span>
                </li>
            @endforeach
        </ul>
    </div>
</div>

@push('scripts')
<script>
const PALETTE = ['#1756c8','#7c3aed','#0f7a65','#d4860e','#c0392b','#0891b2','#db2777','#65a30d'];

function baseOptions(extra) {
    return Object.assign({
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { font: { size: 10 }, boxWidth: 10 } } },
    }, extra || {});
}

// Quotations by Underwriter
var companyLabels = {{ \Illuminate\Support\Js::from($companyBreakdown->keys()) }};
var companyData = {{ \Illuminate\Support\Js::from($companyBreakdown->values()) }};
new Chart(document.getElementById('analyticsCompanyChart'), {
    type: 'doughnut',
    data: { labels: companyLabels, datasets: [{ data: companyData, backgroundColor: PALETTE, borderWidth: 0 }] },
    options: baseOptions({
        cutout: '58%',
        onClick: function(e, item) {
            if (item.length > 0) {
                window.location.href = '{{ route("quotations.index") }}?company=' + encodeURIComponent(this.data.labels[item[0].index]);
            }
        },
    })
});

// Monthly Quotation Trend
new Chart(document.getElementById('analyticsMonthlyChart'), {
    type: 'line',
    data: {
        labels: {{ \Illuminate\Support\Js::from($monthlyQuotations->pluck('month')) }},
        datasets: [{
            label: 'Quotations',
            data: {{ \Illuminate\Support\Js::from($monthlyQuotations->pluck('total')) }},
            borderColor: '#1756c8', backgroundColor: 'rgba(23,86,200,0.08)', fill: true, tension: 0.4,
            pointBackgroundColor: '#1756c8', pointRadius: 3, borderWidth: 2,
        }]
    },
    options: baseOptions({ plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' } }, x: { grid: { display: false } } } })
});

// Quotations by Status
var statusLabels = {{ \Illuminate\Support\Js::from($quotationsByStatus->keys()) }};
var statusData = {{ \Illuminate\Support\Js::from($quotationsByStatus->values()) }};
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: { labels: statusLabels, datasets: [{ data: statusData, backgroundColor: PALETTE, borderWidth: 0 }] },
    options: baseOptions({
        cutout: '58%',
        onClick: function(e, item) {
            if (item.length > 0) {
                window.location.href = '{{ route("quotations.index") }}?status=' + encodeURIComponent(this.data.labels[item[0].index]);
            }
        },
    })
});

// Sticker Trend
new Chart(document.getElementById('stickerChart'), {
    type: 'line',
    data: {
        labels: {{ \Illuminate\Support\Js::from($stickerStats->pluck('month')) }},
        datasets: [{
            label: 'Stickers',
            data: {{ \Illuminate\Support\Js::from($stickerStats->pluck('total')) }},
            borderColor: '#0f7a65', backgroundColor: 'rgba(15,122,101,0.08)', fill: true, tension: 0.4,
            pointBackgroundColor: '#0f7a65', pointRadius: 3, borderWidth: 2,
        }]
    },
    options: baseOptions({ plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' } }, x: { grid: { display: false } } } })
});

// Premium by Underwriter
new Chart(document.getElementById('premiumChart'), {
    type: 'bar',
    data: {
        labels: {{ \Illuminate\Support\Js::from($underwriterRows->pluck('name')) }},
        datasets: [{
            label: 'Premium (KES)',
            data: {{ \Illuminate\Support\Js::from($underwriterRows->pluck('premium')) }},
            backgroundColor: '#1756c8', borderRadius: 4, maxBarThickness: 26,
        }]
    },
    options: baseOptions({
        plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => 'KES ' + Number(c.parsed.y).toLocaleString() } } },
        scales: { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { size: 9 }, callback: v => Number(v).toLocaleString() } }, x: { grid: { display: false }, ticks: { font: { size: 9 } } } }
    })
});

// Users by Role
new Chart(document.getElementById('usersRoleChart'), {
    type: 'doughnut',
    data: {
        labels: {{ \Illuminate\Support\Js::from($usersByRole->keys()) }},
        datasets: [{ data: {{ \Illuminate\Support\Js::from($usersByRole->values()) }}, backgroundColor: PALETTE, borderWidth: 0 }]
    },
    options: baseOptions({ cutout: '58%' })
});

// --- Real-time refresh (no full page reload) ---
(function () {
    var feed = document.getElementById('activityFeed');
    var updated = document.getElementById('analytics-updated');

    function escapeHtml(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function initials(name) {
        return (name || 'NA').substring(0, 2).toUpperCase();
    }

    function renderFeed(items) {
        if (!feed) return;
        if (!items || !items.length) {
            feed.innerHTML = '<div class="px-5 py-10 text-center text-xs text-gray-400">No recent activity</div>';
            return;
        }
        feed.innerHTML = items.map(function (log) {
            var action = escapeHtml(String(log.action || 'activity').replace(/_/g, ' '));
            action = action.charAt(0).toUpperCase() + action.slice(1);
            var model = log.model ? ' &middot; <span class="text-gray-400">' + escapeHtml(String(log.model).split('\\\\').pop()) + '</span>' : '';
            return '<div class="px-5 py-2.5 flex items-center gap-3">'
                + '<span class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-[9px] font-bold flex-shrink-0">' + escapeHtml(initials(log.user)) + '</span>'
                + '<div class="flex-1 min-w-0"><p class="text-xs text-gray-700 dark:text-gray-200 truncate">' + action + model + '</p>'
                + '<p class="text-[10px] text-gray-400">' + escapeHtml(log.user) + '</p></div>'
                + '<span class="text-[10px] text-gray-400 flex-shrink-0">' + escapeHtml(log.time) + '</span>'
                + '</div>';
        }).join('');
    }

    function setKpi(id, value) {
        var el = document.getElementById('kpi-' + id);
        if (el) el.textContent = value;
    }

    function refresh() {
        fetch('{{ route("admin.analytics.live") }}', { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                renderFeed(d.activities);
                if (updated) updated.textContent = 'Updated ' + d.generated_at;
                var k = d.kpis || {};
                Object.keys(k).forEach(function (key) {
                    var val = k[key];
                    if (key === 'conversion_rate') {
                        val = val + '%';
                    } else if (key.indexOf('premium') !== -1) {
                        val = Number(val).toLocaleString();
                    } else if (typeof val === 'number') {
                        val = val.toLocaleString();
                    }
                    setKpi(key, val);
                });
            })
            .catch(function () {});
    }

    var timer = setInterval(refresh, 15000);
    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            clearInterval(timer);
        } else {
            clearInterval(timer);
            refresh();
            timer = setInterval(refresh, 15000);
        }
    });
})();
</script>
@endpush
</x-app-layout>
