<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    {{-- ─── HERO ─────────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Communications</p>
        <h1 class="mt-0.5 text-xl font-bold text-white">Email Analytics</h1>
        <p class="mt-0.5 text-xs text-slate-400">Delivery volume and success rate across claim and sticker notifications sent to underwriters.</p>
    </div>

    {{-- ─── STAT STRIP ───────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Total Sent</p>
            <p class="mt-1 text-2xl font-extrabold text-gray-800 dark:text-white">{{ number_format($totalSent) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Successful</p>
            <p class="mt-1 text-2xl font-extrabold text-emerald-500">{{ number_format($totalSuccessful) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Failed</p>
            <p class="mt-1 text-2xl font-extrabold text-red-500">{{ number_format($totalFailed) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Success Rate</p>
            <p class="mt-1 text-2xl font-extrabold text-blue-600 dark:text-blue-400">{{ $successRate }}%</p>
        </div>
    </div>

    {{-- ─── TIME BUCKETS ────────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Volume</p>
            <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Emails Sent Over Time</h2>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 p-6">
            <div class="text-center">
                <p class="text-lg font-extrabold text-gray-800 dark:text-white">{{ number_format($buckets['today']) }}</p>
                <p class="text-[10px] text-gray-400 uppercase tracking-wide mt-0.5">Today</p>
            </div>
            <div class="text-center">
                <p class="text-lg font-extrabold text-gray-800 dark:text-white">{{ number_format($buckets['this_week']) }}</p>
                <p class="text-[10px] text-gray-400 uppercase tracking-wide mt-0.5">This Week</p>
            </div>
            <div class="text-center">
                <p class="text-lg font-extrabold text-gray-800 dark:text-white">{{ number_format($buckets['this_month']) }}</p>
                <p class="text-[10px] text-gray-400 uppercase tracking-wide mt-0.5">This Month</p>
            </div>
            <div class="text-center">
                <p class="text-lg font-extrabold text-gray-800 dark:text-white">{{ number_format($buckets['last_6_months']) }}</p>
                <p class="text-[10px] text-gray-400 uppercase tracking-wide mt-0.5">Last 6 Months</p>
            </div>
            <div class="text-center">
                <p class="text-lg font-extrabold text-gray-800 dark:text-white">{{ number_format($buckets['this_year']) }}</p>
                <p class="text-[10px] text-gray-400 uppercase tracking-wide mt-0.5">This Year</p>
            </div>
        </div>
    </div>

    {{-- ─── BY UNDERWRITER ──────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Underwriters</p>
            <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">
                Emails By Underwriter
                @if($topClaimsUnderwriter && $topClaimsUnderwriter['claims_sent'] > 0)
                    <span class="text-gray-400 font-normal">— <strong class="text-gray-800 dark:text-white">{{ $topClaimsUnderwriter['name'] }}</strong> is getting the most claims</span>
                @endif
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="text-gray-400 uppercase tracking-wider text-[10px] border-b border-gray-100 dark:border-gray-700">
                        <th class="px-4 py-3 text-left">Underwriter</th>
                        <th class="px-4 py-3 text-right">Claim Emails</th>
                        <th class="px-4 py-3 text-right">Claim Success</th>
                        <th class="px-4 py-3 text-right">Sticker Emails</th>
                        <th class="px-4 py-3 text-right">Sticker Success</th>
                        <th class="px-4 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($underwriterRows as $row)
                    <tr class="hover:bg-blue-50/50 dark:hover:bg-blue-900/10">
                        <td class="px-4 py-3 font-semibold text-gray-800 dark:text-white">{{ $row['name'] }}</td>
                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ $row['claims_sent'] }}</td>
                        <td class="px-4 py-3 text-right text-gray-400">{{ $row['claims_successful'] }}</td>
                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ $row['stickers_sent'] }}</td>
                        <td class="px-4 py-3 text-right text-gray-400">{{ $row['stickers_successful'] }}</td>
                        <td class="px-4 py-3 text-right font-extrabold text-blue-600 dark:text-blue-400">{{ $row['total_sent'] }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400">No emails logged yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ─── RECENT ACTIVITY ─────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Activity</p>
                <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Recent Emails</h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.claims.email-logs') }}" class="text-[11px] font-semibold text-blue-600 hover:underline">Claim Logs →</a>
                <a href="{{ route('admin.stickers.analytics') }}" class="text-[11px] font-semibold text-blue-600 hover:underline">Sticker Analytics →</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="text-gray-400 uppercase tracking-wider text-[10px] border-b border-gray-100 dark:border-gray-700">
                        <th class="px-4 py-3 text-left">Type</th>
                        <th class="px-4 py-3 text-left">Recipient</th>
                        <th class="px-4 py-3 text-left">Subject</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-right">Sent</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($recentLogs as $log)
                    <tr class="hover:bg-blue-50/50 dark:hover:bg-blue-900/10">
                        <td class="px-4 py-3">
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $log['type'] === 'Claim' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">{{ $log['type'] }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $log['recipient'] ?: '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300 truncate max-w-xs">{{ $log['subject'] ?: '—' }}</td>
                        <td class="px-4 py-3">
                            @php $__ok = in_array($log['status'], ['sent','delivered','opened','clicked']); @endphp
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $__ok ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">{{ ucfirst($log['status']) }}</span>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-400">{{ $log['at'] ? $log['at']->format('d M Y, h:i A') : '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-gray-400">No emails logged yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
</x-app-layout>
