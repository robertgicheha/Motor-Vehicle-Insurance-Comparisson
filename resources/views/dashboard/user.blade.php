<x-app-layout>
    <div class="space-y-6 fade-in">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $greeting }}, {{ auth()->user()->first_name ?: auth()->user()->name }}!
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Compare and generate insurance quotations</p>
            </div>

            @if(isset($weather))
                <div class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="text-3xl">
                        @if(str_contains(strtolower($weather['condition']), 'clear'))
                            ☀️
                        @elseif(str_contains(strtolower($weather['condition']), 'cloud'))
                            ☁️
                        @elseif(str_contains(strtolower($weather['condition']), 'rain'))
                            🌧️
                        @elseif(str_contains(strtolower($weather['condition']), 'snow'))
                            ❄️
                        @else
                            🌤️
                        @endif
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $weather['temperature'] }}°C</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ $weather['description'] }} in {{ $weather['city'] }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 card-hover">
                <div class="flex items-center gap-3">
                    <div class="p-3 rounded-lg bg-blue-50 dark:bg-blue-900/30">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Quotations</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalQuotations }}</p>
                    </div>
                </div>
            </div>

            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 card-hover">
                <div class="flex items-center gap-3">
                    <div class="p-3 rounded-lg bg-green-50 dark:bg-green-900/30">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Today</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $todayQuotations }}</p>
                    </div>
                </div>
            </div>

            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 card-hover">
                <div class="flex items-center gap-3">
                    <div class="p-3 rounded-lg bg-purple-50 dark:bg-purple-900/30">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Companies</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $companies->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if(isset($recentQuotation))
            <div class="p-5 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Last Quotation</p>
                        <p class="text-lg font-bold">{{ $recentQuotation->quotation_number }}</p>
                        <p class="text-sm opacity-90">{{ $recentQuotation->car_make }} {{ $recentQuotation->car_model }} - {{ number_format($recentQuotation->total_premium, 2) }} KES</p>
                    </div>
                    <a href="{{ route('quotations.show', $recentQuotation) }}" class="px-4 py-2 bg-white/20 rounded-lg hover:bg-white/30 transition text-sm font-medium">
                        View Details
                    </a>
                </div>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Your Recent Quotations</h2>
                <a href="{{ route('quotations.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                    New Quotation
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/50">
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">#</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Company</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Vehicle</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Valuation</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Premium</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($quotations as $q)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                <td class="px-4 py-3">
                                    <a href="{{ route('quotations.show', $q) }}" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">{{ $q->quotation_number }}</a>
                                </td>
                                <td class="px-4 py-3">{{ $q->company->name }}</td>
                                <td class="px-4 py-3">{{ $q->car_make }} {{ $q->car_model }} ({{ $q->car_year }})</td>
                                <td class="px-4 py-3 text-right">{{ number_format($q->car_valuation, 0) }}</td>
                                <td class="px-4 py-3 text-right font-medium">{{ number_format($q->total_premium, 0) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                        {{ ucfirst($q->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right text-gray-500">{{ $q->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    No quotations yet.
                                    <a href="{{ route('quotations.create') }}" class="text-blue-600 dark:text-blue-400 hover:underline ml-1">Generate your first quotation</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
