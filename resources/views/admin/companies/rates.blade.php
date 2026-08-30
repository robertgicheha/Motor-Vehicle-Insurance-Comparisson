<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    {{-- ─── HERO ─────────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Underwriter Directory</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">{{ $company->name }} &mdash; Rates</h1>
                <p class="mt-0.5 text-xs text-slate-400">Manage Underwriter rates, cover limits, excesses and additional coverages</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.companies.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium border border-white/20 text-white/80 rounded-lg hover:bg-white/10 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Underwriters
                </a>
            </div>
        </div>
    </div>

    @php
        $categoryIcons = [
            'Premium Rate' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'Cover Limits' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
            'Third Party Limits' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
            'Excess' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z',
            'Additional Cover' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ];
    @endphp

    {{-- ─── IMPORT TAB BUTTONS ───────────────────────────────────────────── --}}
    <div x-data="{ tab: 'rates' }">
        <div class="flex gap-2 mb-4">
            <button @click="tab = 'rates'" :class="tab === 'rates' ? 'bg-micblue text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700'" class="px-4 py-2 text-xs font-semibold rounded-lg transition">
                <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Rates
            </button>
            <button @click="tab = 'import'" :class="tab === 'import' ? 'bg-micblue text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700'" class="px-4 py-2 text-xs font-semibold rounded-lg transition">
                <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Import Rates
            </button>
            <button @click="tab = 'bulk'" :class="tab === 'bulk' ? 'bg-micblue text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700'" class="px-4 py-2 text-xs font-semibold rounded-lg transition">
                <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                Bulk Import
            </button>
        </div>

        {{-- ─── IMPORT ERRORS ─────────────────────────────────────────── --}}
        @if(session('import_errors'))
            <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-2xl p-4">
                <h4 class="text-xs font-semibold text-rose-700 dark:text-rose-300 mb-2">Import Errors</h4>
                <ul class="list-disc list-inside text-xs text-rose-600 dark:text-rose-400 space-y-0.5">
                    @foreach(session('import_errors') as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ─── TAB: RATES ─────────────────────────────────────────────── --}}
        <div x-show="tab === 'rates'">
            <div x-data="{ vtype: '{{ request('type', 'private') }}' }">
                <div class="flex gap-2 mb-4">
                    <button @click="vtype = 'private'" :class="vtype === 'private' ? 'bg-blue-500 text-white shadow-sm' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700'" class="px-4 py-2 text-xs font-semibold rounded-lg transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10"/></svg>
                        Private Comprehensive
                    </button>
                    <button @click="vtype = 'psv'" :class="vtype === 'psv' ? 'bg-amber-500 text-white shadow-sm' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700'" class="px-4 py-2 text-xs font-semibold rounded-lg transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        PSV Comprehensive
                    </button>
                </div>

                <div x-show="vtype === 'private'">
                    @forelse($privateRates as $category => $categoryRates)
                        @include('admin.companies._rate_category_card', ['categoryRates' => $categoryRates, 'category' => $category, 'categoryIcons' => $categoryIcons, 'company' => $company])
                    @empty
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-10 text-center mb-5">
                            <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            <p class="text-sm text-gray-500 dark:text-gray-400">No private rates defined yet. Add rates below or import from Excel/JSON.</p>
                        </div>
                    @endforelse
                </div>

                <div x-show="vtype === 'psv'">
                    @forelse($psvRates as $category => $categoryRates)
                        @include('admin.companies._rate_category_card', ['categoryRates' => $categoryRates, 'category' => $category, 'categoryIcons' => $categoryIcons, 'company' => $company])
                    @empty
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-10 text-center mb-5">
                            <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            <p class="text-sm text-gray-500 dark:text-gray-400">No PSV rates defined yet. Add rates below or import from Excel/JSON.</p>
                        </div>
                    @endforelse
                </div>

                {{-- ─── ADD RATE CARD ───────────────────────────────────────── --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Add New Rate</h2>
                    </div>
                    <form action="{{ route('admin.companies.rates.add', $company) }}" method="POST" class="p-5">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Vehicle Type</label>
                                <select name="vehicle_type" required
                                        class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                                    <option value="private">Private</option>
                                    <option value="psv">PSV</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Category</label>
                                <select name="category" required
                                        class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                                    <option value="">Select category</option>
                                    <option value="Premium Rate">Premium Rate</option>
                                    <option value="Cover Limits">Cover Limits</option>
                                    <option value="Third Party Limits">Third Party Limits</option>
                                    <option value="Excess">Excess</option>
                                    <option value="Additional Cover">Additional Cover</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Other Category</label>
                                <input type="text" name="category_other" placeholder="Or type custom"
                                       class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Item Name</label>
                                <input type="text" name="item_name" placeholder="e.g. Windscreen / Glass" required
                                       class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Value Type</label>
                                <select name="value_type" required
                                        class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                                    <option value="fixed">Fixed Amount</option>
                                    <option value="percentage">Percentage</option>
                                    <option value="percentage_min">Percentage with Minimum</option>
                                    <option value="text">Text</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Value (KES)</label>
                                <input type="number" name="value" step="0.01"
                                       class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Percentage (%)</label>
                                <input type="number" name="percentage" step="0.01" max="100"
                                       class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Min Value</label>
                                <input type="number" name="min_value" step="0.01"
                                       class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Min Premium</label>
                                <input type="number" name="min_premium" step="0.01"
                                       class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div class="md:col-span-4">
                                <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Notes / Description</label>
                                <input type="text" name="notes" placeholder="e.g. Kes. 50,000/- min, Covered, etc."
                                       class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div class="md:col-span-4 flex items-center gap-2">
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition">Add Rate</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ─── TAB: IMPORT RATES (Single Company) ─────────────────────── --}}
        <div x-show="tab === 'import'">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Import Rates for {{ $company->name }}</h2>
                </div>
                <div class="p-5">
                    <form action="{{ route('admin.companies.rates.import', $company) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Upload File (XLSX, CSV, or JSON)</label>
                            <input type="file" name="file" accept=".xlsx,.csv,.json" required
                                   class="block w-full text-xs text-gray-500 dark:text-gray-400 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-micblue file:text-white hover:file:bg-blue-600 transition cursor-pointer">
                            <p class="mt-1 text-[10px] text-gray-400">Max 5MB. First row skipped as header.</p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-4">
                            <h4 class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Expected Excel/CSV Format</h4>
                            <div class="overflow-x-auto">
                                <table class="w-full text-[10px]">
                                    <thead>
                                        <tr class="bg-gray-200 dark:bg-gray-600">
                                            <th class="px-2 py-1 text-left">Category</th>
                                            <th class="px-2 py-1 text-left">Item Name</th>
                                            <th class="px-2 py-1 text-left">Value Type</th>
                                            <th class="px-2 py-1 text-right">Value</th>
                                            <th class="px-2 py-1 text-right">%</th>
                                            <th class="px-2 py-1 text-right">Min Value</th>
                                            <th class="px-2 py-1 text-right">Min Premium</th>
                                            <th class="px-2 py-1 text-left">Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="border-b border-gray-200 dark:border-gray-700">
                                            <td class="px-2 py-1">Cover Limits</td>
                                            <td class="px-2 py-1">Windscreen / Glass</td>
                                            <td class="px-2 py-1">fixed</td>
                                            <td class="px-2 py-1 text-right">30000</td>
                                            <td class="px-2 py-1 text-right"></td>
                                            <td class="px-2 py-1 text-right"></td>
                                            <td class="px-2 py-1 text-right"></td>
                                            <td class="px-2 py-1">Kes. 30,000/-</td>
                                        </tr>
                                        <tr class="border-b border-gray-200 dark:border-gray-700">
                                            <td class="px-2 py-1">Premium Rate</td>
                                            <td class="px-2 py-1">Premium Rate</td>
                                            <td class="px-2 py-1">percentage_min</td>
                                            <td class="px-2 py-1 text-right"></td>
                                            <td class="px-2 py-1 text-right">5.5</td>
                                            <td class="px-2 py-1 text-right"></td>
                                            <td class="px-2 py-1 text-right">37500</td>
                                            <td class="px-2 py-1">5.5% Min Premium- Kes. 37,500/-</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-4">
                            <h4 class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">JSON Format Example</h4>
                            <pre class="text-[10px] text-gray-600 dark:text-gray-400 overflow-x-auto">[
  {
    "category": "Cover Limits",
    "item_name": "Windscreen / Glass",
    "value_type": "fixed",
    "value": 30000,
    "percentage": null,
    "min_value": null,
    "min_premium": null,
    "notes": "Kes. 30,000/-"
  }
]</pre>
                        </div>

                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-emerald-500 hover:bg-emerald-400 text-white rounded-lg transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            Import Rates
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ─── TAB: BULK IMPORT ───────────────────────────────────────── --}}
        <div x-show="tab === 'bulk'">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Bulk Import Companies with Rates</h2>
                </div>
                <div class="p-5">
                    <form action="{{ route('admin.companies.import.bulk') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Upload Excel File (XLSX or CSV)</label>
                            <input type="file" name="file" accept=".xlsx,.csv" required
                                   class="block w-full text-xs text-gray-500 dark:text-gray-400 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-micblue file:text-white hover:file:bg-blue-600 transition cursor-pointer">
                            <p class="mt-1 text-[10px] text-gray-400">Max 10MB.</p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-4">
                            <h4 class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Expected Format</h4>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 mb-2">First row: underwriter names as column headers (starting column B). First column: rate item labels. Cells: rate values.</p>
                            <div class="overflow-x-auto">
                                <table class="w-full text-[10px]">
                                    <thead>
                                        <tr class="bg-gray-200 dark:bg-gray-600">
                                            <th class="px-2 py-1 text-left">Item</th>
                                            <th class="px-2 py-1 text-left">SANLAM ALLIANZ</th>
                                            <th class="px-2 py-1 text-left">GA</th>
                                            <th class="px-2 py-1 text-left">DEFINITE ASSURANCE</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="border-b border-gray-200 dark:border-gray-700">
                                            <td class="px-2 py-1 font-medium">Windscreen/ Glass</td>
                                            <td class="px-2 py-1">Kes.50,000/-</td>
                                            <td class="px-2 py-1">Kes.75,000/-</td>
                                            <td class="px-2 py-1">Kes. 30,000/-</td>
                                        </tr>
                                        <tr class="border-b border-gray-200 dark:border-gray-700">
                                            <td class="px-2 py-1 font-medium">Premium Rate</td>
                                            <td class="px-2 py-1">6.5% Min Premium- Kes. 75,000 Inclusive of PVT</td>
                                            <td class="px-2 py-1">7.5% Min Premium- Kes. 35,000, basic</td>
                                            <td class="px-2 py-1">5.5% Min Premium- Kes. 37,500/-</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p class="mt-2 text-[10px] text-gray-500 dark:text-gray-400">Underwriters are created if they don't exist. Rate categories are auto-detected from item names.</p>
                        </div>

                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-violet-500 hover:bg-violet-400 text-white rounded-lg transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            Bulk Import
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const catSelect = document.querySelector('select[name="category"]');
    const catOther = document.querySelector('input[name="category_other"]');
    if (catSelect && catOther) {
        catSelect.addEventListener('change', function() {
            if (this.value) catOther.value = '';
        });
        catOther.addEventListener('input', function() {
            if (this.value) catSelect.value = '';
        });
    }
});
</script>
@endpush
</x-app-layout>
