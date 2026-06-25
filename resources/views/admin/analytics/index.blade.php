<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Insights</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Analytics</h1>
                <p class="mt-0.5 text-xs text-slate-400">Deep dive into platform metrics, trends and activity</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-3">
            <a href="{{ route('admin.users.index') }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar"></div>
                <p class="kpi-eyebrow">Total Users</p>
                <p class="kpi-value mt-1">{{ $totalUsers }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Registered accounts</span>
            </a>
            <a href="{{ route('admin.reports.index') }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#34d399,#6ee7b7)"></div>
                <p class="kpi-eyebrow">Total Quotations</p>
                <p class="kpi-value mt-1">{{ $totalQuotations }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Generated quotes</span>
            </a>
            <a href="{{ route('admin.companies.index') }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#818cf8,#a5b4fc)"></div>
                <p class="kpi-eyebrow">Companies</p>
                <p class="kpi-value mt-1">{{ $totalCompanies }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Active insurers</span>
            </a>
            <a href="{{ route('admin.stickers.index') }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#fb7185,#fda4af)"></div>
                <p class="kpi-eyebrow">Certificates</p>
                <p class="kpi-value mt-1">{{ $totalStickers }}</p>
                <span class="kpi-delta down mt-1.5 inline-block">Insurance stickers</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="text-xs font-semibold text-gray-800 dark:text-white mb-3">Quotations by Company</h3>
            <canvas id="companyChart" height="200"></canvas>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="text-xs font-semibold text-gray-800 dark:text-white mb-3">Monthly Quotation Trend</h3>
            <canvas id="monthlyChart" height="200"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="text-xs font-semibold text-gray-800 dark:text-white mb-3">Quotations by Status</h3>
            <canvas id="statusChart" height="200"></canvas>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="text-xs font-semibold text-gray-800 dark:text-white mb-3">Sticker Trend (12 months)</h3>
            <canvas id="stickerChart" height="200"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-xs font-semibold text-gray-800 dark:text-white">Recent Activity</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Latest system actions</p>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($recentActivities as $log)
                    <div class="px-5 py-2.5 flex items-center gap-3">
                        <span class="w-1.5 h-1.5 rounded-full {{ $log->level === 'error' ? 'bg-red-500' : ($log->level === 'warning' ? 'bg-yellow-500' : 'bg-blue-500') }}"></span>
                        <p class="text-xs text-gray-600 dark:text-gray-300 flex-1">{{ $log->description ?? $log->action }}</p>
                        <span class="text-[10px] text-gray-400">{{ $log->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-xs text-gray-400">No recent activity</div>
                @endforelse
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-xs font-semibold text-gray-800 dark:text-white">Top Users by Quotations</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Most active users</p>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($topUsers as $user)
                    <div class="px-5 py-2.5 flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-[9px] font-bold">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                        <p class="text-xs font-medium text-gray-800 dark:text-white flex-1">{{ $user->name }}</p>
                        <span class="text-[10px] font-semibold text-blue-600">{{ $user->quotations_count }} quotes</span>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-xs text-gray-400">No data</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
const colors = ['#1756c8','#7c3aed','#0f7a65','#d4860e','#c0392b'];
new Chart(document.getElementById('companyChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($companyBreakdown->keys()) !!},
        datasets: [{
            label: 'Quotations',
            data: {!! json_encode($companyBreakdown->values()) !!},
            backgroundColor: colors,
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' } }, x: { grid: { display: false } } } }
});

new Chart(document.getElementById('monthlyChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($monthlyQuotations->pluck('month')) !!},
        datasets: [{
            label: 'Quotations',
            data: {!! json_encode($monthlyQuotations->pluck('total')) !!},
            borderColor: '#1756c8',
            backgroundColor: 'rgba(23,86,200,0.08)',
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#1756c8',
            pointRadius: 3,
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' } }, x: { grid: { display: false } } } }
});

new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($quotationsByStatus->keys()) !!},
        datasets: [{
            data: {!! json_encode($quotationsByStatus->values()) !!},
            backgroundColor: ['#1756c8','#0f7a65','#d4860e','#c0392b','#7c3aed'],
            borderWidth: 0,
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 10 } } } } }
});

new Chart(document.getElementById('stickerChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($stickerStats->pluck('month')) !!},
        datasets: [{
            label: 'Stickers',
            data: {!! json_encode($stickerStats->pluck('total')) !!},
            borderColor: '#0f7a65',
            backgroundColor: 'rgba(15,122,101,0.08)',
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#0f7a65',
            pointRadius: 3,
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' } }, x: { grid: { display: false } } } }
});
</script>
@endpush
</x-app-layout>
