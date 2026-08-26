<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    {{-- ─── HERO ─────────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Dashboard</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">{{ $greeting }}, {{ auth()->user()->first_name ?: auth()->user()->name }}!</h1>
                <p class="mt-0.5 text-xs text-slate-400">Compare and generate insurance quotations</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                    <div class="flex items-center gap-2 px-3 py-1.5 text-xs font-medium border border-white/20 text-white/80 rounded-lg" x-data="userWeather()" x-init="init()">
                        <span x-html="emoji">🌤️</span>
                        <span><span x-text="temp">--</span>°C</span>
                        <span class="text-white/60 hidden sm:inline" id="weather-city">Nairobi</span>
                    </div>


                <a href="{{ route('quotations.create') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    New Quotation
                </a>
            </div>
        </div>

    

        {{-- ─── KPI CARDS (4-COLUMN) ────────────────────────────────────── --}}
        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-3">
            <a href="{{ route('quotations.index') }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar"></div>
                <p class="kpi-eyebrow">Total Quotations</p>
                <p class="kpi-value mt-1">{{ $totalQuotations }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">All your quotes</span>
            </a>
            <a href="{{ route('quotations.index') }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#34d399,#6ee7b7)"></div>
                <p class="kpi-eyebrow">This Week</p>
                <p class="kpi-value mt-1">{{ $weeklyQuotations }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Generated this week</span>
            </a>
            <a href="{{ route('stickers.issue') }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#818cf8,#a5b4fc)"></div>
                <p class="kpi-eyebrow">Stickers</p>
                <p class="kpi-value mt-1">{{ $stickersCount }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Certificates issued</span>
            </a>
            <a href="{{ route('profile.edit') }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#fb7185,#fda4af)"></div>
                <p class="kpi-eyebrow">Profile</p>
                <p class="kpi-value mt-1">{{ $profileCompletion }}%</p>
                <span class="kpi-delta up mt-1.5 inline-block">Profile complete</span>
            </a>
        </div>

      {{-- ─── LATEST QUOTATION NOTIFICATION ───────────────────────────── --}}
        @if(isset($recentQuotation))
            <div
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 10000)"
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                class="bg-green-600 rounded-2xl mt-5 px-5 py-4 flex items-center justify-between shadow-sm">

                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-white">
                        Latest Quotation Added
                    </p>

                    <p class="text-sm font-bold text-white mt-0.5">
                        {{ $recentQuotation->quotation_number }}
                    </p>

                    <p class="text-xs text-white">
                        {{ $recentQuotation->car_make }}
                        {{ $recentQuotation->car_model }}
                        — KES {{ number_format($recentQuotation->total_premium, 2) }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('quotations.show', $recentQuotation) }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-white/20 text-white rounded-lg hover:bg-white/30 transition">
                        View Details
                    </a>

                    <button
                        @click="show = false"
                        type="button"
                        class="flex items-center justify-center w-8 h-8 rounded-full hover:bg-white/20 text-white transition"
                        aria-label="Close">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- ─── PROFILE COMPLETION ALERT ─────────────────────────────────────── --}}
    @if(!auth()->user()->profile_completed)
    <div class="rounded-2xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 px-5 py-4 flex items-center justify-between gap-4 fade-in">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">Complete Your Profile</p>
                <p class="text-xs text-amber-700 dark:text-amber-400 mt-0.5">Your profile is incomplete. Please add your phone number and other details to finish setting up your account.</p>
            </div>
        </div>
        <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition shrink-0">
            Complete Profile
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
    @endif

    {{-- ─── TWO-COLUMN GRID ─────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ─── LEFT COLUMN (2/3) ───────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- QUOTE FORM --}}
            @include('dashboard.partials.quick-quote')

            {{-- A. RECENT QUOTATIONS TABLE ────────────────────────────────── --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Recent Quotations</h2>
                        <p class="text-[10px] text-gray-400 mt-0.5">Your latest generated quotations</p>
                    </div>
                    <a href="{{ route('quotations.index') }}" class="text-[10px] font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400">View all</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700/40">
                                <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">#</th>
                                <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Company</th>
                                <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Vehicle</th>
                                <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Valuation</th>
                                <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Premium</th>
                                <th class="px-5 py-2.5 text-center font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Status</th>
                                <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($recentQuotations ?? $quotations->take(5) as $q)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <td class="px-5 py-2.5">
                                        <a href="{{ route('quotations.show', $q) }}" class="font-medium text-blue-600 hover:text-blue-800">{{ $q->quotation_number }}</a>
                                    </td>
                                    <td class="px-5 py-2.5 font-medium text-gray-800 dark:text-white">{{ $q->company->name }}</td>
                                    <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">{{ $q->car_make }} {{ $q->car_model }} ({{ $q->car_year }})</td>
                                    <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">{{ number_format($q->car_valuation, 0) }}</td>
                                    <td class="px-5 py-2.5 text-right font-medium text-gray-800 dark:text-white">{{ number_format($q->total_premium, 0) }}</td>
                                    <td class="px-5 py-2.5 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                            {{ ucfirst($q->status) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">{{ $q->created_at->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center text-xs text-gray-400">
                                        No quotations yet.
                                        <a href="{{ route('quotations.create') }}" class="text-blue-600 hover:underline ml-1">Generate your first quotation</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

                {{-- C. QUOTATION INSIGHTS CHARTS ──────────────────────────────── --}}
                <div
                    id="mic-quote-charts"
                    class="space-y-6"
                    data-chart-data="{{ json_encode([
                        'status' => $quotationStatusBreakdown ?? [],
                        'byCompany' => $companyQuotationCounts ?? [],
                        'monthly' => $quotationsByMonth ?? [],
                        'premiumByCompany' => $companyPremiumTotals ?? [],
                        'companyColors' => $companyColorMap ?? [],
                    ]) }}"
                >
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                
                        {{-- C1. STATUS BREAKDOWN (PIE) ───────────────────────── --}}
                        <div class="chart-card bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Quotation Status</h2>
                                <p class="text-[10px] text-gray-400 mt-0.5">Breakdown by status</p>
                            </div>
                            <div class="p-4">
                                @if(isset($quotationStatusBreakdown) && count($quotationStatusBreakdown) > 0)
                                    <div class="chart-wrap chart-wrap-sm">
                                        <canvas id="statusPieChart"></canvas>
                                    </div>
                                    <ul id="statusPieLegend" class="chart-legend mt-3"></ul>
                                @else
                                    <p class="chart-empty">No status data yet.</p>
                                @endif
                            </div>
                        </div>
                
                        {{-- C2. QUOTATIONS BY COMPANY (BAR) ──────────────────── --}}
                        <div class="chart-card bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Quotations by Company</h2>
                                <p class="text-[10px] text-gray-400 mt-0.5">Volume per insurer</p>
                            </div>
                            <div class="p-4">
                                @if(isset($companyQuotationCounts) && count($companyQuotationCounts) > 0)
                                    <div class="chart-wrap chart-wrap-sm">
                                        <canvas id="companyBarChart"></canvas>
                                    </div>
                                @else
                                    <p class="chart-empty">No company data yet.</p>
                                @endif
                            </div>
                        </div>
                
                        {{-- C3. MONTHLY QUOTATIONS (LINE) ────────────────────── --}}
                        <div class="chart-card bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Quotations Monthly</h2>
                                <p class="text-[10px] text-gray-400 mt-0.5">Volume over time</p>
                            </div>
                            <div class="p-4">
                                @if(isset($quotationsByMonth) && count($quotationsByMonth) > 0)
                                    <div class="chart-wrap chart-wrap-sm">
                                        <canvas id="monthlyLineChart"></canvas>
                                    </div>
                                @else
                                    <p class="chart-empty">No monthly data yet.</p>
                                @endif
                            </div>
                        </div>
                
                        {{-- C4. PREMIUM COMPARISON BY COMPANY (BAR, WIDE) ────── --}}
                        <div class="chart-card bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden sm:col-span-2 lg:col-span-3">
                            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Premium Comparison</h2>
                                <p class="text-[10px] text-gray-400 mt-0.5">Total premium generated per company</p>
                            </div>
                            <div class="p-4">
                                @if(isset($companyPremiumTotals) && count($companyPremiumTotals) > 0)
                                    <div class="chart-wrap chart-wrap-wide">
                                        <canvas id="premiumBarChart"></canvas>
                                    </div>
                                @else
                                    <p class="chart-empty">No premium data yet.</p>
                                @endif
                            </div>
                        </div>
                
                    </div>
                </div>
                
                {{-- D. COMPANY COMPARISON TABLE ───────────────────────────────── --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Company Comparison</h2>
                        <p class="text-[10px] text-gray-400 mt-0.5">Your totals per insurer</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/40">
                                    <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Company</th>
                                    <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Quotations</th>
                                    <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Total Premium</th>
                                    <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Avg Premium</th>
                                    <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Share</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($companyComparison ?? [] as $row)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                        <td class="px-5 py-2.5 font-medium text-gray-800 dark:text-white">{{ $row['company'] }}</td>
                                        <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">{{ number_format($row['total_quotations']) }}</td>
                                        <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">{{ number_format($row['total_premium'], 0) }}</td>
                                        <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">{{ number_format($row['avg_premium'], 0) }}</td>
                                        <td class="px-5 py-2.5 text-right">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                                {{ number_format($row['market_share'], 1) }}%
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-5 py-10 text-center text-xs text-gray-400">
                                            No company data yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>


        </div>

        {{-- ─── RIGHT COLUMN (1/3) ───────────────────────────────────────── --}}
        <div class="space-y-6">

            {{-- A. QUICK ACTIONS ─────────────────────────────────────────── --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Quick Actions</h2>
                    <p class="text-[10px] text-gray-400 mt-0.5">Frequently used tasks</p>
                </div>
                <div class="p-4 grid grid-cols-2 gap-2">
                    <a href="{{ route('quotations.create') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:border-blue-300 dark:hover:border-blue-700 transition no-underline">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-gray-800 dark:text-white">New Quotation</div>
                            <div class="text-[10px] text-gray-400">Generate a quote</div>
                        </div>
                    </a>
                    <a href="{{ route('quotations.index') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:border-blue-300 dark:hover:border-blue-700 transition no-underline">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-gray-800 dark:text-white">My Quotations</div>
                            <div class="text-[10px] text-gray-400">View all quotes</div>
                        </div>
                    </a>
                    <a href="{{ route('stickers.issue') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:border-blue-300 dark:hover:border-blue-700 transition no-underline">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-gray-800 dark:text-white">Issue Certificate</div>
                            <div class="text-[10px] text-gray-400">Upload KYC & issue sticker</div>
                        </div>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:border-blue-300 dark:hover:border-blue-700 transition no-underline">
                        <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-gray-800 dark:text-white">My Profile</div>
                            <div class="text-[10px] text-gray-400">Update details</div>
                        </div>
                    </a>
                    <a href="{{ route('claims.create') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-red-50 dark:hover:bg-red-900/20 hover:border-red-300 dark:hover:border-red-700 transition no-underline">
                        <div class="w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-gray-800 dark:text-white">Log Claim</div>
                            <div class="text-[10px] text-gray-400">For you or a customer</div>
                        </div>
                    </a>
                    <a href="{{ route('claims.mine') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-red-50 dark:hover:bg-red-900/20 hover:border-red-300 dark:hover:border-red-700 transition no-underline">
                        <div class="w-8 h-8 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-gray-800 dark:text-white">My Claims</div>
                            <div class="text-[10px] text-gray-400">{{ $myClaimsCount ?? 0 }} claims logged</div>
                        </div>
                    </a>
                </div>
            </div>

            {{-- B. COMPANY BREAKDOWN ───────────────────────────────────────── --}}
            @if(isset($quotationsByCompany) && $quotationsByCompany->sum() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Company Breakdown</h2>
                    <p class="text-[10px] text-gray-400 mt-0.5">Quotations by insurer</p>
                </div>
                <div class="p-4 flex justify-center">
                    <canvas id="userCompanyChart" style="max-width:260px;max-height:260px"></canvas>
                </div>
            </div>
            @endif

            {{-- C. ACTIVITY CALENDAR ─────────────────────────────────────── --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Activity Calendar</h2>
                    <p class="text-[10px] text-gray-400 mt-0.5">Quotation submission days</p>
                </div>
                <div class="p-3" x-data="{
                    month: new Date().getMonth(),
                    year: new Date().getFullYear(),
                    showModal: false,
                    modalDate: '',
                    modalQuotes: [],
                    quotationDates: {{ json_encode($quotationDates->map(fn($d) => ['date' => $d['date'], 'count' => $d['count']])->values()) }},
                    allQuotationData: {{ json_encode($quotationDates) }},
                    daysInMonth() { return new Date(this.year, this.month + 1, 0).getDate(); },
                    firstDay() { return new Date(this.year, this.month, 1).getDay(); },
                    monthName() { return new Date(this.year, this.month).toLocaleString('default', { month: 'long' }); },
                    hasQuotations(day) {
                        const dateStr = this.year + '-' + String(this.month + 1).padStart(2, '0') + '-' + String(day).padStart(2, '0');
                        return this.quotationDates.some(d => d.date === dateStr);
                    },
                    getDayColors(day) {
                        const dateStr = this.year + '-' + String(this.month + 1).padStart(2, '0') + '-' + String(day).padStart(2, '0');
                        const dayData = this.allQuotationData.filter(d => d.date === dateStr);
                        if (!dayData.length) return [];
                        const colors = [];
                        dayData.forEach(dd => {
                            (dd.items || []).forEach(item => {
                                if (item.company_color && !colors.includes(item.company_color)) colors.push(item.company_color);
                            });
                        });
                        return colors.slice(0, 3);
                    },
                    getQuotations(day) {
                        const dateStr = this.year + '-' + String(this.month + 1).padStart(2, '0') + '-' + String(day).padStart(2, '0');
                        return this.allQuotationData.filter(d => d.date === dateStr);
                    },
                    prevMonth() {
                        if (this.month === 0) { this.month = 11; this.year--; }
                        else { this.month--; }
                    },
                    nextMonth() {
                        if (this.month === 11) { this.month = 0; this.year++; }
                        else { this.month++; }
                    },
                    openModal(day) {
                        if (!this.hasQuotations(day)) return;
                        this.modalDate = day + ' ' + this.monthName() + ' ' + this.year;
                        this.modalQuotes = this.getQuotations(day);
                        this.showModal = true;
                    },
                    closeModal() { this.showModal = false; }
                }">
                    <div class="flex items-center justify-between mb-2">
                        <button @click="prevMonth()" class="p-0.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <span class="text-[11px] font-semibold text-gray-800 dark:text-white" x-text="monthName() + ' ' + year"></span>
                        <button @click="nextMonth()" class="p-0.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-7 gap-0.5 text-center mb-0.5">
                        <template x-for="d in ['S','M','T','W','T','F','S']" :key="d">
                            <div class="text-[9px] font-semibold text-gray-400 py-0.5" x-text="d"></div>
                        </template>
                    </div>

                    <div class="grid grid-cols-7 gap-0.5 text-center">
                        <template x-for="blank in firstDay()" :key="'b' + blank">
                            <div></div>
                        </template>
                        <template x-for="day in daysInMonth()" :key="day">
                            <div>
                                <div @click="openModal(day)"
                                     :class="{
                                         'cursor-pointer': hasQuotations(day),
                                         'cursor-default': !hasQuotations(day)
                                     }"
                                     class="relative flex items-center justify-center text-[10px] font-medium text-gray-700 dark:text-gray-300 rounded transition"
                                     :style="'width:24px;height:24px;margin:0 auto;' + (hasQuotations(day) ? 'background:' + getDayColors(day)[0] + '20;color:' + getDayColors(day)[0] + ';font-weight:700' : '')">
                                    <span x-text="day"></span>
                                    <template x-if="hasQuotations(day) && getDayColors(day).length > 0">
                                        <span class="absolute -bottom-0.5 left-1/2 -translate-x-1/2 flex gap-0.5">
                                            <template x-for="(color, ci) in getDayColors(day)" :key="ci">
                                                <span :style="'width:3px;height:3px;border-radius:50%;background:' + color"></span>
                                            </template>
                                            <template x-if="getDayColors(day).length > 3">
                                                <span class="text-[6px] leading-none" style="color:#9ca3af">+</span>
                                            </template>
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Modal --}}
                    <template x-teleport="body">
                        <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.5);backdrop-filter:blur(2px)">
                            <div @click.away="closeModal()" class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full mx-4 max-h-[80vh] overflow-y-auto">
                                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                    <div>
                                        <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Quotations</p>
                                        <h3 class="text-sm font-bold text-gray-800 dark:text-white mt-0.5" x-text="modalDate"></h3>
                                    </div>
                                    <button @click="closeModal()" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                                <div class="px-5 py-3 space-y-2">
                                    <template x-for="(dayData, idx) in modalQuotes" :key="idx">
                                        <template x-for="(item, iidx) in dayData.items" :key="iidx">
                                            <a x-bind:href="'{{ url('quotations') }}/' + item.id" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/30 transition no-underline">
                                                <div :style="'width:4px;height:36px;border-radius:2px;background:' + (item.company_color || '#1756c8')" class="shrink-0"></div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-xs font-bold text-gray-800 dark:text-white" x-text="item.quotation_number"></span>
                                                        <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded" :style="'background:' + (item.company_color || '#1756c8') + '20;color:' + (item.company_color || '#1756c8')" x-text="item.company"></span>
                                                    </div>
                                                    <p class="text-[11px] text-gray-500 mt-0.5">
                                                        <span x-text="item.user_name"></span>
                                                        <span class="mx-1">&middot;</span>
                                                        <span x-text="item.car"></span>
                                                    </p>
                                                </div>
                                                <div class="text-right shrink-0">
                                                    <div class="text-xs font-bold text-gray-800 dark:text-white" x-text="'KES ' + Number(item.premium).toLocaleString()"></div>
                                                </div>
                                            </a>
                                        </template>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- A2. RECENT MY CLAIMS ─────────────────────────────────────── --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <h2 class="text-xs font-semibold text-gray-800 dark:text-white">My Claims</h2>
                        <p class="text-[10px] text-gray-400 mt-0.5">Your latest claims</p>
                    </div>
                    <a href="{{ route('claims.mine') }}" class="text-[10px] font-semibold text-blue-600 dark:text-blue-400 hover:underline no-underline">View all →</a>
                </div>
                <div class="p-4 space-y-3">
                    @forelse($recentMyClaims ?? [] as $claim)
                    <a href="{{ route('claims.show', $claim) }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/30 transition no-underline">
                        <div class="w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-gray-800 dark:text-white">{{ $claim->claim_number }}</span>
                                <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded {{ $claim->status === 'settled' ? 'bg-emerald-100 text-emerald-700' : ($claim->status === 'declined' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">{{ \App\Models\Claim::statuses()[$claim->status] ?? $claim->status }}</span>
                            </div>
                            <p class="text-[11px] text-gray-500 mt-0.5">
                                {{ $claim->registration_number }} <span class="mx-1">&middot;</span> {{ $claim->created_at->format('d M Y') }}
                            </p>
                        </div>
                    </a>
                    @empty
                    <div class="text-center py-4">
                        <p class="text-xs text-gray-400">No claims yet.</p>
                        <a href="{{ route('claims.create') }}" class="inline-block mt-2 text-[10px] font-semibold text-blue-600 dark:text-blue-400 hover:underline">Log your first claim</a>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- B. RECENT ACTIVITY ───────────────────────────────────────── --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Recent Activity</h2>
                    <p class="text-[10px] text-gray-400 mt-0.5">Your latest actions</p>
                </div>
                <div class="p-4 space-y-3">
                    @forelse($recentActivities as $activity)
                        @php
                            $description = $activity->description ?? $activity['description'] ?? 'Action performed';
                            $activityUserId = $activity->user_id ?? $activity['user_id'] ?? null;
                            $timeAgo = $activity->created_at?->diffForHumans() ?? $activity['time_ago'] ?? '';

                            if ($activityUserId && (int)$activityUserId === (int)auth()->id()) {
                                $description = preg_replace('/^User\s+\S+(\s+\S+)?\s+/', 'You ', $description);
                                $description = str_replace(['updated profile', 'logged in'], ['updated your profile', 'logged in'], $description);
                            } else if ($activityUserId) {
                                $activityUser = \App\Models\User::find($activityUserId);
                                if ($activityUser) {
                                    $description = preg_replace('/^User\s+\S+(\s+\S+)?/', $activityUser->name, $description);
                                }
                            }

                            $descLower = strtolower($description);
                            if (str_contains($descLower, 'quotation') || str_contains($descLower, 'quote')) {
                                $bgColor = 'bg-blue-500';
                                $iconColor = 'text-blue-100';
                                $icon = 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
                            } elseif (str_contains($descLower, 'login') || str_contains($descLower, 'register') || str_contains($descLower, 'sign')) {
                                $bgColor = 'bg-emerald-500';
                                $iconColor = 'text-emerald-100';
                                $icon = 'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1';
                            } elseif (str_contains($descLower, 'profile') || str_contains($descLower, 'update')) {
                                $bgColor = 'bg-purple-500';
                                $iconColor = 'text-purple-100';
                                $icon = 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z';
                            } elseif (str_contains($descLower, 'sticker') || str_contains($descLower, 'certificate')) {
                                $bgColor = 'bg-amber-500';
                                $iconColor = 'text-amber-100';
                                $icon = 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z';
                            } else {
                                $bgColor = 'bg-gray-500';
                                $iconColor = 'text-gray-100';
                                $icon = 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z';
                            }
                        @endphp
                        <div class="flex items-start gap-3">
                            <div class="w-7 h-7 rounded-full {{ $bgColor }} flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5 {{ $iconColor }} text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] font-medium text-gray-800 dark:text-white leading-tight">{{ $description }}</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $timeAgo }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6">
                            <svg class="w-8 h-8 mx-auto text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-xs text-gray-400 mt-2">No recent activity</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- C. NOTIFICATIONS & ALERTS ─────────────────────────────────--}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Notifications &amp; Alerts</h2>
                    <p class="text-[10px] text-gray-400 mt-0.5">Your latest updates</p>
                </div>
                <div class="p-4 space-y-3">
                    @forelse($recentNotifications as $n)
                        @php
                            $notifMsg = $n->data['message'] ?? $n->data['action'] ?? 'Notification';
                            $descLower = strtolower($notifMsg);
                            if (str_contains($descLower, 'quotation') || str_contains($descLower, 'quote')) {
                                $bgColor = 'bg-blue-500'; $icon = 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
                            } elseif (str_contains($descLower, 'sticker') || str_contains($descLower, 'certificate')) {
                                $bgColor = 'bg-amber-500'; $icon = 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z';
                            } elseif (str_contains($descLower, 'status') || str_contains($descLower, 'change')) {
                                $bgColor = 'bg-purple-500'; $icon = 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
                            } else {
                                $bgColor = 'bg-gray-500'; $icon = 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z';
                            }
                        @endphp
                        <a href="{{ route('notifications.show', $n->id) }}" class="flex items-start gap-3 group">
                            <div class="w-7 h-7 rounded-full {{ $bgColor }} flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] font-medium text-gray-800 dark:text-white leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">{{ $notifMsg }}</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $n->created_at->diffForHumans() }}</p>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-4">
                            <svg class="w-6 h-6 mx-auto text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <p class="text-xs text-gray-400 mt-2">No notifications yet</p>
                        </div>
                    @endforelse
                </div>
                @if($unreadCount > 0)
                <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('notifications.index') }}" class="text-[10px] font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400">View all notifications →</a>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ─── FULL-WIDTH QUOTATIONS TABLE ──────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div>
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">All Quotations</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">Complete list of your generated quotations</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/40">
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">#</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Company</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Vehicle</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Valuation</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Premium</th>
                        <th class="px-5 py-2.5 text-center font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Status</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($quotations->take(5) as $q)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-5 py-2.5">
                                <a href="{{ route('quotations.show', $q) }}" class="font-medium text-blue-600 hover:text-blue-800">{{ $q->quotation_number }}</a>
                            </td>
                            <td class="px-5 py-2.5 font-medium text-gray-800 dark:text-white">{{ $q->company->name }}</td>
                            <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">{{ $q->car_make }} {{ $q->car_model }} ({{ $q->car_year }})</td>
                            <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">{{ number_format($q->car_valuation, 0) }}</td>
                            <td class="px-5 py-2.5 text-right font-medium text-gray-800 dark:text-white">{{ number_format($q->total_premium, 0) }}</td>
                            <td class="px-5 py-2.5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                    {{ ucfirst($q->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">{{ $q->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-xs text-gray-400">
                                No quotations yet.
                                <a href="{{ route('quotations.create') }}" class="text-blue-600 hover:underline ml-1">Generate your first quotation</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{-- ─── PENDING QUOTE SECTION ──────────────────────────────── --}}
    <div id="pending-quote-section" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden" style="display:none;">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div>
                    <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Quote Generated</h2>
                    <p class="text-[10px] text-gray-400 mt-0.5">Your quotation has been saved successfully</p>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                    Generated
                </span>
            </div>
            <a id="pq-view-link" href="#" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-blue-500 text-white rounded-lg hover:bg-blue-400 transition">
                View Full Quote
            </a>
        </div>
        <div class="p-4">
            <div class="flex items-center gap-3 mb-3">
                <div id="pq-company-color" class="w-3 h-3 rounded-full" style="background:#0066B3"></div>
                <span id="pq-company" class="text-sm font-bold text-gray-800 dark:text-white">—</span>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                <div><span class="text-gray-400">Name</span><p id="pq-name" class="font-medium text-gray-800 dark:text-white">—</p></div>
                <div><span class="text-gray-400">Vehicle</span><p id="pq-vehicle" class="font-medium text-gray-800 dark:text-white">—</p></div>
                <div><span class="text-gray-400">Reg</span><p id="pq-reg" class="font-medium text-gray-800 dark:text-white">—</p></div>
                <div><span class="text-gray-400">Value</span><p id="pq-value" class="font-medium text-gray-800 dark:text-white">—</p></div>
                <div><span class="text-gray-400">Rate</span><p id="pq-rate" class="font-medium text-gray-800 dark:text-white">—</p></div>
                <div><span class="text-gray-400">Premium</span><p id="pq-premium" class="font-medium text-gray-800 dark:text-white">—</p></div>
                <div><span class="text-gray-400">Total</span><p id="pq-total" class="font-medium text-gray-800 dark:text-white">—</p></div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <span class="text-[10px] text-gray-400">Quote #<span id="pq-quote-num">—</span></span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@php
    $_companyColors = ['#003087', '#0066B3', '#00205B', '#D0021B'];
    $_idx = 0;
    $_companyData = $companies->map(function($c) use ($_companyColors, &$_idx) {
        $rate = $c->rates->firstWhere('item_name', 'Premium Rate');
        $pvtRate = $c->rates->firstWhere('item_name', 'PVT Rate');
        return [
            'slug' => $c->slug,
            'name' => $c->name,
            'rate' => $rate ? (float) $rate->percentage / 100 : 0,
            'pvtRate' => $pvtRate ? (float) $pvtRate->percentage / 100 : 0,
            'color' => $_companyColors[$_idx++ % 4],
            'prefix' => $c->getInitials(),
        ];
    })->values();
@endphp
<script>
window.MIC_COMPANIES = @json($_companyData);
window.MIC_QUOTE_COMPANIES = @json($quoteCompanies->values());
window.MIC_QUOTE_COMPANY_DATA_PRIVATE = @json($quoteCompanyDataPrivate ?? []);
window.MIC_QUOTE_COMPANY_DATA_PSV = @json($quoteCompanyDataPsv ?? []);
</script>
<script src="{{ asset('js/dashboard-quote.js') }}"></script>
<script>
(function(){
  var urlParams = new URLSearchParams(window.location.search);
  var hasPending = urlParams.has('pending_quote');
  var pendingCompany = sessionStorage.getItem('pending_quote_company');
  var pendingData = sessionStorage.getItem('micQuoteData');

  if ((hasPending || pendingCompany) && pendingData) {
    try {
      var data = JSON.parse(pendingData);
      var company = (window.MIC_COMPANIES || []).find(function(c) { return c.slug === pendingCompany; });
      if (!company) { console.warn('Company not found:', pendingCompany); return; }

      var premium = (data.value || 0) * company.rate;
      var pvt = (data.value || 0) * (company.pvtRate || 0);
      var levis = pvt * 0.0045;
      var stampDuty = 40;
      var total = premium + pvt + levis + stampDuty;
      var fmtNum = function(n) { return 'KES ' + Number(n).toLocaleString('en-KE', {minimumFractionDigits:0, maximumFractionDigits:0}); };
      var name = [data.salutation, data.firstName, data.lastName].filter(Boolean).join(' ');

      document.getElementById('pending-quote-section').style.display = 'block';
      document.getElementById('pq-company').textContent = company.name;
      document.getElementById('pq-company-color').style.background = company.color;
      document.getElementById('pq-name').textContent = name || '—';
      document.getElementById('pq-vehicle').textContent = ((data.make || '') + ' ' + (data.model || '')).trim() || '—';
      document.getElementById('pq-reg').textContent = data.reg || '—';
      document.getElementById('pq-value').textContent = fmtNum(data.value);
      document.getElementById('pq-premium').textContent = fmtNum(premium);
      document.getElementById('pq-total').textContent = fmtNum(total);
      document.getElementById('pq-rate').textContent = (company.rate * 100).toFixed(1) + '%';

      fetch('/api/quotation/from-session', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        body: JSON.stringify({
          company_slug: pendingCompany,
          car_make: data.make || '',
          car_model: data.model || '',
          car_year: data.year || '',
          car_registration: data.reg || '',
          car_valuation: Number(data.value) || 0,
          vehicle_type: data.vehicleType || '',
          num_passengers: Number(data.numPassengers) || 1,
        }),
      })
      .then(function(r) { return r.json(); })
      .then(function(res) {
        if (res.success) {
          showDashboardToast('Your Quotation has been successfully generated. <a href=\'/my-quote\' style=\'color:#fff;font-weight:700;text-decoration:underline\'>Click here to view it</a>', 'success');
          document.getElementById('pq-quote-num').textContent = res.quotation.quotation_number;
          document.getElementById('pq-view-link').href = '/my-quote';

          sessionStorage.removeItem('pending_quote_company');
          sessionStorage.removeItem('micQuoteData');
          sessionStorage.removeItem('auth_redirect');

          setTimeout(function() {
            window.location.href = '/dashboard';
          }, 4000);
        }
      })
      .catch(function(e) {
        console.warn('Failed to save quotation:', e);
        renderPendingQuoteFromSession(pendingCompany, data, company);
      });

    } catch(e) { console.warn('Pending quote error:', e); }
  }

  function showDashboardToast(message, type) {
    type = type || 'success';
    var container = document.createElement('div');
    container.id = 'pending-toast-container';
    container.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:8px';
    document.body.appendChild(container);

    var toast = document.createElement('div');
    toast.style.cssText = 'padding:12px 20px;border-radius:8px;font-size:13px;font-weight:600;box-shadow:0 4px 20px rgba(0,0,0,0.15);display:flex;align-items:center;gap:8px';
    if (type === 'success') {
      toast.style.background = '#059669';
      toast.style.color = 'white';
      toast.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> ' + message;
    } else {
      toast.style.background = '#DC2626';
      toast.style.color = 'white';
      toast.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg> ' + message;
    }
    container.appendChild(toast);

    setTimeout(function() {
      toast.style.opacity = '0';
      toast.style.transition = 'opacity 0.3s';
      setTimeout(function() { container.remove(); }, 300);
    }, 4000);
  }

  function renderPendingQuoteFromSession(pendingCompany, data, company) {
    if (!company) return;
    var quoteNum = company.prefix + '-' + new Date().getFullYear() + '/' + String(Math.floor(Math.random()*9000)+1000);
    document.getElementById('pq-quote-num').textContent = quoteNum;
    document.getElementById('pq-view-link').href = '/my-quote';
    showDashboardToast('Quote preview ready. Sign in to save.', 'info');
  }
})();
</script>
<script>
// ─── Notification Sound ─────────────────────────────────────────────
(function() {
    let lastCount = {{ $unreadCount ?? 0 }};
    var audioCtx = null;

    function playChime() {
        try {
            if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            var osc = audioCtx.createOscillator();
            var gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.frequency.setValueAtTime(880, audioCtx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(1320, audioCtx.currentTime + 0.1);
            gain.gain.setValueAtTime(0.15, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.3);
            osc.start(audioCtx.currentTime);
            osc.stop(audioCtx.currentTime + 0.3);
        } catch(e) { /* silent fallback */ }
    }

    document.addEventListener('notifications-updated', function(e) {
        var newCount = e.detail && typeof e.detail.count !== 'undefined' ? e.detail.count : 0;
        if (newCount > lastCount) {
            playChime();
        }
        lastCount = newCount;
    });
})();

// ─── Page-load sound: chime if new unread since last visit ─────────
(function() {
    var currentCount = {{ auth()->user()->unreadNotifications()->count() }};
    var stored = sessionStorage.getItem('mic_unread_count');
    if (stored !== null && parseInt(stored) < currentCount) {
        try {
            var audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            var osc = audioCtx.createOscillator();
            var gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.frequency.setValueAtTime(880, audioCtx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(1320, audioCtx.currentTime + 0.1);
            gain.gain.setValueAtTime(0.15, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.3);
            osc.start(audioCtx.currentTime);
            osc.stop(audioCtx.currentTime + 0.3);
        } catch(e) {}
    }
    sessionStorage.setItem('mic_unread_count', currentCount);
})();

function userWeather() {
    return {
        emoji: '{{ $we ?? "🌤️" }}',
        temp: '{{ $weather["temperature"] ?? 25 }}',
        pollTimer: null,
        coords: null,
        init() {
            this.detectLocation();
            this.pollTimer = setInterval(() => this.fetchWeather(), 300000);
        },
        detectLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        this.coords = { lat: pos.coords.latitude, lon: pos.coords.longitude };
                        this.fetchWeather();
                    },
                    () => { this.coords = null; this.fetchWeather(); },
                    { timeout: 5000, enableHighAccuracy: false }
                );
            } else {
                this.fetchWeather();
            }
        },
        fetchWeather() {
            var url = '{{ route("dashboard.weather") }}';
            if (this.coords) {
                url += '?lat=' + this.coords.lat + '&lon=' + this.coords.lon;
            }
            fetch(url)
                .then(r => r.json())
                .then(w => {
                    this.temp = w.temperature;
                    var cond = (w.condition || '').toLowerCase();
                    if (cond.includes('clear')) this.emoji = '☀️';
                    else if (cond.includes('cloud')) this.emoji = '☁️';
                    else if (cond.includes('rain')) this.emoji = '🌧️';
                    else if (cond.includes('snow')) this.emoji = '❄️';
                    else if (cond.includes('thunderstorm')) this.emoji = '⛈️';
                    else if (cond.includes('fog')||cond.includes('mist')||cond.includes('haze')) this.emoji = '🌫️';
                    else this.emoji = '🌤️';
                    if (w.city && document.getElementById('weather-city')) {
                        document.getElementById('weather-city').textContent = w.city;
                    }
                })
                .catch(() => {});
        },
        destroy() { if (this.pollTimer) clearInterval(this.pollTimer); }
    };
}
document.addEventListener('DOMContentLoaded', function () {
    @if(isset($quotationsByCompany) && $quotationsByCompany->sum() > 0)
    var ucc = document.getElementById('userCompanyChart');
    if (ucc) {
        new Chart(ucc, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($quotationsByCompany->keys()) !!},
                datasets: [{
                    data: {!! json_encode($quotationsByCompany->values()) !!},
                    backgroundColor: ['#1756c8','#7c3aed','#0f7a65','#d4860e','#c0392b','#0f7a65'],
                    borderWidth: 2,
                    borderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '60%',
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, padding: 8, font: { size: 10 }, color: '#9ca3af' } }
                },
                onClick: function(e, item) {
                    if (item.length > 0) {
                        var label = this.data.labels[item[0].index];
                        window.location.href = '{{ route("quotations.index") }}?company=' + encodeURIComponent(label);
                    }
                }
            }
        });
    }
    @endif

    @if(isset($monthlyQuotations) && count($monthlyQuotations) > 0)
    var ctx = document.getElementById('activityChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode(collect($monthlyQuotations)->pluck('month')->toArray()) !!},
                datasets: [{
                    label: 'Quotations',
                    data: {!! json_encode(collect($monthlyQuotations)->pluck('total')->toArray()) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.08)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: { color: '#9ca3af', font: { size: 10 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#9ca3af', font: { size: 10 } }
                    }
                },
                onClick: function() {
                    window.location.href = '{{ route("quotations.index") }}';
                }
            }
        });
    }
    @endif

    // ─── Session Storage: Draft recovery for /quotations/create ─────
    var createForm = document.getElementById('quotation-create-form');
    if (createForm) {
        var inputs = createForm.querySelectorAll('input, select, textarea');
        var savedData = sessionStorage.getItem('quotation_draft');
        if (savedData) {
            var parsed = JSON.parse(savedData);
            var confirmed = confirm('You have unsaved quotation data from a previous session. Would you like to restore it?');
            if (confirmed) {
                for (var key in parsed) {
                    var input = createForm.querySelector('[name="' + key + '"]');
                    if (input) input.value = parsed[key];
                }
            } else {
                sessionStorage.removeItem('quotation_draft');
            }
        }
        inputs.forEach(function (el) {
            el.addEventListener('change', function () {
                var data = {};
                inputs.forEach(function (inp) {
                    if (inp.name) data[inp.name] = inp.value;
                });
                sessionStorage.setItem('quotation_draft', JSON.stringify(data));
            });
        });
        createForm.addEventListener('submit', function () {
            sessionStorage.removeItem('quotation_draft');
        });
    }
});
</script>
@endpush
</x-app-layout>
