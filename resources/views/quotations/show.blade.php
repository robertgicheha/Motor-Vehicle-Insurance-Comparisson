<x-app-layout>
<div class="max-w-7xl mx-auto space-y-5 pt-10 fade-in font-montserrat">

    {{-- ─── SUCCESS ALERT ──────────────────────────────────────────────── --}}
    @if(!empty($showSuccess))
    <div class="rounded-2xl bg-emerald-500/10 border border-emerald-500/30 px-6 py-5 shadow-sm flex items-center gap-4 print:hidden">
        <div class="w-10 h-10 rounded-full bg-emerald-500/20 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div>
            <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400">Your quotation was successfully generated</p>
            <p class="text-xs text-emerald-500/70 mt-0.5">Your quotation has been saved and is now available in your account.</p>
        </div>
    </div>
    @endif

    {{-- ─── HERO ───────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm print:hidden">
        <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Motor Insurance</p>
        <h1 class="mt-0.5 text-2xl font-bold text-white tracking-tight">Motor Insurance Quotation</h1>
        <p class="mt-1 text-xs text-blue-200">Your personalised premium breakdown from Kenya's leading motor insurers. Review, compare and download your quotation below.</p>
        <div class="mt-3 flex flex-wrap items-center gap-3">
            <div class="inline-flex items-center gap-2 rounded-full bg-white/15 border border-white/25 px-4 py-1.5 text-xs font-bold text-white tracking-wide">
                <span class="opacity-70 font-medium">Ref:</span>
                <span id="hero-quote-num">{{ $quotation->quotation_number }}</span>
                <button type="button" onclick="copyQuoteNumber()" title="Copy quotation number" class="text-white/70 hover:text-white transition">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                </button>
                <span id="hero-copy-feedback" class="hidden text-emerald-300">Copied!</span>
            </div>
            <div class="inline-flex flex-wrap items-center gap-2">
                <a href="{{ route('quotations.download.pdf', $quotation) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-white/15 hover:bg-white/25 border border-white/25 text-white rounded-full transition">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Download
                </a>
                <a href="{{ route('quotations.preview.pdf', $quotation) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-white/15 hover:bg-white/25 border border-white/25 text-white rounded-full transition">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                    Print
                </a>
                <button type="button" onclick="actionTogglePreview()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-white/15 hover:bg-white/25 border border-white/25 text-white rounded-full transition">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    Preview
                </button>
                <a href="{{ route('stickers.issue', ['quotation_id' => $quotation->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-emerald-500 hover:bg-emerald-400 text-white rounded-full transition">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Proceed to Request for Sticker
                </a>
            </div>
        </div>
    </div>

    {{-- ─── CUSTOMER & VEHICLE SUMMARY ─────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 print:hidden">

        {{-- Personal Details --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Customer</p>
                <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Personal Details</h2>
            </div>
            <div class="p-5 space-y-3 text-xs">
            <div class="flex justify-between items-start gap-3">
                <span class="text-gray-400 shrink-0">Salutation</span>
                <span class="font-medium text-gray-800 dark:text-white text-right">{{ $quotation->customer_salutation ?: '—' }}</span>
            </div>
            <div class="flex justify-between items-start gap-3">
                <span class="text-gray-400 shrink-0">First Name</span>
                <span class="font-medium text-gray-800 dark:text-white text-right">{{ $quotation->customer_first_name ?: '—' }}</span>
            </div>
            <div class="flex justify-between items-start gap-3">
                <span class="text-gray-400 shrink-0">Last Name</span>
                <span class="font-medium text-gray-800 dark:text-white text-right">{{ $quotation->customer_last_name ?: '—' }}</span>
            </div>
            <div class="flex justify-between items-start gap-3">
                <span class="text-gray-400 shrink-0">Phone</span>
                <span class="font-medium text-gray-800 dark:text-white text-right">{{ $quotation->customer_phone ?: '—' }}</span>
            </div>
            <div class="flex justify-between items-start gap-3">
                <span class="text-gray-400 shrink-0">Email</span>
                <span class="font-medium text-gray-800 dark:text-white text-right break-all">{{ $quotation->customer_email ?: '—' }}</span>
            </div>
        </div>
        </div>

        {{-- Vehicle Details --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
        <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Vehicle</p>
        <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Vehicle Details</h2>
    </div>

    {{-- auto-fit rather than a fixed 2 columns: this card is also rendered in a
         narrow (~227px) sidebar column, where two columns leave ~81px each and
         the label/value pairs spill out of the card. Sizing against the actual
         container drops it to one column there and keeps two when there's room. --}}
    <div class="p-5 grid gap-x-6 gap-y-3 text-xs" style="grid-template-columns:repeat(auto-fit,minmax(min(200px,100%),1fr))">

        {{-- Registration No --}}
        <div class="flex justify-between items-start gap-2">
            <span class="text-gray-400 shrink-0">Registration No</span>
            <span class="font-medium text-gray-800 dark:text-white text-right">
                {{ $quotation->car_registration ?: '—' }}
            </span>
        </div>

        {{-- Make of Vehicle --}}
        <div class="flex justify-between items-start gap-2">
            <span class="text-gray-400 shrink-0">Make of Vehicle</span>
            <span class="font-medium text-gray-800 dark:text-white text-right">
                {{ $quotation->car_make ?: '—' }}
            </span>
        </div>

        {{-- Model Name --}}
        <div class="flex justify-between items-start gap-2">
            <span class="text-gray-400 shrink-0">Model Name</span>
            <span class="font-medium text-gray-800 dark:text-white text-right">
                {{ $quotation->car_model ?: '—' }}
            </span>
        </div>

        {{-- Body Type --}}
        <div class="flex justify-between items-start gap-2">
            <span class="text-gray-400 shrink-0">Body Type</span>
            <span class="font-medium text-gray-800 dark:text-white text-right">
                {{ $quotation->body_type ?: '—' }}
            </span>
        </div>

        {{-- Year of Make --}}
        <div class="flex justify-between items-start gap-2">
            <span class="text-gray-400 shrink-0">Year of Make</span>
            <span class="font-medium text-gray-800 dark:text-white text-right">
                {{ $quotation->car_year ?: '—' }}
            </span>
        </div>

        {{-- Color --}}
        <div class="flex justify-between items-start gap-2">
            <span class="text-gray-400 shrink-0">Color</span>
            <span class="font-medium text-gray-800 dark:text-white text-right">
                {{ $quotation->color ?: '—' }}
            </span>
        </div>

        {{-- Chassis No --}}
        <div class="flex justify-between items-start gap-2">
            <span class="text-gray-400 shrink-0">Chassis No</span>
            <span class="font-medium text-gray-800 dark:text-white text-right">
                {{ $quotation->chassis_number ?: '—' }}
            </span>
        </div>

        {{-- Engine No --}}
        <div class="flex justify-between items-start gap-2">
            <span class="text-gray-400 shrink-0">Engine No</span>
            <span class="font-medium text-gray-800 dark:text-white text-right">
                {{ $quotation->engine_number ?: '—' }}
            </span>
        </div>

    </div>
</div>

        {{-- Valuation --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Cover</p>
                <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Valuation</h2>
            </div>
            <div class="p-5 space-y-3 text-xs">
                <div class="flex justify-between items-start gap-3">
                    <span class="text-gray-400 shrink-0">Estimated Value</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right">KES {{ number_format($quotation->car_valuation, 0) }}</span>
                </div>
                <div class="flex justify-between items-start gap-3">
                    <span class="text-gray-400 shrink-0">Cover Type</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right">Comprehensive ({{ ucfirst($quotation->vehicle_type ?? 'private') }})</span>
                </div>
                <div class="flex justify-between items-start gap-3">
                    <span class="text-gray-400 shrink-0">Frequency</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right">Annually</span>
                </div>
                <div class="flex justify-between items-start gap-3">
                    <span class="text-gray-400 shrink-0">Geo. Limit</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right">{{ $breakdown['geo_limit'] ?? 'Kenya' }}</span>
                </div>
            </div>
        </div>
    </div>

   {{-- ─── QUOTATION SUMMARY ──────────────────────────────────────────── --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden print:hidden">
    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
        <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Financials</p>
        <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Quotation Summary — {{ $quotation->company->name ?? 'Insurer' }}</h2>
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
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <tr class="hover:bg-blue-50 dark:hover:bg-blue-900/20">
                    <td class="px-4 py-3">Estimated Vehicle Value</td>
                    <td class="px-4 py-3 text-gray-400 text-xs">—</td>
                    <td class="px-4 py-3 text-right font-semibold tabular-nums">{{ number_format($quotation->car_valuation, 2) }}</td>
                </tr>
                <tr class="hover:bg-blue-50 dark:hover:bg-blue-900/20">
                    <td class="px-4 py-3">Basic Annual Premium</td>
                    <td class="px-4 py-3 text-gray-400 text-xs">@ {{ \App\Models\Rate::fmtPercent($breakdown['premium_rate'] ?? 0) }}% of vehicle value</td>
                    <td class="px-4 py-3 text-right font-semibold tabular-nums">{{ number_format($breakdown['basic_premium'] ?? 0, 2) }}</td>
                </tr>
                <tr class="hover:bg-blue-50 dark:hover:bg-blue-900/20">
                    <td class="px-4 py-3">PVT(Political Violence &amp; Terrorism Extension)</td>
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ !empty($breakdown['is_pvt_inclusive']) ? 'Included in basic premium' : '@ ' . \App\Models\Rate::fmtPercent($breakdown['pvt_rate'] ?? 0) . '%' }}</td>
                    <td class="px-4 py-3 text-right font-semibold tabular-nums">{{ number_format($breakdown['pvt_premium'] ?? 0, 2) }}</td>
                </tr>
                @if(($breakdown['pll_total'] ?? 0) > 0)
                <tr class="hover:bg-blue-50 dark:hover:bg-blue-900/20">
                    <td class="px-4 py-3">Passenger Legal Liability (PLL)</td>
                    <td class="px-4 py-3 text-gray-400 text-xs">KES {{ number_format($breakdown['pll_per_person'] ?? 0, 0) }} &times; {{ $quotation->num_passengers ?? 1 }} pax</td>
                    <td class="px-4 py-3 text-right font-semibold tabular-nums">{{ number_format($breakdown['pll_total'] ?? 0, 2) }}</td>
                </tr>
                @endif
                <tr class="hover:bg-blue-50 dark:hover:bg-blue-900/20">
                    <td class="px-4 py-3">Training Levy</td>
                    <td class="px-4 py-3 text-gray-400 text-xs">@ {{ \App\Models\Rate::fmtPercent($breakdown['training_levy_rate'] ?? 0) }}% of premium + PVT + PLL</td>
                    <td class="px-4 py-3 text-right font-semibold tabular-nums">{{ number_format($breakdown['training_levy'] ?? 0, 2) }}</td>
                </tr>
                <tr class="hover:bg-blue-50 dark:hover:bg-blue-900/20">
                    <td class="px-4 py-3">Policy Holder Fund</td>
                    <td class="px-4 py-3 text-gray-400 text-xs">@ {{ \App\Models\Rate::fmtPercent($breakdown['policy_holder_fund_rate'] ?? 0) }}% of premium + PVT + PLL</td>
                    <td class="px-4 py-3 text-right font-semibold tabular-nums">{{ number_format($breakdown['policy_holder_fund'] ?? 0, 2) }}</td>
                </tr>
                <tr class="hover:bg-blue-50 dark:hover:bg-blue-900/20">
                    <td class="px-4 py-3">Stamp Duty</td>
                    <td class="px-4 py-3 text-gray-400 text-xs">Fixed</td>
                    <td class="px-4 py-3 text-right font-semibold tabular-nums">{{ number_format($breakdown['stamp_duty'] ?? 0, 2) }}</td>
                </tr>
                <tr class="hover:bg-blue-50 dark:hover:bg-blue-900/20">
                    <td class="px-4 py-3">Excess - Own Damage</td>
                    <td class="px-4 py-3 text-gray-400 text-xs">—</td>
                    <td class="px-4 py-3 text-right font-semibold tabular-nums">{{ ($breakdown['excess_own_damage'] ?? 0) > 0 ? number_format($breakdown['excess_own_damage'], 2) : 'Not Applicable' }}</td>
                </tr>
                <tr class="hover:bg-blue-50 dark:hover:bg-blue-900/20">
                    <td class="px-4 py-3">Excess - Theft</td>
                    <td class="px-4 py-3 text-gray-400 text-xs">—</td>
                    <td class="px-4 py-3 text-right font-semibold tabular-nums">{{ ($breakdown['excess_theft'] ?? 0) > 0 ? number_format($breakdown['excess_theft'], 2) : 'Not Applicable' }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-gray-200 dark:border-gray-600">
                    <td colspan="2" class="pt-3 font-bold text-gray-800 dark:text-white">Total Premium (incl. taxes)</td>
                    <td class="pt-3 text-right font-extrabold text-lg text-blue-600 dark:text-blue-400">KES {{ number_format($breakdown['total_premium'] ?? $quotation->total_premium, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if(!empty($rateCategories) && collect($rateCategories)->flatten(1)->isNotEmpty())
<div class="px-6 pb-6 pt-2 space-y-6 border-t border-gray-100 dark:border-gray-700">
    <h3 class="text-[10px] font-bold uppercase tracking-widest text-gray-400 pt-4">Benefits &amp; Cover Details</h3>
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
                            <td class="px-4 py-2.5 text-right font-semibold text-gray-800 dark:text-white tabular-nums">
                                @if($item->item_name === 'Max Age New Business' && $item->value)
                                    {{ number_format($item->value, 0) }} years
                                @elseif($item->percentage)
                                    {{ \App\Models\Rate::fmtPercent($item->percentage) }}%
                                @elseif($item->value)
                                    KES {{ number_format($item->value, 0) }}
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

    <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-700">
        <table class="w-full text-xs">
            <tfoot>
                <tr class="border-t-2 border-gray-200 dark:border-gray-600">
                    <td colspan="2" class="px-4 py-3 font-bold text-gray-800 dark:text-white">Total Premium (incl. taxes)</td>
                    <td class="px-4 py-3 text-right font-extrabold text-lg text-blue-600 dark:text-blue-400">KES {{ number_format($breakdown['total_premium'] ?? $quotation->total_premium, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endif
</div>

    {{-- ─── DOWNLOAD CARD ──────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden print:hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Actions</p>
            <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Download Quote</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border-2 border-blue-500 ring-2 ring-blue-500/20 p-5 text-center sm:col-span-2 lg:col-span-1">
                <div class="h-11 flex items-center justify-center mb-3">
                    @php $__companyLogo = $quotation->company?->logoUrl(); @endphp
                    @if($__companyLogo)
                        <img src="{{ $__companyLogo }}" alt="{{ $quotation->company->name }}" class="max-h-10 max-w-[110px] object-contain" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
                    @endif
                    <span class="{{ $__companyLogo ? 'hidden' : '' }} text-xs font-extrabold tracking-wider text-blue-600">{{ $quotation->company->name ?? '—' }}</span>
                </div>
                <div class="text-xs font-bold text-gray-800 dark:text-white mb-1">{{ $quotation->company->name ?? 'Insurer' }}</div>
                <div class="text-[11px] text-gray-400 mb-3.5">Rate: <strong>{{ \App\Models\Rate::fmtPercent($breakdown['premium_rate'] ?? 0) }}%</strong> of vehicle value</div>
                <div class="text-lg font-extrabold text-blue-600 dark:text-blue-400 mb-3.5 tabular-nums">KES {{ number_format($breakdown['total_premium'] ?? $quotation->total_premium, 2) }}<small class="block text-[10px] font-semibold text-gray-400">Total incl. taxes</small></div>
                <a href="{{ route('quotations.download.pdf', $quotation) }}" target="_blank" class="block w-full py-2.5 rounded-lg text-xs font-bold text-white transition bg-blue-600 hover:bg-blue-500">Download Quote ↓</a>
            </div>
        </div>
    </div>

    {{-- ─── ACTION BAR ─────────────────────────────────────────────────── --}}
    <div class="action-bar visible bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center gap-3 print:hidden" id="action-bar">
        <span class="action-bar-label text-[11px] font-semibold uppercase tracking-widest text-gray-400 flex-1">Quotation Actions</span>
        <a href="{{ route('quotations.download.pdf', $quotation) }}" target="_blank" class="action-btn action-btn-download inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Download PDF
        </a>
        <a href="{{ route('quotations.preview.pdf', $quotation) }}" target="_blank" class="action-btn action-btn-print inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-semibold bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Print
        </a>
        <button class="action-btn action-btn-preview inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-semibold bg-red-600 hover:bg-red-500 text-white border border-blue-600 hover:bg-red-50 dark:hover:bg-blue-900/20 rounded-lg transition" id="btn-preview" onclick="actionTogglePreview()">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
            <circle cx="12" cy="12" r="3"/>
        </svg>
        Preview
    </button>
        @if($quotation)
        <a href="{{ route('stickers.issue', ['quotation_id' => $quotation->id]) }}"
           class="inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg transition">
            Request for Sticker
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
        @endif
    </div>

    {{-- ─── PDF PREVIEW (real, streamed from the server) ───────────────── --}}
    <div class="pdf-preview print:hidden" id="pdf-preview" style="display:none">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <p class="text-xs font-bold text-gray-800 dark:text-white">Quotation Preview</p>
                <button onclick="actionTogglePreview()" class="text-gray-400 hover:text-gray-600 text-lg leading-none">&times;</button>
            </div>
            <iframe id="pdf-preview-frame" src="" style="width:100%;height:80vh;border:0;display:block"></iframe>
        </div>
    </div>

</div>

@push('scripts')
<script>
let _pdfPreviewLoaded = false;

function actionTogglePreview() {
  const wrap  = document.getElementById('pdf-preview');
  const frame = document.getElementById('pdf-preview-frame');
  const btn   = document.getElementById('btn-preview');
  const isHidden = wrap.style.display === 'none' || !wrap.style.display;
  if (isHidden) {
    if (!_pdfPreviewLoaded) {
      frame.src = '{{ route("quotations.preview.pdf", $quotation) }}';
      _pdfPreviewLoaded = true;
    }
    wrap.style.display = 'block';
    if (btn) btn.classList.add('active');
    wrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
  } else {
    wrap.style.display = 'none';
    if (btn) btn.classList.remove('active');
  }
}

function copyQuoteNumber() {
  const text = document.getElementById('hero-quote-num').textContent.trim();
  const feedback = document.getElementById('hero-copy-feedback');
  const showFeedback = () => {
    if (!feedback) return;
    feedback.classList.remove('hidden');
    setTimeout(() => feedback.classList.add('hidden'), 1500);
  };
  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(text).then(showFeedback).catch(() => {});
  } else {
    const el = document.createElement('textarea');
    el.value = text;
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
    showFeedback();
  }
}
</script>
@endpush

</x-app-layout>
