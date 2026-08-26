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
        <div class="mt-3 inline-flex items-center gap-2 rounded-full bg-white/15 border border-white/25 px-4 py-1.5 text-xs font-bold text-white tracking-wide">
            <span class="opacity-70 font-medium">Ref:</span>
            <span id="hero-quote-num">—</span>
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
                    <span class="text-gray-400 shrink-0">Full Name</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right" id="s-name">—</span>
                </div>
                <div class="flex justify-between items-start gap-3">
                    <span class="text-gray-400 shrink-0">Phone</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right" id="s-phone">—</span>
                </div>
                <div class="flex justify-between items-start gap-3">
                    <span class="text-gray-400 shrink-0">Email</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right break-all" id="s-email">—</span>
                </div>
            </div>
        </div>

        {{-- Vehicle Details --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Vehicle</p>
                <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Vehicle Details</h2>
            </div>
            <div class="p-5 space-y-3 text-xs">
                <div class="flex justify-between items-start gap-3">
                    <span class="text-gray-400 shrink-0">Registration</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right" id="s-reg">—</span>
                </div>
                <div class="flex justify-between items-start gap-3">
                    <span class="text-gray-400 shrink-0">Make / Model</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right" id="s-make">—</span>
                </div>
                <div class="flex justify-between items-start gap-3">
                    <span class="text-gray-400 shrink-0">Year / Body</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right" id="s-year">—</span>
                </div>
                <div class="flex justify-between items-start gap-3">
                    <span class="text-gray-400 shrink-0">Color</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right" id="s-color">—</span>
                </div>
                <div class="flex justify-between items-start gap-3">
                    <span class="text-gray-400 shrink-0">Chassis No.</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right" id="s-chassis">—</span>
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
                    <span class="font-medium text-gray-800 dark:text-white text-right" id="s-value">—</span>
                </div>
                <div class="flex justify-between items-start gap-3">
                    <span class="text-gray-400 shrink-0">Cover Type</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right">Comprehensive</span>
                </div>
                <div class="flex justify-between items-start gap-3">
                    <span class="text-gray-400 shrink-0">Frequency</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right">Annually</span>
                </div>
                <div class="flex justify-between items-start gap-3">
                    <span class="text-gray-400 shrink-0">Geo. Limit</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right">Kenya / Uganda / TZ</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── QUOTATION BREAKDOWN (single company mode) ──────────────────── --}}
    <div id="single-breakdown-wrap" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden print:hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Financials</p>
            <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Quotation Summary — <span id="breakdown-company-label">GA Insurance</span></h2>
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
                <tbody id="breakdown-rows" class="divide-y divide-gray-100 dark:divide-gray-700">
                    {{-- populated by JS --}}
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-200 dark:border-gray-600">
                        <td colspan="2" class="pt-3 font-bold text-gray-800 dark:text-white">Total Premium (incl. taxes)</td>
                        <td id="breakdown-total" class="pt-3 text-right font-extrabold text-lg text-blue-600 dark:text-blue-400">—</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ─── COMPARE ALL TABLE ──────────────────────────────────────────── --}}
    <div id="compare-all-wrap" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden print:hidden" style="display:none">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Comparison</p>
            <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Premium Comparison — All Insurers</h2>
        </div>
        <div class="p-6 overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="text-gray-400 uppercase tracking-wider text-[10px]">
                        <th class="text-left font-semibold pb-2">Description</th>
                        <th class="text-right font-semibold pb-2">GA Insurance</th>
                        <th class="text-right font-semibold pb-2">Britam</th>
                        <th class="text-right font-semibold pb-2">Sanlam Allianz</th>
                    </tr>
                </thead>
                <tbody id="compare-rows" class="divide-y divide-gray-100 dark:divide-gray-700">
                    {{-- populated by JS --}}
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-200 dark:border-gray-600">
                        <td class="pt-3 font-bold text-gray-800 dark:text-white">Total Premium (incl. taxes)</td>
                        <td id="cmp-total-ga" class="pt-3 text-right font-extrabold text-blue-600 dark:text-blue-400">—</td>
                        <td id="cmp-total-britam" class="pt-3 text-right font-extrabold text-blue-600 dark:text-blue-400">—</td>
                        <td id="cmp-total-sanlam" class="pt-3 text-right font-extrabold text-blue-600 dark:text-blue-400">—</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ─── DOWNLOAD CARDS ─────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden print:hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Actions</p>
            <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5" id="dl-cards-label">Download Quote</h2>
        </div>
        <div id="dl-cards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 p-6">
            {{-- populated by JS based on mode --}}
        </div>
    </div>

    {{-- ─── ACTION BAR ─────────────────────────────────────────────────── --}}
    <div class="action-bar bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center gap-3 print:hidden" id="action-bar">
        <span class="action-bar-label text-[11px] font-semibold uppercase tracking-widest text-gray-400 flex-1">Quotation Actions</span>
        <button class="action-btn action-btn-download inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition" id="btn-download" onclick="actionDownload()">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Download PDF
        </button>
        <button class="action-btn action-btn-print inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-semibold bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition" onclick="actionPrint()">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Print
        </button>
        <button class="action-btn action-btn-preview inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-semibold bg-white dark:bg-gray-800 text-blue-600 border border-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition" id="btn-preview" onclick="actionTogglePreview()">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            Preview
        </button>
    </div>

    {{-- ─── PDF PREVIEW (print target) ─────────────────────────────────── --}}
    <div class="pdf-preview hidden print:block" id="pdf-preview">
        {{-- populated by JS --}}
    </div>

</div>

@push('scripts')
@php
    $_quoteData = $quotation ? [
        'salutation' => '',
        'firstName'  => optional($quotation->user)->first_name ?? '',
        'lastName'   => optional($quotation->user)->last_name ?? '',
        'phone'      => optional($quotation->user)->mobile ?? '',
        'email'      => optional($quotation->user)->email ?? '',
        'reg'        => $quotation->car_registration ?? '',
        'chassis'    => '',
        'make'       => $quotation->car_make ?? '',
        'model'      => $quotation->car_model ?? '',
        'body'       => '',
        'year'       => $quotation->car_year ?? '',
        'color'      => '',
        'value'      => (float) ($quotation->car_valuation ?? 0),
        'quoteNum'   => $quotation->quotation_number ?? '',
        'mode'       => 'ALL',   // 'GA' | 'BRITAM' | 'SANLAM' | 'ALL'
    ] : null;
@endphp
<script>
// ═══════════════════════════════════════════════════════
//  QUOTATION PAGE
//  Reads server-injected data (window.MIC_QUOTE_DATA), falling back
//  to sessionStorage set by the quote form, then a demo record.
// ═══════════════════════════════════════════════════════

// Server-side quote data injected by the Blade view (preferred).
window.MIC_QUOTE_DATA = @json($_quoteData);

const RATES = {
  GA:     { rate: 0.035, label: '3.5%', name: 'GA Insurance Ltd',         color: '#005baa', imgSrc: '{{ asset("images/GA Insurance.png") }}'   },
  BRITAM: { rate: 0.041, label: '4.1%', name: 'Britam General Insurance', color: '#0071bc', imgSrc: '{{ asset("images/britam_logo.png") }}'    },
  SANLAM: { rate: 0.045, label: '4.5%', name: 'Sanlam Allianz Insurance', color: '#1d3c6e', imgSrc: '{{ asset("images/Sanlam-Allianz.png") }}' }
};
const TAX_RATE = 0.005; // 0.5% IRA levy

const COMPANY_META = {
  GA: {
    address: 'GA Insurance Ltd, GA Building, Hospital Road, Upper Hill, P.O. Box 42166 - 00100 Nairobi',
    website: 'www.gainsurance.co.ke'
  },
  BRITAM: {
    address: 'Britam General Insurance Company (Kenya) Limited\nBritam Centre, Junction of Mara and Ragati Road, Upper Hill\nP.O. BOX 30375 - 0100 Nairobi',
    website: 'www.britam.com'
  },
  SANLAM: {
    address: 'Sanlam Allianz Insurance Company Limited\nSanlam Tower, Hospital Road, Upper Hill\nP.O. Box 30522 - 00100 Nairobi',
    website: 'www.sanlam.co.ke'
  }
};

function fmt(n) {
  return 'KES ' + Number(n).toLocaleString('en-KE', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
}
function calcPremiums(vehicleValue) {
  const result = {};
  for (const [key, cfg] of Object.entries(RATES)) {
    const basic = vehicleValue * cfg.rate;
    const tax   = basic * TAX_RATE;
    const total = basic + tax;
    result[key] = { basic, tax, total };
  }
  return result;
}

// ── READ DATA ──
function getFormData() {
  // 1. Data injected server-side by the Blade view (preferred)
  if (window.MIC_QUOTE_DATA) return window.MIC_QUOTE_DATA;

  // 2. Data left in sessionStorage by the quote form (index.html flow)
  try {
    const raw = sessionStorage.getItem('micQuoteData');
    if (raw) return JSON.parse(raw);
  } catch(e) {}

  // 3. Demo fallback
  return {
    salutation: 'Mr.',
    firstName: 'Robert',
    lastName: 'Gicheha',
    phone: '+254 010 351 517',
    email: 'r.gicheha@example.com',
    reg: 'KDH 724V',
    chassis: 'WVWZZZ1KZ9W123456',
    make: 'Volkswagen',
    model: 'Golf',
    body: 'Hatchback',
    year: '2018',
    color: 'Silver/Grey',
    value: 800000,
    quoteNum: 'MIC-2026/0042',
    mode: 'ALL'   // 'GA' | 'BRITAM' | 'SANLAM' | 'ALL'
  };
}

// ── URL PARAM OVERRIDE ──
function getMode() {
  const p = new URLSearchParams(window.location.search);
  return p.get('company') || getFormData().mode || 'ALL';
}

function genQuoteNum(company) {
  const d = new Date();
  const y = d.getFullYear();
  const n = String(Math.floor(Math.random() * 9000) + 1000);
  return `${company}-${y}/${n}`;
}

// ── SHOW ACTION BAR ──
// Called the first time a download card is clicked; stays visible thereafter.
function showActionBar() {
  document.getElementById('action-bar').classList.add('visible');
}

// ── POPULATE PAGE ──
function init() {
  const fd   = getFormData();
  const mode = getMode();
  const value = Number(fd.value) || 800000;
  const prems = calcPremiums(value);
  const qNum  = fd.quoteNum || genQuoteNum(mode === 'ALL' ? 'MIC' : mode);

  // Hero
  document.getElementById('hero-quote-num').textContent = qNum;

  // Summary
  document.getElementById('s-name').textContent    = `${fd.salutation || ''} ${fd.firstName} ${fd.lastName}`.trim();
  document.getElementById('s-phone').textContent   = fd.phone || '—';
  document.getElementById('s-email').textContent   = fd.email || '—';
  document.getElementById('s-reg').textContent     = fd.reg   || '—';
  document.getElementById('s-make').textContent    = `${fd.make} ${fd.model}`.trim() || '—';
  document.getElementById('s-year').textContent    = `${fd.year}  •  ${fd.body || '—'}`;
  document.getElementById('s-color').textContent   = fd.color   || '—';
  document.getElementById('s-chassis').textContent = fd.chassis || '—';
  document.getElementById('s-value').textContent   = fmt(value);

  if (mode === 'ALL') {
    renderCompareAll(value, prems, fd, qNum);
    // Pre-build preview for the first company so Download/Print work immediately
    buildPDFPreview('GA', value, prems['GA'], fd, qNum);
  } else {
    renderSingle(mode, value, prems, fd, qNum);
    // renderSingle already calls buildPDFPreview
  }
}

// ── SINGLE COMPANY MODE ──
function renderSingle(company, value, prems, fd, qNum) {
  const cfg = RATES[company];
  const p   = prems[company];

  document.getElementById('single-breakdown-wrap').style.display = '';
  document.getElementById('compare-all-wrap').style.display      = 'none';

  document.getElementById('breakdown-company-label').textContent = cfg.name;

  const rows = [
    ['Estimated Vehicle Value', '—', fmt(value)],
    ['Basic Annual Premium', `@ ${cfg.label} of vehicle value`, fmt(p.basic)],
    ['Windscreen Premium', 'Included', 'KES 0'],
    ['Radio / Cassette Premium', 'Included', 'KES 0'],
    ['IRA Levy (Training Levy)', `@ 0.5% of basic premium`, fmt(p.tax)],
  ];
  const tbody = document.getElementById('breakdown-rows');
  tbody.innerHTML = rows.map(([desc, note, amt]) =>
    `<tr class="hover:bg-blue-50 dark:hover:bg-blue-900/20"><td class="px-4 py-3">${desc}</td><td class="px-4 py-3 text-gray-400 text-xs">${note}</td><td class="px-4 py-3 text-right font-semibold tabular-nums">${amt}</td></tr>`
  ).join('');
  document.getElementById('breakdown-total').textContent = fmt(p.total);

  // Download cards: the 3 companies + "Download All"
  renderDownloadCards(company, value, prems, fd, qNum);

  // PDF preview for print
  buildPDFPreview(company, value, prems[company], fd, qNum);
}

// ── COMPARE ALL MODE ──
function renderCompareAll(value, prems, fd, qNum) {
  document.getElementById('single-breakdown-wrap').style.display = 'none';
  document.getElementById('compare-all-wrap').style.display      = '';
  document.getElementById('dl-cards-label').textContent          = 'Download Individual Quote';

  const rows = [
    { label: 'Estimated Vehicle Value', ga: fmt(value), britam: fmt(value), sanlam: fmt(value) },
    { label: 'Premium Rate', ga: '3.5%', britam: '4.1%', sanlam: '4.5%' },
    { label: 'Basic Annual Premium', ga: fmt(prems.GA.basic), britam: fmt(prems.BRITAM.basic), sanlam: fmt(prems.SANLAM.basic) },
    { label: 'IRA Levy (0.5%)',       ga: fmt(prems.GA.tax),   britam: fmt(prems.BRITAM.tax),   sanlam: fmt(prems.SANLAM.tax)   },
  ];
  document.getElementById('compare-rows').innerHTML = rows.map(r =>
    `<tr class="hover:bg-blue-50 dark:hover:bg-blue-900/20"><td class="px-4 py-3">${r.label}</td><td class="px-4 py-3 text-right font-semibold">${r.ga}</td><td class="px-4 py-3 text-right font-semibold">${r.britam}</td><td class="px-4 py-3 text-right font-semibold">${r.sanlam}</td></tr>`
  ).join('');
  document.getElementById('cmp-total-ga').textContent     = fmt(prems.GA.total);
  document.getElementById('cmp-total-britam').textContent = fmt(prems.BRITAM.total);
  document.getElementById('cmp-total-sanlam').textContent = fmt(prems.SANLAM.total);

  // Four download cards
  renderDownloadCards('ALL', value, prems, fd, qNum);
}

function renderDownloadCards(mode, value, prems, fd, qNum) {
  const container = document.getElementById('dl-cards');
  const companies = [
    { key: 'GA',     btnClass: 'bg-[#005baa] hover:bg-[#004a8c]', btnLabel: 'Download GA Quote' },
    { key: 'BRITAM', btnClass: 'bg-[#0071bc] hover:bg-[#005fa0]', btnLabel: 'Download Britam Quote' },
    { key: 'SANLAM', btnClass: 'bg-[#1d3c6e] hover:bg-[#152d54]', btnLabel: 'Download Sanlam Quote' },
  ];

  let html = companies.map(({ key, btnClass, btnLabel }) => {
    const cfg = RATES[key];
    const p   = prems[key];
    const selected = (mode === key) ? 'border-blue-500 ring-2 ring-blue-500/20 shadow-md' : 'border-gray-200 dark:border-gray-700';
    return `
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border-2 p-5 text-center cursor-pointer transition hover:-translate-y-0.5 hover:shadow-md hover:border-blue-500 ${selected}" onclick="downloadQuote('${key}')">
        <div class="h-11 flex items-center justify-center mb-3">
          <img src="${cfg.imgSrc}" alt="${cfg.name}" class="max-h-10 max-w-[110px] object-contain" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
          <span class="hidden text-xs font-extrabold tracking-wider text-blue-600">${key}</span>
        </div>
        <div class="text-xs font-bold text-gray-800 dark:text-white mb-1">${cfg.name}</div>
        <div class="text-[11px] text-gray-400 mb-3.5">Rate: <strong>${cfg.label}</strong> of vehicle value</div>
        <div class="text-lg font-extrabold text-blue-600 dark:text-blue-400 mb-3.5 tabular-nums">${fmt(p.total)}<small class="block text-[10px] font-semibold text-gray-400">Total incl. taxes</small></div>
        <button class="block w-full py-2.5 rounded-lg text-xs font-bold text-white transition ${btnClass}">${btnLabel} ↓</button>
      </div>`;
  }).join('');

  // "Download All" card
  html += `
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border-2 border-red-600 p-5 text-center cursor-pointer transition hover:-translate-y-0.5 hover:shadow-md" onclick="downloadAll()">
      <div class="h-11 flex items-center justify-center mb-3">
        <img src="{{ asset('images/benchmarking.png') }}" alt="Compare All" class="max-h-10 max-w-[110px] object-contain" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
        <span class="hidden text-xs font-extrabold tracking-wider text-red-600">ALL</span>
      </div>
      <div class="text-xs font-bold text-gray-800 dark:text-white mb-1">Download All Quotes</div>
      <div class="text-[11px] text-gray-400 mb-3.5">Full comparison of all insurers</div>
      <div class="text-lg font-extrabold text-red-600 mb-3.5">3 Quotes<small class="block text-[10px] font-semibold text-gray-400">GA · Britam · Sanlam</small></div>
      <button class="block w-full py-2.5 rounded-lg text-xs font-bold text-white transition bg-[#D0021B] hover:bg-[#b0021a]">Download All ↓</button>
    </div>`;

  container.innerHTML = html;
}

// ── PDF PREVIEW STYLES (inline, kept for the printable letterhead) ──
const PDF_TABLE = 'width:100%;border-collapse:collapse;margin:16px 0;font-size:13px';
const PDF_TH    = 'background:#f0f4fa;padding:8px 12px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;border:1px solid #dde3ed';
const PDF_TD    = 'padding:9px 12px;border:1px solid #dde3ed';

// ── BUILD LETTERHEAD BANNER ──
// Logo images sit directly on the coloured banner background.
// No CSS filter is applied so logos render in their natural colours.
function buildLetterheadBanner(cfg, meta, now) {
  return `
    <div style="display:flex;justify-content:space-between;align-items:stretch;min-height:90px;background:${cfg.color}">
      <div style="display:flex;align-items:center;padding:20px 28px;min-width:220px">
        <img src="${cfg.imgSrc}" alt="${cfg.name}" style="max-height:44px;max-width:160px;object-fit:contain"
             onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
        <span style="display:none;font-size:22px;font-weight:800;color:#fff">${cfg.name.split(' ')[0].toUpperCase()}</span>
      </div>
      <div style="flex:1;display:flex;align-items:center;justify-content:flex-end;padding:18px 28px;text-align:right">
        <p style="font-size:12px;line-height:1.75;color:rgba(255,255,255,.92);margin:0">${meta.address.replace(/\n/g,'<br>')}<br>Website: ${meta.website}</p>
      </div>
    </div>`;
}

// ── BUILD PDF PREVIEW (printable letterhead) ──
function buildPDFPreview(company, value, prem, fd, qNum) {
  const cfg  = RATES[company];
  const meta = COMPANY_META[company];
  const now  = new Date().toLocaleString('en-KE', { dateStyle: 'long', timeStyle: 'short' });
  const name = `${fd.salutation || ''} ${fd.firstName} ${fd.lastName}`.trim();

  const html = `
    ${buildLetterheadBanner(cfg, meta, now)}
    <div style="padding:28px 36px">
      <p style="margin-top:4px;font-size:13px;line-height:1.7;margin-bottom:16px">${now}</p>
      <p style="font-size:13px;line-height:1.7;margin-bottom:16px">Dear ${name},</p>
      <h2 style="font-size:15px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">RE: PRIVATE CAR INSURANCE QUOTE</h2>
      <p style="font-size:12px;color:#6b7280;margin-bottom:20px">Quotation Reference: <strong>${qNum}</strong></p>
      <p style="font-size:13px;line-height:1.7;margin-bottom:16px">Thank you for requesting us to provide a quote for the above mentioned. We are pleased to submit the quote</p>

      <table style="${PDF_TABLE}">
        <thead>
          <tr>
            <th style="${PDF_TH}">Owner's Name</th><th style="${PDF_TH}">Vehicle Make</th><th style="${PDF_TH}">Year of Manufacture</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="${PDF_TD}">${name}</td>
            <td style="${PDF_TD}">${fd.make || '—'}</td>
            <td style="${PDF_TD}">${fd.year || '—'}</td>
          </tr>
        </tbody>
      </table>

      <table style="${PDF_TABLE}">
        <thead>
          <tr>
            <th style="${PDF_TH}">Registration Number</th><th style="${PDF_TH}">Vehicle Estimated Price</th><th style="${PDF_TH}">Phone Number</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="${PDF_TD}">${fd.reg || '—'}</td>
            <td style="${PDF_TD}">${fmt(value)}</td>
            <td style="${PDF_TD}">${fd.phone || '—'}</td>
          </tr>
        </tbody>
      </table>

      <table style="${PDF_TABLE}">
        <thead><tr><th colspan="2" style="${PDF_TH}">COVER TYPE</th></tr></thead>
        <tbody>
          <tr><td style="${PDF_TD}">Scope of Cover</td><td style="${PDF_TD}">Comprehensive</td></tr>
          <tr><td style="${PDF_TD}">Frequency</td><td style="${PDF_TD}">Annually</td></tr>
        </tbody>
      </table>

      <table style="${PDF_TABLE}">
        <thead><tr><th colspan="3" style="${PDF_TH}background:${cfg.color};color:#fff;border-color:${cfg.color}">PREMIUM &amp; RATES BREAKDOWN</th></tr></thead>
        <tbody>
          <tr><td style="${PDF_TD}">Estimated Vehicle Value</td><td style="${PDF_TD}">—</td><td style="${PDF_TD}text-align:right;font-weight:bold">${fmt(value)}</td></tr>
          <tr><td style="${PDF_TD}">Basic Annual Premium</td><td style="${PDF_TD}">@ ${cfg.label}</td><td style="${PDF_TD}text-align:right">${fmt(prem.basic)}</td></tr>
          <tr><td style="${PDF_TD}">PVT Premium</td><td style="${PDF_TD}">Included</td><td style="${PDF_TD}text-align:right">KES 0</td></tr>
          <tr><td style="${PDF_TD}">Windscreen Premium</td><td style="${PDF_TD}">Included</td><td style="${PDF_TD}text-align:right">KES 0</td></tr>
          <tr><td style="${PDF_TD}">Radio / Cassette Premium</td><td style="${PDF_TD}">Included</td><td style="${PDF_TD}text-align:right">KES 0</td></tr>
          <tr><td style="${PDF_TD}">IRA Levy (Training Levy)</td><td style="${PDF_TD}">@ 0.5%</td><td style="${PDF_TD}text-align:right">${fmt(prem.tax)}</td></tr>
          <tr><td style="${PDF_TD}font-weight:bold">Total Premium (incl. taxes)</td><td style="${PDF_TD}">—</td><td style="${PDF_TD}text-align:right;font-weight:bold">${fmt(prem.total)}</td></tr>
        </tbody>
      </table>

      <table style="${PDF_TABLE}">
        <thead><tr><th colspan="3" style="${PDF_TH}">INSURER RATES COMPARISON</th></tr></thead>
        <tbody>
          ${['GA','BRITAM','SANLAM'].map(k => `<tr><td style="${PDF_TD}">${RATES[k].name}</td><td style="${PDF_TD}text-align:right">@ ${RATES[k].label}</td><td style="${PDF_TD}text-align:right">${fmt(calcPremiums(value)[k].total)}</td></tr>`).join('')}
        </tbody>
      </table>`;

  const wrap = document.getElementById('pdf-preview');
  wrap.innerHTML = html;
  // Keep hidden — only shown via the Preview button or a download trigger.
}

// ── ACTION BAR HANDLERS ──

let _activeCompany = null;

function actionDownload() {
  const wrap = document.getElementById('pdf-preview');
  if (!wrap.innerHTML.trim()) return;
  wrap.style.display = 'block';
  setTimeout(() => {
    window.print();
    setTimeout(() => {
      if (!document.getElementById('btn-preview').classList.contains('active')) {
        wrap.style.display = 'none';
      }
    }, 500);
  }, 150);
}

function actionPrint() {
  const wrap = document.getElementById('pdf-preview');
  if (!wrap.innerHTML.trim()) return;
  wrap.style.display = 'block';
  setTimeout(() => window.print(), 150);
}

function actionTogglePreview() {
  const wrap = document.getElementById('pdf-preview');
  const btn  = document.getElementById('btn-preview');
  if (wrap.style.display === 'none' || !wrap.style.display) {
    wrap.style.display = 'block';
    btn.classList.add('active');
    wrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
  } else {
    wrap.style.display = 'none';
    btn.classList.remove('active');
  }
}

// ── DOWNLOAD CARD CLICK — builds preview, triggers print dialog ──
function downloadQuote(company) {
  _activeCompany = company;
  const fd    = getFormData();
  const value = Number(fd.value) || 800000;
  const prems = calcPremiums(value);
  const qNum  = fd.quoteNum || genQuoteNum(company);
  buildPDFPreview(company, value, prems[company], fd, qNum);

  // Reset preview button to non-active state
  document.getElementById('btn-preview').classList.remove('active');

  // Reveal the action bar now that a quote has been selected
  showActionBar();

  // Trigger download immediately
  document.getElementById('pdf-preview').style.display = 'block';
  setTimeout(() => window.print(), 150);
}

function downloadAll() {
  _activeCompany = 'ALL';
  const fd    = getFormData();
  const value = Number(fd.value) || 800000;
  const prems = calcPremiums(value);

  // Reveal the action bar
  showActionBar();

  const pages = ['GA', 'BRITAM', 'SANLAM'].map(key => {
    const cfg  = RATES[key];
    const meta = COMPANY_META[key];
    const p    = prems[key];
    const now  = new Date().toLocaleString('en-KE', { dateStyle: 'long', timeStyle: 'short' });
    const name = `${fd.salutation || ''} ${fd.firstName} ${fd.lastName}`.trim();
    const qNum = fd.quoteNum || genQuoteNum(key);
    return `
      <div style="page-break-after:always">
        ${buildLetterheadBanner(cfg, meta, now)}
        <div style="padding:28px 36px">
          <p style="margin-top:4px;font-size:13px;line-height:1.7;margin-bottom:16px">${now}</p>
          <p style="font-size:13px;line-height:1.7;margin-bottom:16px">Dear ${name},</p>
          <h2 style="font-size:15px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">RE: PRIVATE CAR INSURANCE QUOTE</h2>
          <p style="font-size:12px;color:#6b7280;margin-bottom:20px">Quotation Reference: <strong>${qNum}</strong></p>
          <p style="font-size:13px;line-height:1.7;margin-bottom:16px">Thank you for requesting us to provide a quote for the above mentioned. We are pleased to submit the quote</p>
          <table style="${PDF_TABLE}">
            <thead><tr><th style="${PDF_TH}">Owner's Name</th><th style="${PDF_TH}">Vehicle Make</th><th style="${PDF_TH}">Year of Manufacture</th></tr></thead>
            <tbody><tr><td style="${PDF_TD}">${name}</td><td style="${PDF_TD}">${fd.make} ${fd.model}</td><td style="${PDF_TD}">${fd.year}</td></tr></tbody>
          </table>
          <table style="${PDF_TABLE}">
            <thead><tr><th style="${PDF_TH}">Registration Number</th><th style="${PDF_TH}">Vehicle Estimated Price</th><th style="${PDF_TH}">Phone Number</th></tr></thead>
            <tbody><tr><td style="${PDF_TD}">${fd.reg}</td><td style="${PDF_TD}">${fmt(value)}</td><td style="${PDF_TD}">${fd.phone}</td></tr></tbody>
          </table>
          <table style="${PDF_TABLE}">
            <thead><tr><th colspan="3" style="${PDF_TH}background:${cfg.color};color:#fff;border-color:${cfg.color}">PREMIUM &amp; RATES BREAKDOWN</th></tr></thead>
            <tbody>
              <tr><td style="${PDF_TD}">Estimated Vehicle Value</td><td style="${PDF_TD}">—</td><td style="${PDF_TD}text-align:right;font-weight:bold">${fmt(value)}</td></tr>
              <tr><td style="${PDF_TD}">Basic Annual Premium</td><td style="${PDF_TD}">@ ${cfg.label}</td><td style="${PDF_TD}text-align:right">${fmt(p.basic)}</td></tr>
              <tr><td style="${PDF_TD}">PVT Premium</td><td style="${PDF_TD}">Included</td><td style="${PDF_TD}text-align:right">KES 0</td></tr>
              <tr><td style="${PDF_TD}">Windscreen Premium</td><td style="${PDF_TD}">Included</td><td style="${PDF_TD}text-align:right">KES 0</td></tr>
              <tr><td style="${PDF_TD}">Radio / Cassette Premium</td><td style="${PDF_TD}">Included</td><td style="${PDF_TD}text-align:right">KES 0</td></tr>
              <tr><td style="${PDF_TD}">IRA Levy (Training Levy)</td><td style="${PDF_TD}">@ 0.5%</td><td style="${PDF_TD}text-align:right">${fmt(p.tax)}</td></tr>
              <tr><td style="${PDF_TD}font-weight:bold">Total Premium (incl. taxes)</td><td style="${PDF_TD}">—</td><td style="${PDF_TD}text-align:right;font-weight:bold">${fmt(p.total)}</td></tr>
            </tbody>
          </table>
          <table style="${PDF_TABLE}">
            <thead><tr><th colspan="3" style="${PDF_TH}">INSURER RATES COMPARISON</th></tr></thead>
            <tbody>
              ${['GA','BRITAM','SANLAM'].map(k => `<tr><td style="${PDF_TD}">${RATES[k].name}</td><td style="${PDF_TD}text-align:right">@ ${RATES[k].label}</td><td style="${PDF_TD}text-align:right">${fmt(prems[k].total)}</td></tr>`).join('')}
            </tbody>
          </table>
          <p style="font-size:13px;line-height:1.7;margin-bottom:16px">Quotation valid for fifteen (15) days · Subject to standard policy terms and conditions</p>
          <p style="font-size:13px;line-height:1.7;margin-bottom:16px">For and on behalf of ${cfg.name}</p>
        </div>
        <div style="padding:20px 36px;background:#f8f9fb;border-top:1px solid #dde3ed;font-size:11px;color:#6b7280;line-height:1.8">KENYA | UGANDA | TANZANIA | Rate subject to satisfactory valuation report</div>
      </div>`;
  }).join('');

  const wrap = document.getElementById('pdf-preview');
  wrap.innerHTML = pages;
  wrap.style.display = 'block';
  setTimeout(() => window.print(), 150);
}

// ── INIT ──
document.addEventListener('DOMContentLoaded', init);
</script>
@endpush

</x-app-layout>
