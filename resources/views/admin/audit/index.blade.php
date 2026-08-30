<x-app-layout>
<div class="space-y-6 pt-10 fade-in" x-data="{ open: false, selected: null }">

    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">System Logs</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Audit Trail</h1>
                <p class="mt-0.5 text-xs text-slate-400">View system audit and activity logs</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" id="refreshAudit"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg transition border border-white/20 text-white/80 hover:bg-white/10">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Refresh
                </button>
                <a href="{{ route('admin.audit.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('admin.audit.index') ? 'bg-blue-500 text-white' : 'border border-white/20 text-white/80 hover:bg-white/10' }}">
                    Audit Logs
                </a>
                <a href="{{ route('admin.audit.activity') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('admin.audit.activity') ? 'bg-blue-500 text-white' : 'border border-white/20 text-white/80 hover:bg-white/10' }}">
                    Activity Logs
                </a>
                <a href="{{ route('admin.audit.story') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('admin.audit.story') ? 'bg-blue-500 text-white' : 'border border-white/20 text-white/80 hover:bg-white/10' }}">
                    User Story
                </a>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-3">
            <a href="{{ route('admin.audit.index') }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar"></div>
                <p class="kpi-eyebrow">Total Entries</p>
                <p class="kpi-value mt-1">{{ method_exists($logs, 'total') ? $logs->total() : $logs->count() }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Audit trail entries</span>
            </a>
            @if(request()->routeIs('admin.audit.activity'))
            <a href="{{ route('admin.audit.activity', ['level' => 'info']) }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#34d399,#6ee7b7)"></div>
                <p class="kpi-eyebrow">Info</p>
                <p class="kpi-value mt-1">{{ $infoCount }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Information logs</span>
            </a>
            <a href="{{ route('admin.audit.activity', ['level' => 'warning']) }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#fbbf24,#fcd34d)"></div>
                <p class="kpi-eyebrow">Warnings</p>
                <p class="kpi-value mt-1">{{ $warningCount }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Warning entries</span>
            </a>
            <a href="{{ route('admin.audit.activity', ['level' => 'error']) }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#fb7185,#fda4af)"></div>
                <p class="kpi-eyebrow">Errors</p>
                <p class="kpi-value mt-1">{{ $errorCount }}</p>
                <span class="kpi-delta down mt-1.5 inline-block">Error entries</span>
            </a>
            @else
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#818cf8,#a5b4fc)"></div>
                <p class="kpi-eyebrow">Recent</p>
                <p class="kpi-value mt-1 text-[11px]">{{ $logs->first()?->created_at->diffForHumans() ?? 'N/A' }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Latest audit entry</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#f59e0b,#fbbf24)"></div>
                <p class="kpi-eyebrow">Models Tracked</p>
                <p class="kpi-value mt-1">{{ \App\Models\AuditLog::query()->distinct('model')->count('model') }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Entities under audit</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#22c55e,#4ade80)"></div>
                <p class="kpi-eyebrow">Logging Active</p>
                <p class="kpi-value mt-1 text-lg">ON</p>
                <span class="kpi-delta up mt-1.5 inline-block">Audit trail enabled</span>
            </div>
            @endif
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Activity Log</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">Filtered system logs</p>
            </div>
            <form method="GET" class="flex flex-wrap items-center gap-2">
                @if(request()->routeIs('admin.audit.activity'))
                    <select name="level"
                            class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">All Levels</option>
                        <option value="info" {{ request('level') === 'info' ? 'selected' : '' }}>Info</option>
                        <option value="warning" {{ request('level') === 'warning' ? 'selected' : '' }}>Warning</option>
                        <option value="error" {{ request('level') === 'error' ? 'selected' : '' }}>Error</option>
                    </select>
                @else
                    <select name="model"
                            class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">All Models</option>
                        <option value="User" {{ request('model') === 'User' ? 'selected' : '' }}>User</option>
                        <option value="Quotation" {{ request('model') === 'Quotation' ? 'selected' : '' }}>Quotation</option>
                        <option value="Company" {{ request('model') === 'Company' ? 'selected' : '' }}>Company</option>
                        <option value="Role" {{ request('model') === 'Role' ? 'selected' : '' }}>Role</option>
                    </select>
                @endif
                <button type="submit" class="px-3 py-1.5 text-xs bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Filter</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/40">
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">User</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Action</th>
                        @if(!request()->routeIs('admin.audit.activity'))
                            <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Model</th>
                            <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Model ID</th>
                        @endif
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Description</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px] hidden md:table-cell">IP Address</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 cursor-pointer" @click="selected = {{ Illuminate\Support\Js::from($log) }}; open = true">
                            <td class="px-5 py-2.5 font-medium text-gray-800 dark:text-white">{{ $log->user?->name ?? 'System' }}</td>
                            <td class="px-5 py-2.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium
                                    @if(isset($log->level) && $log->level === 'error') bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400
                                    @elseif(isset($log->level) && $log->level === 'warning') bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400
                                    @else bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300
                                    @endif">
                                    {{ $log->action }}
                                </span>
                            </td>
                            @if(!request()->routeIs('admin.audit.activity'))
                                <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">{{ $log->model ?? 'N/A' }}</td>
                                <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">{{ $log->model_id ?? 'N/A' }}</td>
                            @endif
                            <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400 max-w-xs truncate">{{ $log->description ?? $log->action }}</td>
                            <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400 hidden md:table-cell">{{ $log->ip_address ?? 'N/A' }}</td>
                            <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">{{ $log->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-5 py-10 text-center text-xs text-gray-400">No logs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($logs, 'hasPages') && $logs->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">{{ $logs->links() }}</div>
        @endif
    </div>

    {{-- ─── LOG DETAIL MODAL ───────────────────────────────────────────── --}}
    <div x-show="open" x-cloak @keydown.escape.window="open = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" @click.self="open = false">
        <div x-show="open" x-transition class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-lg max-h-[85vh] overflow-y-auto">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-xs font-bold text-gray-800 dark:text-white">Log Entry Details</h3>
                <button @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-5 space-y-3 text-xs" x-show="selected">
                <div class="flex justify-between gap-3">
                    <span class="text-gray-400 shrink-0">User</span>
                    <span class="font-semibold text-gray-800 dark:text-white text-right" x-text="selected?.user?.name ?? 'System'"></span>
                </div>
                <div class="flex justify-between gap-3">
                    <span class="text-gray-400 shrink-0">Date</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right" x-text="selected?.created_at ? new Date(selected.created_at).toLocaleDateString('en-GB') : ''"></span>
                </div>
                <div class="flex justify-between gap-3">
                    <span class="text-gray-400 shrink-0">Time</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right" x-text="selected?.created_at ? new Date(selected.created_at).toLocaleTimeString() : ''"></span>
                </div>
                <div class="flex justify-between gap-3">
                    <span class="text-gray-400 shrink-0">Activity</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right" x-text="selected?.action"></span>
                </div>
                <div class="flex justify-between gap-3" x-show="selected?.description">
                    <span class="text-gray-400 shrink-0">Description</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right" x-text="selected?.description"></span>
                </div>
                <div class="flex justify-between gap-3" x-show="selected?.model">
                    <span class="text-gray-400 shrink-0">Model</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right" x-text="(selected?.model || '') + (selected?.model_id ? ' #' + selected.model_id : '')"></span>
                </div>
                <div class="flex justify-between gap-3">
                    <span class="text-gray-400 shrink-0">IP Address</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right" x-text="selected?.ip_address ?? 'N/A'"></span>
                </div>
                <div x-show="selected?.user_agent">
                    <span class="text-gray-400 block mb-1">User Agent</span>
                    <span class="font-medium text-gray-800 dark:text-white break-all text-[11px]" x-text="selected?.user_agent"></span>
                </div>
                <div x-show="selected?.old_values" class="border-t border-gray-100 dark:border-gray-700 pt-3">
                    <span class="text-gray-400 block mb-1">Before</span>
                    <pre class="bg-gray-50 dark:bg-gray-900 rounded-lg p-2.5 text-[10px] overflow-x-auto" x-text="JSON.stringify(selected?.old_values, null, 2)"></pre>
                </div>
                <div x-show="selected?.new_values" class="pt-1">
                    <span class="text-gray-400 block mb-1">After</span>
                    <pre class="bg-gray-50 dark:bg-gray-900 rounded-lg p-2.5 text-[10px] overflow-x-auto" x-text="JSON.stringify(selected?.new_values, null, 2)"></pre>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const refreshBtn = document.getElementById('refreshAudit');
    if (!refreshBtn) return;
    refreshBtn.addEventListener('click', function () {
        refreshBtn.disabled = true;
        refreshBtn.innerHTML = '<svg class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Refreshing...';
        fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' })
            .then(() => window.location.reload())
            .catch(() => window.location.reload());
    });
});
</script>
@endpush
</x-app-layout>
