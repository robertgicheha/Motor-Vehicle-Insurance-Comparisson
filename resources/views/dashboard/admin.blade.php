<x-app-layout>
    <div class="space-y-6 fade-in">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $greeting }}, {{ auth()->user()->first_name ?: auth()->user()->name }}!
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Admin Dashboard Overview</p>
            </div>

            @if(isset($weather))
                <div class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="text-3xl">
                        @if(str_contains(strtolower($weather['condition']), 'clear')) ☀️
                        @elseif(str_contains(strtolower($weather['condition']), 'cloud')) ☁️
                        @elseif(str_contains(strtolower($weather['condition']), 'rain')) 🌧️
                        @else 🌤️
                        @endif
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $weather['temperature'] }}°C</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ $weather['description'] }} in {{ $weather['city'] }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 card-hover">
                <div class="flex items-center gap-3">
                    <div class="p-3 rounded-lg bg-blue-50 dark:bg-blue-900/30">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Total Users</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $totalUsers }}</p>
                    </div>
                </div>
            </div>
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 card-hover">
                <div class="flex items-center gap-3">
                    <div class="p-3 rounded-lg bg-green-50 dark:bg-green-900/30">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07M13 12a1 1 0 11-2 0 1 1 0 012 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Online</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $onlineUsers }}</p>
                    </div>
                </div>
            </div>
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 card-hover">
                <div class="flex items-center gap-3">
                    <div class="p-3 rounded-lg bg-purple-50 dark:bg-purple-900/30">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Quotations</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $totalQuotations }}</p>
                    </div>
                </div>
            </div>
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 card-hover">
                <div class="flex items-center gap-3">
                    <div class="p-3 rounded-lg bg-orange-50 dark:bg-orange-900/30">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Total Premium</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($totalPremium, 0) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Today</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $todayQuotations }}</p>
                <p class="text-xs text-gray-400 mt-1">quotations generated</p>
            </div>
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">This Week</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $weekQuotations }}</p>
                <p class="text-xs text-gray-400 mt-1">quotations generated</p>
            </div>
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">This Month</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $monthQuotations }}</p>
                <p class="text-xs text-gray-400 mt-1">quotations generated</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quotations by Company</h3>
                <canvas id="companyChart" height="200"></canvas>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Monthly Trend</h3>
                <canvas id="monthlyChart" height="200"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @foreach($companies as $company)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $company->name }}</h3>
                        <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $company->quotations_count }}</span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Quotations</p>
                    <div class="mt-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        @php $maxCount = $companies->max('quotations_count') ?: 1; @endphp
                        <div class="bg-blue-600 dark:bg-blue-500 h-2 rounded-full" style="width: {{ ($company->quotations_count / $maxCount) * 100 }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Quotations</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/50">
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">#</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">User</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Company</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Vehicle</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Premium</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($recentQuotations as $q)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                <td class="px-4 py-3">
                                    <a href="{{ route('quotations.show', $q) }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ $q->quotation_number }}</a>
                                </td>
                                <td class="px-4 py-3">{{ $q->user->name }}</td>
                                <td class="px-4 py-3">{{ $q->company->name }}</td>
                                <td class="px-4 py-3">{{ $q->car_make }} {{ $q->car_model }}</td>
                                <td class="px-4 py-3 text-right font-medium">{{ number_format($q->total_premium, 0) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">{{ ucfirst($q->status) }}</span>
                                </td>
                                <td class="px-4 py-3 text-right text-gray-500">{{ $q->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">No quotations yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        new Chart(document.getElementById('companyChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($quotationsByCompany->keys()) !!},
                datasets: [{
                    label: 'Quotations',
                    data: {!! json_encode($quotationsByCompany->values()) !!},
                    backgroundColor: ['rgba(59,130,246,0.7)', 'rgba(139,92,246,0.7)', 'rgba(236,72,153,0.7)'],
                    borderColor: ['rgb(59,130,246)', 'rgb(139,92,246)', 'rgb(236,72,153)'],
                    borderWidth: 2,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                    x: { grid: { display: false } }
                }
            }
        });

        new Chart(document.getElementById('monthlyChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlyQuotations->pluck('month')) !!},
                datasets: [{
                    label: 'Quotations',
                    data: {!! json_encode($monthlyQuotations->pluck('total')) !!},
                    borderColor: 'rgb(59,130,246)',
                    backgroundColor: 'rgba(59,130,246,0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgb(59,130,246)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
