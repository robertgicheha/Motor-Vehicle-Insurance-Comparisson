<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    {{-- ─── HERO ─────────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Pricing</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Rate Settings</h1>
                <p class="mt-0.5 text-xs text-slate-400">Overview of premium rates across all insurance companies</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-3">
            <a href="{{ route('admin.companies.index') }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar"></div>
                <p class="kpi-eyebrow">Companies</p>
                <p class="kpi-value mt-1">{{ $totalCompanies }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Registered insurers</span>
            </a>
            <a href="{{ route('admin.companies.index') }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#34d399,#6ee7b7)"></div>
                <p class="kpi-eyebrow">Active</p>
                <p class="kpi-value mt-1">{{ $activeCompanies }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Currently active</span>
            </a>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#818cf8,#a5b4fc)"></div>
                <p class="kpi-eyebrow">Private Rates</p>
                <p class="kpi-value mt-1">{{ $privateRates }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Private entries</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#fb7185,#fda4af)"></div>
                <p class="kpi-eyebrow">PSV Rates</p>
                <p class="kpi-value mt-1">{{ $psvRates }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">PSV entries</span>
            </div>
        </div>
    </div>

    @forelse($companies as $company)
        @php $summary = $companyRateSummary[$company->id] ?? []; @endphp
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    @php $__cl = $company->logoUrl(); @endphp
                    @if($__cl)
                        <img src="{{ $__cl }}" class="w-8 h-8 rounded-lg object-cover border border-gray-100 dark:border-gray-700">
                    @else
                        <div class="w-8 h-8 rounded-lg bg-micblue flex items-center justify-center text-blue-400 font-bold text-xs">{{ substr($company->name, 0, 1) }}</div>
                    @endif
                    <div>
                        <h3 class="text-xs font-semibold text-gray-800 dark:text-white">{{ $company->name }}</h3>
                        <p class="text-[10px] text-gray-400">{{ $company->rates->count() }} total rate entries</p>
                    </div>
                </div>
                <a href="{{ route('admin.companies.rates', $company) }}"
                   class="px-3 py-1.5 text-xs font-medium text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition">
                    Manage Rates
                </a>
            </div>

            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- PRIVATE CARD --}}
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden {{ ($summary['private']['count'] ?? 0) === 0 ? 'opacity-60' : '' }}">
                    <div class="px-4 py-2.5 bg-gray-50 dark:bg-gray-700/30 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10"/></svg>
                            <h4 class="text-xs font-semibold text-gray-800 dark:text-white">Private Comprehensive</h4>
                        </div>
                        <span class="text-[10px] text-gray-400">{{ $summary['private']['count'] ?? 0 }} rates</span>
                    </div>
                    @if(($summary['private']['count'] ?? 0) > 0)
                        <div class="px-4 py-3 space-y-2">
                            @foreach($summary['private']['categories'] ?? [] as $category => $count)
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] text-gray-600 dark:text-gray-400">{{ $category }}</span>
                                    <span class="text-[10px] font-semibold text-gray-800 dark:text-white">{{ $count }} {{ Str::plural('item', $count) }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="px-4 py-2 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/20 text-right">
                            <a href="{{ route('admin.companies.rates', $company) }}?type=private" class="text-[10px] font-medium text-blue-600 hover:text-blue-800">Edit Private Rates →</a>
                        </div>
                    @else
                        <div class="px-4 py-6 text-center text-[11px] text-gray-400">No private rates configured</div>
                    @endif
                </div>

                {{-- PSV CARD --}}
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden {{ ($summary['psv']['count'] ?? 0) === 0 ? 'opacity-60' : '' }}">
                    <div class="px-4 py-2.5 bg-gray-50 dark:bg-gray-700/30 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            <h4 class="text-xs font-semibold text-gray-800 dark:text-white">PSV Comprehensive</h4>
                        </div>
                        <span class="text-[10px] text-gray-400">{{ $summary['psv']['count'] ?? 0 }} rates</span>
                    </div>
                    @if(($summary['psv']['count'] ?? 0) > 0)
                        <div class="px-4 py-3 space-y-2">
                            @foreach($summary['psv']['categories'] ?? [] as $category => $count)
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] text-gray-600 dark:text-gray-400">{{ $category }}</span>
                                    <span class="text-[10px] font-semibold text-gray-800 dark:text-white">{{ $count }} {{ Str::plural('item', $count) }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="px-4 py-2 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/20 text-right">
                            <a href="{{ route('admin.companies.rates', $company) }}?type=psv" class="text-[10px] font-medium text-blue-600 hover:text-blue-800">Edit PSV Rates →</a>
                        </div>
                    @else
                        <div class="px-4 py-6 text-center text-[11px] text-gray-400">No PSV rates configured</div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 py-12 text-center">
            <p class="text-xs text-gray-400">No companies found.</p>
            <a href="{{ route('admin.companies.create') }}" class="inline-block mt-2 text-xs font-medium text-blue-600 hover:underline">Add your first company</a>
        </div>
    @endforelse
</div>
</x-app-layout>
