<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">System</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Platform Health</h1>
                <p class="mt-0.5 text-xs text-slate-400">Monitor server, database, site and API health status.</p>
            </div>
            <div class="flex items-center gap-2">
                <label class="flex items-center gap-1.5 text-[10px] text-slate-300 cursor-pointer select-none">
                    <input type="checkbox" id="autoRefresh" class="w-3.5 h-3.5 rounded border-slate-500 text-blue-500 focus:ring-blue-500">
                    Auto-refresh (30s)
                </label>
                <button type="button" id="refreshHealth"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold border border-white/20 text-white/80 rounded-lg hover:bg-white/10 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Refresh
                </button>
                <form action="{{ route('admin.health.run') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Run Checks
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @php
            $typeLabels = ['database' => 'Database', 'site' => 'Site', 'server' => 'Server', 'email_server' => 'Email Server'];
            $typeIcons = ['database' => 'M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7C5 4 4 5 4 7z', 'site' => 'M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'server' => 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2', 'email_server' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'];
        @endphp
        @foreach(['database', 'site', 'server', 'email_server'] as $type)
            @php
                $check = $checks->firstWhere('type', $type);
                $label = $typeLabels[$type] ?? ucfirst($type);
                $icon = $typeIcons[$type] ?? 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
                $isUp = $check && $check->status === 'up';
                $isDegraded = $check && $check->status === 'degraded';
                $statusColor = $isUp ? 'text-emerald-500' : ($isDegraded ? 'text-amber-500' : 'text-rose-500');
                $bgColor = $isUp ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800' : ($isDegraded ? 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800' : 'bg-rose-50 dark:bg-rose-900/20 border-rose-200 dark:border-rose-800');
                $statusBadge = $isUp ? 'bg-emerald-100 text-emerald-700' : ($isDegraded ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700');
            @endphp
            <div class="rounded-2xl border p-5 {{ $bgColor }}">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $isUp ? 'bg-emerald-200' : ($isDegraded ? 'bg-amber-200' : 'bg-rose-200') }}">
                        <svg class="w-5 h-5 {{ $statusColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-800 dark:text-white">{{ $label }}</h3>
                        @if($check)
                            <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-semibold rounded-full {{ $statusBadge }}">{{ ucfirst($check->status) }}</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-semibold rounded-full bg-gray-100 text-gray-500">Not checked</span>
                        @endif
                    </div>
                </div>
                @if($check)
                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $check->message }}</p>
                    <p class="text-[10px] text-gray-400 mt-1">{{ $check->checked_at?->diffForHumans() }}</p>
                @else
                    <p class="text-xs text-gray-400">No health check has been run yet.</p>
                @endif
            </div>
        @endforeach
    </div>

    @if($allChecks->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Recent Checks</h2>
            <p class="text-[10px] text-gray-400 mt-0.5">Complete history of health checks.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/40">
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider">Type</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider">Message</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider">Checked At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($allChecks as $check)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-5 py-2.5 font-medium text-gray-800 dark:text-white">{{ $check->type }}</td>
                        <td class="px-5 py-2.5">
                            <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-semibold rounded-full
                                {{ $check->status === 'up' ? 'bg-emerald-100 text-emerald-700' : ($check->status === 'degraded' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                                {{ ucfirst($check->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400 max-w-xs truncate">{{ $check->message }}</td>
                        <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">{{ $check->checked_at?->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const autoRefresh = document.getElementById('autoRefresh');
    const refreshBtn = document.getElementById('refreshHealth');
    let timer = null;
    const csrfToken = document.querySelector('meta[name="csrf-token"]');

    function refreshNow() {
        refreshBtn.disabled = true;
        refreshBtn.innerHTML = '<svg class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Refreshing...';
        fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' })
            .then(() => window.location.reload())
            .catch(() => window.location.reload());
    }

    function toggleAuto(enable) {
        if (enable && !timer) {
            timer = setInterval(refreshNow, 30000);
        } else if (!enable && timer) {
            clearInterval(timer);
            timer = null;
        }
    }

    if (refreshBtn) refreshBtn.addEventListener('click', refreshNow);
    if (autoRefresh) {
        const saved = localStorage.getItem('health_auto_refresh') === '1';
        autoRefresh.checked = saved;
        toggleAuto(saved);
        autoRefresh.addEventListener('change', () => {
            localStorage.setItem('health_auto_refresh', autoRefresh.checked ? '1' : '0');
            toggleAuto(autoRefresh.checked);
        });
    }
});
</script>
@endpush
</x-app-layout>
