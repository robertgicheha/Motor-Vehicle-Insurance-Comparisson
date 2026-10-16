<x-app-layout>
<div class="max-w-5xl mx-auto space-y-5 pt-10 fade-in font-montserrat" x-data="{ expanded: true }">

    @if(!empty($showSuccess))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition.opacity
         class="rounded-2xl bg-emerald-500/10 border border-emerald-500/30 px-6 py-5 shadow-sm flex items-center gap-4">
        <div class="w-10 h-10 rounded-full bg-emerald-500/20 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div>
            <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400">Your quotation was successfully generated</p>
            <p class="text-xs text-emerald-500/70 mt-0.5">Your quotation has been saved and is now available in your account.</p>
        </div>
    </div>
    @endif

    {{-- ─── HERO ─────────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-300">Quotation / <span id="hero-company-name" x-text="selectedName">{{ $selectedCompany->name }}</span></p>
                <div class="flex items-center gap-3 mt-0.5">
                    <h1 class="text-2xl font-bold text-white tracking-tight" style="font-size:26px">Motor Insurance Quotation</h1>
                    <img id="hero-company-logo" src="" alt="" class="hidden h-9 w-auto" onerror="this.style.display='none'">
                    <button id="copy-quote-btn" onclick="copyQuoteNumber()"
                            class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-semibold bg-white/20 text-white rounded-lg hover:bg-white/30 transition"
                            style="border:1px solid rgba(255,255,255,0.25)">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                        Copy
                    </button>
                </div>
                <p class="mt-1 text-xs text-blue-200">
                    Reference:
                    <span id="hero-quote-num" class="font-semibold text-white">—</span>
                    &middot; Generated {{ now()->format('d M Y, h:i A') }}
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('motor.quotation') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold border border-white/20 text-white/80 hover:bg-white/10 rounded-lg transition">
                    Compare All Insurers
                </a>
                <button class="action-btn action-btn-download inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition"
                        onclick="actionDownload()">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download PDF
                </button>
            </div>
        </div>

        {{-- Vehicle summary bar --}}
        <div class="mt-4 flex flex-wrap items-center gap-4 text-xs text-white/70" id="vehicle-bar">
            <span class="flex items-center gap-1.5" id="vb-make"><span x-show="false"></span>—</span>
            <span class="flex items-center gap-1.5" id="vb-reg">—</span>
            <span class="flex items-center gap-1.5" id="vb-value">—</span>
            <span class="flex items-center gap-1.5" id="vb-company">—</span>
        </div>
    </div>

    {{-- ─── EXPANDED DETAILS (collapsible) ───────────────────────────────── --}}
    <div x-show="expanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">

    {{-- ─── CUSTOMER & VEHICLE SUMMARY ──────────────────────────────────── --}}
    <!-- <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
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
                <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Insurer</span>
                <span class="block font-medium text-gray-800 dark:text-white mt-1" id="s-company">—</span>
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
    </div> -->

    {{-- ─── QUOTATION BREAKDOWN (selected company) ──────────────────────── --}}
    <div id="single-breakdown-wrap" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Financials</p>
            <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Quotation Summary — <span id="breakdown-company-label">—</span></h2>
        </div>
        <div class="p-6 overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="text-gray-400 uppercase tracking-wider text-[10px]">
                        <th class="text-left font-semibold pb-2">Description</th>
                        <th class="text-left font-semibold pb-2">Rate / Notes</th>
                        <th class="text-right font-semibold pb-2">Amount (KES)</th>
                    </tr>
                </thead>
                <tbody id="breakdown-rows" class="divide-y divide-gray-100 dark:divide-gray-700"></tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-200 dark:border-gray-600">
                        <td colspan="2" class="pt-3 font-bold text-gray-800 dark:text-white">Total Premium (incl. taxes)</td>
                        <td id="breakdown-total" class="pt-3 text-right font-extrabold text-lg text-blue-600 dark:text-blue-400">—</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ─── BENEFITS & COVER DETAILS ──────────────────────────────────────── --}}
    @if(!empty($rateCategories) && collect($rateCategories)->flatten(1)->isNotEmpty())
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Detailed</p>
            <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Benefits &amp; Cover Details</h2>
        </div>
        <div class="p-6 space-y-6">
            @foreach($rateCategories as $categoryLabel => $items)
                @if($items->isNotEmpty())
                <div>
                    <h4 class="text-[10px] font-bold uppercase tracking-widest text-blue-600 dark:text-blue-400 mb-2">{{ $categoryLabel }}</h4>
                    <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-700">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="text-gray-400 uppercase tracking-wider text-[10px]">
                                    <th class="text-left font-semibold pb-2 px-4 pt-3">Description</th>
                                    <th class="text-left font-semibold pb-2 px-4 pt-3">Rate / Notes</th>
                                    <th class="text-right font-semibold pb-2 px-4 pt-3">Amount (KES)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($items as $item)
                                <tr class="hover:bg-blue-50 dark:hover:bg-blue-900/20">
                                    <td class="px-4 py-2.5 text-gray-600 dark:text-gray-300 w-1/3">{{ $item->item_name }}</td>
                                    <td class="px-4 py-2.5 text-gray-400 text-[11px]">{{ $item->notes ?: '—' }}</td>
                                    @php
                                        // Decimal-cast columns (value/percentage) come back as numeric
                                        // strings (e.g. "0.00"), which PHP treats as truthy — compare
                                        // numerically so a real 0 falls through to the Notes wording
                                        // (No Cover, Covered, etc.) instead of showing "KES 0".
                                        $itemValue = (float) ($item->value ?? 0);
                                        $itemPercentage = (float) ($item->percentage ?? 0);
                                    @endphp
                                    <td class="px-4 py-2.5 text-right font-semibold text-gray-800 dark:text-white tabular-nums">
                                        @if($item->item_name === 'Max Age New Business' && $itemValue > 0)
                                            {{ number_format($itemValue, 0) }} years
                                        @elseif($itemPercentage > 0)
                                            @if(($carValuation ?? 0) > 0)
                                                KES {{ number_format((float) ($item->computeAmountFor((float) $carValuation) ?? 0), 2) }}
                                                <span class="text-[10px] text-gray-400 font-normal">({{ $item->notes ?: (\App\Models\Rate::fmtPercent($item->percentage) . '% Min KES ' . number_format((float) ($item->min_value ?? 0), 0)) }})</span>
                                            @else
                                                {{ \App\Models\Rate::fmtPercent($item->percentage) }}%
                                            @endif
                                        @elseif($itemValue > 0)
                                            KES {{ number_format($itemValue, 0) }}{{ (stripos($item->item_name, 'novice') !== false || stripos($item->item_name, 'young driver') !== false) ? ' additional' : '' }}
                                        @elseif($item->notes)
                                            {{ $item->notes }}
                                        @elseif(stripos($item->item_name, 'tracking device') !== false)
                                            No Cover
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- ─── DOWNLOAD CARD ──────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Actions</p>
            <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Download Quote</h2>
        </div>
        <div id="dl-card-single" class="p-6"></div>
    </div>

    {{-- ─── ACTION BAR ──────────────────────────────────────────────────── --}}
    <div class="action-bar bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center gap-3" id="action-bar">
        <span class="action-bar-label text-[11px] font-semibold uppercase tracking-widest text-gray-400 flex-1">Quotation Actions</span>
        <button class="action-btn action-btn-download inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition" onclick="actionDownload()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Download PDF
        </button>
        <button class="action-btn action-btn-print inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-semibold bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition" onclick="actionPrint()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Print
        </button>
        <button class="action-btn action-btn-preview inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-semibold bg-purple-600 hover:bg-purple-500 text-white rounded-lg transition" id="btn-preview" onclick="actionTogglePreview()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            Preview
        </button>
    </div>

    </div>
    {{-- /END EXPANDED DETAILS --}}

    {{-- ─── COLLAPSED SUMMARY ROW (compact) ────────────────────────────── --}}
    <div x-show="!expanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
         class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-3.5 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs">
            <span class="font-semibold text-blue-600 dark:text-blue-400" id="compact-quote">—</span>
            <span class="text-gray-800 dark:text-white font-medium" id="compact-vehicle">—</span>
            <span class="text-gray-400" id="compact-company">—</span>
            <button @click="expanded = true" class="inline-flex items-center gap-1 px-3 py-1.5 text-[10px] font-semibold bg-blue-500 text-white rounded-lg hover:bg-blue-400 transition ml-auto">Expand</button>
        </div>
    </div>

    {{-- ─── PDF PREVIEW (real, streamed from the server) ─────────────────── --}}
    <div class="pdf-preview" id="pdf-preview" style="display:none">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <p class="text-xs font-bold text-gray-800 dark:text-white">Quotation Preview</p>
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
window.selectedCompanySlug = {{ json_encode($selectedCompany->slug) }};
window.MIC_QUOTATION_SLUG  = {{ json_encode($selectedCompany->slug) }};
</script>

@php
    $_quotationDbData = $userQuotation ? [
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

@if(auth()->guest())
<script>
function openAuthModal(tab) {
  if (!sessionStorage.getItem('auth_redirect')) {
    sessionStorage.setItem('auth_redirect', window.location.href);
  }
  document.getElementById('authModal').style.display = 'flex';
  document.body.style.overflow = 'hidden';
  if (tab && window.Alpine) {
    var el = document.getElementById('authModal');
    if (el) { try { window.Alpine.$data(el).tab = tab; } catch(e) {} }
  }
}

function closeAuthModal() {
  document.getElementById('authModal').style.display = 'none';
  document.body.style.overflow = '';
}

(function(){
  if (window.MIC_GUEST) {
    var banner = document.createElement('div');
    banner.id = 'guest-save-banner';
    banner.style.cssText = 'position:fixed;bottom:0;left:0;right:0;background:linear-gradient(135deg,#003D7A,#0066B3);color:white;padding:14px 24px;z-index:999;display:flex;align-items:center;justify-content:center;gap:16px;flex-wrap:wrap;font-size:14px;box-shadow:0 -4px 20px rgba(0,0,0,0.15)';
    banner.innerHTML = 'You are viewing a guest quotation. <strong>Sign in</strong> to save and manage your quotes. ' +
      '<button onclick="guestSaveQuote()" style="background:white;color:#003D7A;border:none;padding:8px 20px;border-radius:6px;font-weight:700;font-size:13px;cursor:pointer">Sign in to Save</button>' +
      '<button onclick="this.parentElement.remove()" style="background:transparent;color:rgba(255,255,255,.7);border:1px solid rgba(255,255,255,.3);padding:8px 16px;border-radius:6px;font-size:12px;cursor:pointer">Continue as Guest</button>';
    document.body.appendChild(banner);
  }

  window.guestSaveQuote = function() {
    var banner = document.getElementById('guest-save-banner');
    if (banner) banner.remove();

    var slug = window.MIC_QUOTATION_SLUG;
    if (!slug) {
      var companies = window.quotationData || [];
      slug = companies.length > 0 ? companies[0].slug : null;
    }
    if (slug) {
      sessionStorage.setItem('pending_quote_company', slug);
      try {
        var raw = sessionStorage.getItem('micQuoteData');
        if (!raw) {
          var fd = getFormData();
          sessionStorage.setItem('micQuoteData', JSON.stringify({
            email: fd.email || '',
            reg: fd.reg || '',
            chassis: fd.chassis || '',
            make: fd.make || '',
            model: fd.model || '',
            body: fd.body || '',
            year: fd.year || '',
            color: fd.color || '',
            value: Number(fd.value) || 0,
            mode: slug,
          }));
        }
      } catch(e) {}
    }
    sessionStorage.setItem('auth_redirect', '/dashboard?pending_quote=1');
    openAuthModal();
  };
})();
</script>
@endif

<script src="{{ asset('js/quotation.js') }}"></script>
@endpush

</x-app-layout>
