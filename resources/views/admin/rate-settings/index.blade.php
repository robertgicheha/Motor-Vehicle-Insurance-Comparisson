<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

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
                <p class="kpi-eyebrow">Rate Entries</p>
                <p class="kpi-value mt-1">{{ $totalRates }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Total rate bands</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#fb7185,#fda4af)"></div>
                <p class="kpi-eyebrow">Categories</p>
                <p class="kpi-value mt-1">{{ $companies->sum(fn($c) => $c->rates->pluck('category')->unique()->count()) }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Unique rate categories</span>
            </div>
        </div>
    </div>

    @forelse($companies as $company)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-micblue flex items-center justify-center text-blue-400 font-bold text-xs">{{ substr($company->name, 0, 1) }}</div>
                    <div>
                        <h3 class="text-xs font-semibold text-gray-800 dark:text-white">{{ $company->name }}</h3>
                        <p class="text-[10px] text-gray-400">{{ $company->rates->count() }} rate entries</p>
                    </div>
                </div>
                <a href="{{ route('admin.companies.rates', $company) }}"
                   class="px-3 py-1.5 text-xs font-medium text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition">
                    Manage Rates
                </a>
            </div>
            @if($company->rates->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700/40">
                                <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Category</th>
                                <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Item</th>
                                <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Value</th>
                                <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Min</th>
                                <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Max</th>
                                <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">%</th>
                                <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Min Premium</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($company->rates as $rate)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">{{ $rate->category }}</td>
                                    <td class="px-5 py-2.5 font-medium text-gray-800 dark:text-white">{{ $rate->item_name }}</td>
                                    <td class="px-5 py-2.5 text-right font-medium text-gray-800 dark:text-white">{{ $rate->getDisplayValueAttribute() }}</td>
                                    <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">{{ $rate->min_value ?? '—' }}</td>
                                    <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">{{ $rate->max_value ?? '—' }}</td>
                                    <td class="px-5 py-2.5 text-right font-semibold text-gray-800 dark:text-white">{{ $rate->percentage ? $rate->percentage . '%' : '—' }}</td>
                                    <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">{{ $rate->min_premium ? 'KES ' . number_format($rate->min_premium) : '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-5 py-8 text-center text-xs text-gray-400">No rates configured for this company.</div>
            @endif
        </div>
    @empty
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 py-12 text-center">
            <p class="text-xs text-gray-400">No companies found.</p>
            <a href="{{ route('admin.companies.create') }}" class="inline-block mt-2 text-xs font-medium text-blue-600 hover:underline">Add your first company</a>
        </div>
    @endforelse
</div>
</x-app-layout>
