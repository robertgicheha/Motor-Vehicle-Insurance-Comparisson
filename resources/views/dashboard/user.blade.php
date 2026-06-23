<x-app-layout>
    <div class="fade-in">
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

            <a href="{{ route('quotations.create') }}" class="dashboard-btn dashboard-btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                New Quotation
            </a>
        </div>

        <div class="dashboard-section-label">Overview</div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="dashboard-card">
                <div class="flex items-center gap-3">
                    <div class="p-3 rounded-lg" style="background:var(--blue-light)">
                        <svg class="w-6 h-6" style="color:var(--blue)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <p class="dashboard-stat-label">Total Quotations</p>
                        <p class="dashboard-stat-value">{{ $totalQuotations }}</p>
                    </div>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="flex items-center gap-3">
                    <div class="p-3 rounded-lg" style="background:var(--blue-light)">
                        <svg class="w-6 h-6" style="color:var(--blue)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div>
                        <p class="dashboard-stat-label">Today</p>
                        <p class="dashboard-stat-value">{{ $todayQuotations }}</p>
                    </div>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="flex items-center gap-3">
                    <div class="p-3 rounded-lg" style="background:var(--blue-light)">
                        <svg class="w-6 h-6" style="color:var(--blue)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <p class="dashboard-stat-label">Companies</p>
                        <p class="dashboard-stat-value">{{ $companies->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if(isset($recentQuotation))
            <div class="dashboard-card" style="background:linear-gradient(135deg, var(--blue) 0%, #004f8c 100%);border:none;margin-top:16px">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm" style="color:rgba(255,255,255,.8)">Last Quotation</p>
                        <p class="text-lg font-bold text-white">{{ $recentQuotation->quotation_number }}</p>
                        <p class="text-sm" style="color:rgba(255,255,255,.8)">{{ $recentQuotation->car_make }} {{ $recentQuotation->car_model }} - {{ number_format($recentQuotation->total_premium, 2) }} KES</p>
                    </div>
                    <a href="{{ route('quotations.show', $recentQuotation) }}" class="dashboard-btn" style="background:rgba(255,255,255,.2);color:#fff">
                        View Details
                    </a>
                </div>
            </div>
        @endif

        <div class="dashboard-section-label" style="margin-top:32px">Recent Quotations</div>
        <div class="breakdown-table">
            <div class="overflow-x-auto">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Company</th>
                            <th>Vehicle</th>
                            <th>Valuation</th>
                            <th>Premium</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quotations as $q)
                            <tr>
                                <td>
                                    <a href="{{ route('quotations.show', $q) }}" style="color:var(--blue);font-weight:600">{{ $q->quotation_number }}</a>
                                </td>
                                <td>{{ $q->company->name }}</td>
                                <td>{{ $q->car_make }} {{ $q->car_model }} ({{ $q->car_year }})</td>
                                <td>{{ number_format($q->car_valuation, 0) }}</td>
                                <td>{{ number_format($q->total_premium, 0) }}</td>
                                <td>
                                    <span class="dashboard-badge dashboard-badge-active">{{ ucfirst($q->status) }}</span>
                                </td>
                                <td>{{ $q->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center" style="color:var(--text-muted)">
                                    No quotations yet.
                                    <a href="{{ route('quotations.create') }}" style="color:var(--blue);margin-left:4px">Generate your first quotation</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
