<x-app-layout>
<div class="space-y-6 pt-10 fade-in">
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Claims Management</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Claim Email Logs</h1>
                <p class="mt-0.5 text-xs text-slate-400">Every claim email sent to an underwriter, with delivery status</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.claims.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium border border-white/20 text-white/80 rounded-lg hover:bg-white/10 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Claims
                </a>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-3 gap-3">
            <a href="{{ route('admin.claims.email-logs') }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar"></div>
                <p class="kpi-eyebrow">Total</p>
                <p class="kpi-value mt-1">{{ number_format($stats['total']) }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">All claim emails</span>
            </a>
            <a href="{{ route('admin.claims.email-logs', ['status' => 'sent']) }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#34d399,#6ee7b7)"></div>
                <p class="kpi-eyebrow">Sent</p>
                <p class="kpi-value mt-1">{{ number_format($stats['sent']) }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Delivered to underwriter</span>
            </a>
            <a href="{{ route('admin.claims.email-logs', ['status' => 'failed']) }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#f87171,#fca5a5)"></div>
                <p class="kpi-eyebrow">Failed</p>
                <p class="kpi-value mt-1">{{ number_format($stats['failed']) }}</p>
                <span class="kpi-delta down mt-1.5 inline-block">Needs follow-up</span>
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Email Log</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">{{ $logs->total() }} total entries</p>
            </div>
            <form method="GET" class="flex flex-wrap items-center gap-2">
                <select name="status" class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">All Statuses</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
                <button type="submit" class="px-3 py-1.5 text-xs bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Filter</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/40">
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Claim #</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Recipient</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">CC</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Subject</th>
                        <th class="px-5 py-2.5 text-center font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Status</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Sent At</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Triggered By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-5 py-2.5 font-mono text-[10px] font-bold text-gray-800 dark:text-white">
                                @if($log->claim)
                                    <a href="{{ route('admin.claims.show', $log->claim) }}" class="text-blue-600 hover:text-blue-800">{{ $log->claim->claim_number }}</a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">{{ $log->recipient }}</td>
                            <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">{{ $log->cc ?? '—' }}</td>
                            <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400 max-w-xs truncate">{{ $log->subject }}</td>
                            <td class="px-5 py-2.5 text-center">
                                @php
                                    $color = $log->status === 'sent'
                                        ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                                        : 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400';
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $color }}" title="{{ $log->error_message }}">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">{{ $log->sent_at?->format('d M Y, H:i') ?? '-' }}</td>
                            <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">{{ $log->triggeredBy->name ?? 'System' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-10 text-center text-xs text-gray-400">No claim emails logged yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">{{ $logs->links() }}</div>
        @endif
    </div>
</div>
</x-app-layout>
