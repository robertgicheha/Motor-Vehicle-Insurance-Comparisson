<x-app-layout>
<div class="max-w-5xl mx-auto space-y-5 pt-10 fade-in font-montserrat" x-data="{ expanded: true }">

    {{-- ─── HERO ─────────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-300">Quotation / All Insurers</p>
                <h1 class="text-2xl font-bold text-white tracking-tight mt-0.5" style="font-size:26px">Compare All Insurers</h1>
                <p class="mt-1 text-xs text-blue-200">Side-by-side premium and benefits comparison across every underwriter that covers this vehicle.</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" onclick="compareAllPdf('preview')" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Preview Combined PDF
                </button>
            </div>
        </div>

        {{-- Vehicle summary bar --}}
        <div class="mt-4 flex flex-wrap items-center gap-4 text-xs text-white/70" id="vehicle-bar">
            <span class="flex items-center gap-1.5" id="vb-make"><span x-show="false"></span>—</span>
            <span class="flex items-center gap-1.5" id="vb-reg">—</span>
            <span class="flex items-center gap-1.5" id="vb-value">—</span>
        </div>
    </div>

    {{-- ─── EXPANDED DETAILS (collapsible) ───────────────────────────────── --}}
    <div x-show="expanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">

    {{-- ─── CUSTOMER & VEHICLE SUMMARY ──────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Summary</p>
            <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Customer &amp; Vehicle</h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5 p-6 text-xs">
            <div>
                <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Full Name</span>
                <span class="block font-medium text-gray-800 dark:text-white mt-1" id="s-name">—</span>
            </div>
            <div>
                <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Phone</span>
                <span class="block font-medium text-gray-800 dark:text-white mt-1" id="s-phone">—</span>
            </div>
            <div>
                <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Email</span>
                <span class="block font-medium text-gray-800 dark:text-white mt-1" id="s-email">—</span>
            </div>
            <div>
                <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Registration</span>
                <span class="block font-medium text-gray-800 dark:text-white mt-1" id="s-reg">—</span>
            </div>
            <div>
                <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Make / Model</span>
                <span class="block font-medium text-gray-800 dark:text-white mt-1" id="s-make">—</span>
            </div>
            <div>
                <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Year</span>
                <span class="block font-medium text-gray-800 dark:text-white mt-1" id="s-year">—</span>
            </div>
            <div>
                <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Chassis No.</span>
                <span class="block font-medium text-gray-800 dark:text-white mt-1" id="s-chassis">—</span>
            </div>
            <div>
                <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Estimated Value</span>
                <span class="block font-medium text-gray-800 dark:text-white mt-1" id="s-value">—</span>
            </div>
        </div>
    </div>

    {{-- ─── DETAILED COMPARE TABLE ──────────────────────────────────────── --}}
    <div id="compare-all-wrap" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Detailed</p>
            <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Premium Comparison — All Underwriters</h2>
        </div>
        <div class="p-6 overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="text-gray-400 uppercase tracking-wider text-[10px]">
                        <th class="text-left font-semibold pb-2">Description</th>
                        <th id="cmp-head-0" class="text-right font-semibold pb-2">Insurer 1</th>
                        <th id="cmp-head-1" class="text-right font-semibold pb-2">Insurer 2</th>
                        <th id="cmp-head-2" class="text-right font-semibold pb-2">Insurer 3</th>
                    </tr>
                </thead>
                <tbody id="compare-rows" class="divide-y divide-gray-100 dark:divide-gray-700"></tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-200 dark:border-gray-600">
                        <td class="pt-3 font-bold text-gray-800 dark:text-white">Total Premium (incl. taxes)</td>
                        <td id="cmp-total-0" class="pt-3 text-right font-extrabold text-blue-600 dark:text-blue-400">—</td>
                        <td id="cmp-total-1" class="pt-3 text-right font-extrabold text-blue-600 dark:text-blue-400">—</td>
                        <td id="cmp-total-2" class="pt-3 text-right font-extrabold text-blue-600 dark:text-blue-400">—</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ─── BENEFITS & COVER COMPARISON ───────────────────────────────────── --}}
    @if(!empty($benefitsComparison))
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Detailed</p>
            <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Benefits &amp; Cover Comparison</h2>
        </div>
        <div class="p-6 space-y-6">
            @foreach($benefitsComparison as $categoryLabel => $section)
            <div>
                <h4 class="text-[10px] font-bold uppercase tracking-widest text-blue-600 dark:text-blue-400 mb-2">{{ $categoryLabel }}</h4>
                <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-700">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="text-gray-400 uppercase tracking-wider text-[10px]">
                                <th class="text-left font-semibold pb-2 px-4 pt-3">Benefit</th>
                                @foreach($section['companies'] as $company)
                                <th class="text-right font-semibold pb-2 px-4 pt-3">
                                    <div class="flex flex-col items-end gap-1">
                                        @if($company['logo'])
                                        <img src="{{ $company['logo'] }}" alt="{{ $company['name'] }}" class="h-5 max-w-[80px] object-contain" onerror="this.style.display='none'">
                                        @endif
                                        <span>{{ $company['name'] }}</span>
                                    </div>
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($section['rows'] as $row)
                            <tr class="hover:bg-blue-50 dark:hover:bg-blue-900/20">
                                <td class="px-4 py-2.5 text-gray-600 dark:text-gray-300 w-1/3">{{ $row['item'] }}</td>
                                @foreach($section['companies'] as $company)
                                <td class="px-4 py-2.5 text-right font-semibold text-gray-800 dark:text-white tabular-nums">{!! $row['values'][$company['slug']] !!}</td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ─── DOWNLOAD CARDS ──────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Actions</p>
            <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5" id="dl-cards-label">Download Quote</h2>
        </div>
        <div id="dl-cards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 p-6"></div>
    </div>

    {{-- ─── ACTION BAR ──────────────────────────────────────────────────── --}}
    <div class="action-bar visible bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center gap-3" id="action-bar">
        <span class="action-bar-label text-[11px] font-semibold uppercase tracking-widest text-gray-400 flex-1">Compare All — Combined Document</span>
        <button type="button" class="action-btn action-btn-download inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition" onclick="compareAllPdf('download')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Download All (PDF)
        </button>
        <button type="button" class="action-btn action-btn-print inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-semibold bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition" onclick="compareAllPdf('print')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Print All
        </button>
        <button type="button" class="action-btn action-btn-preview inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-semibold bg-purple-600 hover:bg-purple-500 text-white rounded-lg transition" id="btn-preview" onclick="actionTogglePreview()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            Preview All
        </button>
        <button type="button" class="action-btn inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg transition" onclick="compareAllPdf('email')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22 6 12 13 2 6"/></svg>
            Email All
        </button>
    </div>

    </div>
    {{-- /END EXPANDED DETAILS --}}

    {{-- ─── COLLAPSED SUMMARY ROW (compact) ────────────────────────────── --}}
    <div x-show="!expanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
         class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-3.5 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs">
            <span class="text-gray-800 dark:text-white font-medium" id="compact-vehicle">—</span>
            <span class="text-gray-400" id="compact-company">Comparing all insurers</span>
            <button @click="expanded = true" class="inline-flex items-center gap-1 px-3 py-1.5 text-[10px] font-semibold bg-blue-500 text-white rounded-lg hover:bg-blue-400 transition ml-auto">Expand</button>
        </div>
    </div>

    {{-- ─── PDF PREVIEW (real, streamed from the server) ─────────────────── --}}
    <div class="pdf-preview" id="pdf-preview" style="display:none">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <p class="text-xs font-bold text-gray-800 dark:text-white">Combined Quotation Preview</p>
                <button onclick="actionTogglePreview()" class="text-gray-400 hover:text-gray-600 text-lg leading-none">&times;</button>
            </div>
            <iframe id="pdf-preview-frame" name="pdf-preview-frame" style="width:100%;height:80vh;border:0;display:block"></iframe>
        </div>
    </div>

</div>

{{-- Toast container used by quotation.js --}}
<div class="toast-container" id="toast-container"></div>

@push('scripts')
<script>
window.quotationData    = @json($companyData);
window.quotationTaxRate = {{ $taxRate }};
window.quotationVehicleType = '{{ $vehicleType ?? 'psv' }}';
window.quotationNumPassengers = {{ $numPassengers ?? 1 }};
window.quotationCompanies = @json($companies->map(fn($c) => ['slug' => $c->slug, 'name' => $c->name, 'logo' => $c->logoUrl()]));
window.hardcodedLogos  = @json($hardcodedLogos ?? []);
window.compareAllLogoUrl = @json($__compareAllLogoUrl ?? null);
window.selectedCompanySlug = null;
window.MIC_QUOTATION_SLUG  = null;
</script>

@php
    $_quotationDbData = $userQuotation ? [
        'firstName' => $userQuotation->customer_first_name ?? '',
        'lastName'  => $userQuotation->customer_last_name ?? '',
        'salutation' => $userQuotation->customer_salutation ?? '',
        'phone'     => $userQuotation->customer_phone ?? '',
        'email'     => $userQuotation->customer_email ?? '',
        'reg'       => $userQuotation->car_registration ?? '',
        'chassis'   => $userQuotation->chassis_number ?? '',
        'make'      => $userQuotation->car_make ?? '',
        'model'     => $userQuotation->car_model ?? '',
        'body'      => $userQuotation->body_type ?? '',
        'year'      => $userQuotation->car_year ?? '',
        'color'     => $userQuotation->color ?? '',
        'value'     => (float)($userQuotation->car_valuation ?? 0),
        'quoteNum'  => $userQuotation->quotation_number ?? '',
        'numPassengers' => (int)($userQuotation->num_passengers ?? 1),
        'vehicleType'   => $userQuotation->vehicle_type ?? 'private',
    ] : null;
@endphp
<script>
window.quotationDbData = @json($_quotationDbData);
window.MIC_AUTH  = @json(auth()->check());
window.MIC_GUEST = @json(auth()->guest());
</script>

<script src="{{ asset('js/quotation.js') }}"></script>
@endpush

</x-app-layout>
