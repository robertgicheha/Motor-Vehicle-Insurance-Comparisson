<x-app-layout>
    <div class="space-y-6 fade-in">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Certificates / Stickers</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Track all generated insurance certificates across companies</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.reports.index', ['tab' => 'stickers']) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                    View Report
                </a>
            </div>
        </div>

        <div class="grid grid-cols-5 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium">Total Generated</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['total']) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium">Today</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['today']) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium">This Week</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['this_week']) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium">This Month</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['this_month']) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium">Total Downloads</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['total_downloads']) }}</p>
            </div>
        </div>

        @if($byCompany->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-3 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Performance by Company</h3>
                    <span class="text-xs text-gray-400">Generated &amp; Downloads</span>
                </div>
                <div class="p-5">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700/50">
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Company</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Generated</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Downloads</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Download Rate</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Share</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
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
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $item['name'] }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-2">
                                            <span class="font-semibold text-gray-900 dark:text-white">{{ $count }}</span>
                                            <div class="w-24 h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                                <div class="h-full bg-blue-600 rounded-full" style="width:{{ $barWidth }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-2">
                                            <span class="font-semibold text-gray-900 dark:text-white">{{ $downloads }}</span>
                                            <div class="w-24 h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                                <div class="h-full bg-green-500 rounded-full" style="width:{{ $dlBarWidth }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">{{ $rate }}%</td>
                                    <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">{{ $share }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-3 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900 dark:text-white">All Certificates</h3>
                <span class="text-xs text-gray-400">{{ $stickers->total() }} total</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/50">
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Number</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Company</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Customer</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Vehicle</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Premium</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-500 dark:text-gray-400">Downloads</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Via</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Date</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($stickers as $sticker)
                            <tr>
                                <td class="px-4 py-3 font-mono text-xs font-bold text-gray-900 dark:text-white">{{ $sticker->sticker_number }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $sticker->company->name }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $sticker->customer_name }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $sticker->car_make }} {{ $sticker->car_model }} ({{ $sticker->car_year }})</td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">KES {{ number_format($sticker->premium) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $sticker->status === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                                        {{ ucfirst($sticker->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $sticker->download_count }}</span>
                                    @if($sticker->last_downloaded_at)
                                        <span class="block text-[10px] text-gray-400">{{ $sticker->last_downloaded_at->format('d M') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                        {{ strtoupper($sticker->created_via) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-500 text-xs">{{ $sticker->created_at->format('d M Y, h:i A') }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('stickers.show', $sticker) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No certificates generated yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($stickers->hasPages())
                <div class="px-5 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $stickers->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
