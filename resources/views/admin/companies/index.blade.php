<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    {{-- ─── HERO ─────────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Underwriter Directory</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Underwriters</h1>
                <p class="mt-0.5 text-xs text-slate-400">Manage partner underwriters and the rates that power your quotations.</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.companies.create') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Add Underwriter
                </a>
                <button onclick="document.getElementById('bulkImportModal').classList.remove('hidden')"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-violet-500 hover:bg-violet-400 text-white rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Bulk Import
                </button>
            </div>
        </div>

        {{-- KPI row --}}
        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="kpi-card p-4">
                <div class="accent-bar"></div>
                <p class="kpi-eyebrow">Companies</p>
                <p class="kpi-value mt-1">{{ $companies->count() }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Registered insurers</span>
            </div>

            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#34d399,#6ee7b7)"></div>
                <p class="kpi-eyebrow">Active</p>
                <p class="kpi-value mt-1">{{ $companies->where('is_active', true)->count() }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Available for quotations</span>
            </div>

            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#fb7185,#fda4af)"></div>
                <p class="kpi-eyebrow">Inactive</p>
                <p class="kpi-value mt-1">{{ $companies->where('is_active', false)->count() }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Currently disabled</span>
            </div>

            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#818cf8,#a5b4fc)"></div>
                <p class="kpi-eyebrow">Total Quotations</p>
                <p class="kpi-value mt-1">{{ number_format($companies->sum('quotations_count')) }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Generated quotes</span>
            </div>
        </div>
    </div>

    @if($companies->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 py-16 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-400">No insurer companies yet.</p>
            <a href="{{ route('admin.companies.create') }}" class="inline-block mt-3 text-xs font-medium text-blue-600 hover:underline">Add your first company</a>
        </div>
    @else

        {{-- Quotations by insurer --}}
        @php $maxQuotations = max($companies->max('quotations_count'), 1); @endphp
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Quotations by Underwriter</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">Volume of quotations generated per underwriter</p>
            </div>
            <div class="p-5 space-y-3">
                @foreach($companies as $company)
                    <div class="flex items-center gap-3">
                        <span class="w-32 truncate text-xs text-gray-700 dark:text-gray-300">{{ $company->name }}</span>
                        <div class="flex-1 h-2 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                            <div class="h-full rounded-full {{ $company->is_active ? 'bg-blue-500' : 'bg-gray-300 dark:bg-gray-600' }}"
                                 style="width: {{ max(($company->quotations_count / $maxQuotations) * 100, 3) }}%"></div>
                        </div>
                        <span class="w-10 text-right text-xs font-semibold text-gray-900 dark:text-white">{{ $company->quotations_count }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Cards grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($companies as $company)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col">
                    <div class="p-5 flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            @php $cl = $company->logoUrl(); @endphp
                            @if($cl)
                                <img src="{{ $cl }}" alt="{{ $company->name }} logo" class="w-10 h-10 rounded-xl object-cover border border-gray-100 dark:border-gray-700 shrink-0">
                            @else
                                <div class="w-10 h-10 rounded-xl bg-micblue flex items-center justify-center text-white font-bold text-sm shrink-0">
                                    {{ substr($company->name, 0, 1) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <h3 class="text-xs font-semibold text-gray-900 dark:text-white truncate">{{ $company->name }}</h3>
                                <p class="text-[10px] text-gray-500">{{ $company->quotations_count }} quotations</p>
                                <span class="inline-block mt-0.5 px-1.5 py-0.5 rounded text-[9px] font-medium uppercase tracking-wider {{ $company->company_type === 'private' ? 'bg-sky-50 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400' : ($company->company_type === 'psv' ? 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-violet-50 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400') }}">
                                    {{ $company->company_type ?? 'both' }}
                                </span>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium shrink-0 {{ $company->is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400' }}">
                            <span class="w-1 h-1 rounded-full {{ $company->is_active ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                            {{ $company->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <div class="mt-auto grid grid-cols-3 divide-x divide-gray-100 dark:divide-gray-700 border-t border-gray-100 dark:border-gray-700">
                        <a href="{{ route('admin.companies.rates', $company) }}" class="px-3 py-2.5 text-center text-[10px] font-medium text-blue-700 dark:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20">Rates</a>
                        <a href="{{ route('admin.companies.edit', $company) }}" class="px-3 py-2.5 text-center text-[10px] font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50">Edit</a>
                        <form action="{{ route('admin.companies.toggle-status', $company) }}" method="POST" style="display:contents">
                            @csrf @method('PATCH')
                            <button type="submit" class="px-3 py-2.5 text-center text-[10px] font-medium w-full {{ $company->is_active ? 'text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/10' : 'text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/10' }}">
                                {{ $company->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Rate overview table --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Rate Overview</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">Manage detailed rate bands for each insurer</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/40">
                            <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Company</th>
                            <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Status</th>
                            <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Quotations</th>
                            <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Manage</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($companies as $company)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                <td class="px-5 py-2.5">
                                    <div class="flex items-center gap-2.5">
                                        @php $cl = $company->logoUrl(); @endphp
                                        @if($cl)
                                            <img src="{{ $cl }}" alt="{{ $company->name }} logo" class="w-7 h-7 rounded-lg object-cover">
                                        @else
                                            <div class="w-7 h-7 rounded-lg bg-micblue flex items-center justify-center text-white font-semibold text-[10px]">{{ substr($company->name, 0, 1) }}</div>
                                        @endif
                                        <span class="font-medium text-gray-800 dark:text-white">{{ $company->name }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-2.5">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-medium {{ $company->is_active ? 'text-emerald-600' : 'text-rose-600' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $company->is_active ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                        {{ $company->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-5 py-2.5 text-xs text-gray-500 dark:text-gray-400">{{ $company->quotations_count }}</td>
                                <td class="px-5 py-2.5 text-right">
                                    <a href="{{ route('admin.companies.rates', $company) }}" class="text-[10px] font-medium text-blue-600 hover:text-blue-800">Manage rates &rarr;</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    @endif
</div>

{{-- ─── BULK IMPORT MODAL ─────────────────────────────────────────────── --}}
<div id="bulkImportModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/50" x-cloak>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <h2 class="text-xs font-semibold text-gray-900 dark:text-white">Bulk Import Underwriters with Rates</h2>
            </div>
            <button onclick="document.getElementById('bulkImportModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6">
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
                    <p class="text-[10px] text-gray-500 dark:text-gray-400 mb-2">First row: underwriter names. First column: rate item labels. Cells: rate values.</p>
                    <p class="text-[10px] text-gray-500 dark:text-gray-400">Underwriters are auto-created. Categories are auto-detected from item names.</p>
                </div>

                <div class="flex items-center gap-2 justify-end">
                    <button type="button" onclick="document.getElementById('bulkImportModal').classList.add('hidden')"
                            class="px-4 py-2 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">Cancel</button>
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-violet-500 hover:bg-violet-400 text-white rounded-lg transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</x-app-layout>
