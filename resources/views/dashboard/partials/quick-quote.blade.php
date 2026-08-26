@php
    $_qqColors = ['#1756c8', '#7c3aed', '#0f7a65', '#d4860e', '#c0392b', '#1E40AF', '#F59E0B', '#10B981', '#8B5CF6', '#EF4444'];
    $_qqIdx = 0;
    $_qqData = collect($quoteCompanies ?? collect())->map(function ($c) use ($_qqColors, &$_qqIdx) {
        return [
            'slug' => $c->slug,
            'name' => $c->name,
            'color' => $_qqColors[$_qqIdx++ % count($_qqColors)],
            'prefix' => $c->getInitials(),
        ];
    })->values();
@endphp

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Quick Quote</p>
                <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Generate a New Quotation</h2>
            </div>
            <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700 rounded-lg p-0.5">
                <button type="button" onclick="setDashVehicleType('private')" id="dash-toggle-private" class="px-3 py-1.5 text-[10px] font-semibold rounded-md bg-white dark:bg-gray-600 text-gray-800 dark:text-white shadow-sm">Private</button>
                <button type="button" onclick="setDashVehicleType('psv')" id="dash-toggle-psv" class="px-3 py-1.5 text-[10px] font-semibold rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">PSV</button>
            </div>
        </div>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">First Name <span class="text-rose-500">*</span></label>
                <input type="text" id="dash-f-first" value="{{ auth()->user()->first_name ?? '' }}" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none" placeholder="e.g. John">
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Last Name <span class="text-rose-500">*</span></label>
                <input type="text" id="dash-f-last" value="{{ auth()->user()->last_name ?? '' }}" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none" placeholder="e.g. Doe">
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Phone <span class="text-rose-500">*</span></label>
                <input type="tel" id="dash-f-phone" value="{{ auth()->user()->mobile ?? '' }}" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none" placeholder="07 XX XXX XXX">
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Email <span class="text-rose-500">*</span></label>
                <input type="email" id="dash-f-email" value="{{ auth()->user()->email ?? '' }}" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none" placeholder="email@example.com">
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Registration No <span class="text-rose-500">*</span></label>
                <input type="text" id="dash-f-reg" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none" placeholder="e.g. KDH 724V">
            </div>
            <div class="relative">
                <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Make <span class="text-rose-500">*</span></label>
                <input type="text" id="dash-f-make" autocomplete="off" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none" placeholder="e.g. Toyota">
                <div id="dash-f-make-dropdown" class="dash-search-dropdown absolute z-20 mt-1 w-full max-h-52 overflow-y-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg hidden"></div>
            </div>
            <div class="relative">
                <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Model <span class="text-rose-500">*</span></label>
                <input type="text" id="dash-f-model" autocomplete="off" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none" placeholder="e.g. Corolla">
                <div id="dash-f-model-dropdown" class="dash-search-dropdown absolute z-20 mt-1 w-full max-h-52 overflow-y-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg hidden"></div>
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Year <span class="text-rose-500">*</span></label>
                <select id="dash-f-year" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">Select year...</option>
                    @for($y = date('Y'); $y >= 1990; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Value (KES) <span class="text-rose-500">*</span></label>
                <input type="text" id="dash-f-value" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none" placeholder="e.g. 800,000">
            </div>
            <div id="dash-f-pax-wrap" style="display:none">
                <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Passengers <span class="text-rose-500">*</span></label>
                <input type="number" id="dash-f-pax" value="1" min="1" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
        </div>

        <div id="dash-premium-preview" class="mt-4" style="display:none">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3" id="dash-company-cards"></div>
        </div>

        <div class="flex justify-end gap-2 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
            <a href="{{ route('quotations.create') }}" class="px-4 py-2 text-xs font-medium border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">Full Form →</a>
            <button type="button" onclick="dashGenerateQuote()" class="px-5 py-2 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Generate Quote</button>
        </div>
    </div>
</div>

<script>
window.MIC_COMPANIES = @json($_qqData);
window.MIC_QUOTE_COMPANIES = @json(($quoteCompanies ?? collect())->values());
window.MIC_QUOTE_COMPANY_DATA_PRIVATE = @json($quoteCompanyDataPrivate ?? []);
window.MIC_QUOTE_COMPANY_DATA_PSV = @json($quoteCompanyDataPsv ?? []);
</script>
