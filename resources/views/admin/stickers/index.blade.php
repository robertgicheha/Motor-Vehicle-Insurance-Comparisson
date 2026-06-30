<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

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

        <div class="mt-6 grid grid-cols-2 md:grid-cols-5 gap-3">
            <a href="{{ route('admin.stickers.index') }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar"></div>
                <p class="kpi-eyebrow">Total Generated</p>
                <p class="kpi-value mt-1">{{ number_format($stats['total']) }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">All certificates</span>
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
            <a href="{{ route('admin.reports.index', ['tab' => 'stickers']) }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#fb7185,#fda4af)"></div>
                <p class="kpi-eyebrow">Total Downloads</p>
                <p class="kpi-value mt-1">{{ number_format($stats['total_downloads']) }}</p>
                <span class="kpi-delta down mt-1.5 inline-block">All downloads</span>
            </a>
        </div>
    </div>

    @if($byCompany->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Performance by Company</h2>
                    <p class="text-[10px] text-gray-400 mt-0.5">Generated &amp; Downloads</p>
                </div>
            </div>
            <div class="px-5 py-4">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/40">
                            <th class="px-4 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Company</th>
                            <th class="px-4 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Generated</th>
                            <th class="px-4 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Downloads</th>
                            <th class="px-4 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Download Rate</th>
                            <th class="px-4 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Share</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @php $maxCount = $byCompany->max('count'); $maxDownloads = $byCompany->max('downloads'); @endphp
                        @foreach($byCompany as $item)
                            @php
                                $count = $item['count'];
                                $downloads = $item['downloads'];
                                $rate = $count > 0 ? round(($downloads / $count) * 100, 1) : 0;
                                $share = $stats['total'] > 0 ? round(($count / $stats['total']) * 100, 1) : 0;
                                $barWidth = $maxCount > 0 ? ($count / $maxCount) * 100 : 0;
                                $dlBarWidth = $maxDownloads > 0 ? ($downloads / $maxDownloads) * 100 : 0;
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
                                <td class="px-4 py-2.5">
                                    <div class="flex items-center justify-end gap-2">
                                        <span class="font-semibold text-gray-800 dark:text-white">{{ $downloads }}</span>
                                        <div class="w-20 h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                            <div class="h-full bg-green-500 rounded-full" style="width:{{ $dlBarWidth }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-2.5 text-right font-semibold text-gray-800 dark:text-white">{{ $rate }}%</td>
                                <td class="px-4 py-2.5 text-right font-semibold text-gray-800 dark:text-white">{{ $share }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div>
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">All Certificates</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">{{ $stickers->total() }} total</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/40">
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Number</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Company</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Customer</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Vehicle</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Premium</th>
                        <th class="px-5 py-2.5 text-center font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Status</th>
                        <th class="px-5 py-2.5 text-center font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Downloads</th>
                        <th class="px-5 py-2.5 text-center font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Via</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Date</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($stickers as $sticker)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-5 py-2.5 font-mono text-[10px] font-bold text-gray-800 dark:text-white">{{ $sticker->sticker_number }}</td>
                            <td class="px-5 py-2.5 font-medium text-gray-800 dark:text-white">{{ $sticker->company->name }}</td>
                            <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">{{ $sticker->customer_name }}</td>
                            <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">{{ $sticker->car_make }} {{ $sticker->car_model }} ({{ $sticker->car_year }})</td>
                            <td class="px-5 py-2.5 text-right font-semibold text-gray-800 dark:text-white">KES {{ number_format($sticker->premium) }}</td>
                            <td class="px-5 py-2.5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $sticker->status === 'active' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                                    {{ ucfirst($sticker->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-2.5 text-center">
                                <span class="font-semibold text-gray-800 dark:text-white">{{ $sticker->download_count }}</span>
                                @if($sticker->last_downloaded_at)
                                    <span class="block text-[10px] text-gray-400">{{ $sticker->last_downloaded_at->format('d M') }}</span>
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
                        <tr><td colspan="10" class="px-5 py-10 text-center text-xs text-gray-400">No certificates generated yet.</td></tr>
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
