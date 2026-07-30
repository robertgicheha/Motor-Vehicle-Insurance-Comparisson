@inject('companyModel', 'App\Models\Company')
@inject('settingModel', 'App\Models\Setting')
@inject('quoteModel', 'App\Models\Quotation')
@php
    $__companies = $companies ?? $companyModel->active()->with('rates')->get();
    $__compareAllLogo = $settingModel->getValue('compare_all_logo');
    $__compareAllLogoUrl = $__compareAllLogo ? asset('storage/' . $__compareAllLogo) : asset('images/benchmarking.png');
    $__companyStats = $companyStats ?? $quoteModel->selectRaw('company_id, COUNT(*) as total, SUM(total_premium) as total_premium')
        ->whereIn('company_id', $__companies->pluck('id'))
        ->groupBy('company_id')
        ->get()
        ->keyBy('company_id');
    $__totalQuotes = $__companyStats->sum('total');
    $companyColors = ['#003087', '#0066B3', '#00205B', '#D0021B', '#7c3aed', '#0f7a65'];
    $__vehicleType = request()->query('vehicle_type', 'psv');

    $__buildData = function($companies, $vehicleType) use ($companyColors, $__companyStats) {
        return $companies->filter(function($c) use ($vehicleType) {
            return in_array($c->company_type, ['both', $vehicleType]);
        })->values()->map(function ($c, $i) use ($companyColors, $__companyStats, $vehicleType) {
            $rate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'Premium Rate');
            $iraLevy = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'IRA Levy');
            $pvtRate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'PVT Rate');
            $pllRate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'PLL Per Person');
            $maxAgeRate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'Max Age New Business');
            $stats = $__companyStats->get($c->id);
            return [
                'slug' => $c->slug,
                'name' => $c->name,
                'logo' => $c->logoUrl(),
                'rate' => $rate ? (float) $rate->percentage / 100 : 0,
                'taxRate' => $iraLevy ? (float) $iraLevy->percentage / 100 : 0.005,
                'minPremium' => $rate ? (float) $rate->min_premium : 0,
                'isPvtInclusive' => $rate ? (bool) $rate->is_pvt_inclusive : false,
                'pvtRate' => $pvtRate ? (float) $pvtRate->percentage / 100 : 0,
                'pvtMinValue' => $pvtRate ? (float) $pvtRate->min_value : 0,
                'pllPerPerson' => $pllRate ? (float) $pllRate->value : 0,
                'maxAge' => $maxAgeRate ? (int) $maxAgeRate->value : 99,
                'minSumInsured' => $vehicleType === 'psv' ? (float) $c->min_sum_insured_psv : (float) $c->min_sum_insured_private,
                'color' => $companyColors[$i % count($companyColors)],
                'prefix' => $c->getInitials(),
                'logoFallback' => $c->logoUrl(),
                'quote_count' => $stats ? $stats->total : 0,
                'total_premium' => $stats ? $stats->total_premium : 0,
            ];
        });
    };
    $__companyData = $__buildData($__companies, $__vehicleType);
    $__privateCompanyData = $__buildData($__companies, 'private');
    $__psvCompanyData = $__buildData($__companies, 'psv');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>
@if($mode === 'single' && $selectedCompany)
{{ $selectedCompany->name }}
@else
Compare All Insurers
@endif
 | Motor Quote | MIC Global Risks</title>
<meta name="description" content="Get an instant motor insurance quote from {{ $mode === 'single' && $selectedCompany ? $selectedCompany->name : 'Kenya\'s leading insurers' }}. Compare premiums and download your quotation.">
<link rel="canonical" href="{{ url('/generate-quote/' . ($mode === 'compare' ? 'compare-all' : ($selectedCompany?->slug ?? ''))) }}">
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
 <link rel="icon" type="image/x-icon" href="{{ asset('Favicon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('Favicon.png') }}">
@vite(['resources/css/app.css'])
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<x-public-header/>

<!-- ══════════════════════════════════════════════
     HERO  –  drop-in replacement for <section class="hero">
     Requires: hero_css.css appended to style.css
               script_additions.js inside <script>
     4 slides auto-advance every 5 s, infinite loop
     ══════════════════════════════════════════════ -->
<section class="hero">

  <!-- ── slide track ── -->
  <div class="hero-carousel" id="heroCarousel">

    <!-- Slide 1 -->
    {{-- <div class="hero-slide" style="background-image:url('{{ asset('images/hero/vehicle.jpg') }}')">
      <div class="hero-slide-overlay"></div>
    </div> --}}

      <!-- Slide 4 -->
    <div class="hero-slide" style="background-image:url('{{ asset('images/hero/vehicle.jpg') }}')">
      <div class="hero-slide-overlay"></div>
    </div>


    <!-- Slide 2 -->
    <div class="hero-slide" style="background-image:url('{{ asset('images/hero/family-car.jpg') }}')">
      <div class="hero-slide-overlay"></div>
    </div>

    <!-- Slide 3 -->
    <div class="hero-slide" style="background-image:url('{{ asset('images/hero/vehicle2.jpg') }}')">
      <div class="hero-slide-overlay"></div>
    </div>

     <!-- Slide 4 -->
    <div class="hero-slide" style="background-image:url('{{ asset('images/hero/vehicle.jpg') }}')">
      <div class="hero-slide-overlay"></div>
    </div>


  </div><!-- /hero-carousel -->

  <!-- ── content (sits above carousel) ── -->
  <div class="hero-content">
    <div class="hero-eyebrow">Motor Insurance</div>
    <h1>Protect Your <span>Vehicle</span><br>With Confidence</h1>
    <p>Get an instant comprehensive motor insurance quote from Kenya's leading insurers. Compare premiums, download your quotation and drive worry-free.</p>
    <div class="hero-badges">
      <!-- <span class="hero-badge">✓ Comprehensive Cover</span> -->
      <span class="hero-badge">✓ Instant Quote</span>
      <span class="hero-badge">✓ Kenya-Wide Coverage</span>
    </div>
    <button class="btn btn-red hero-cta" onclick="document.querySelector('.section').scrollIntoView({behavior:'smooth'})">Get a Quote ↓</button>
  </div>

  <!-- ── dot indicators ── -->
  <div class="hero-dots" id="heroDots">
    <button class="hero-dot active" onclick="heroGoTo(0)" aria-label="Slide 1"></button>
    <button class="hero-dot" onclick="heroGoTo(1)" aria-label="Slide 2"></button>
    <button class="hero-dot" onclick="heroGoTo(2)" aria-label="Slide 3"></button>
    <button class="hero-dot" onclick="heroGoTo(3)" aria-label="Slide 4"></button>
  </div>

  <!-- ── prev / next arrows ── -->
  <!-- <button class="hero-arrow hero-arrow-prev" onclick="heroStep(-1)" aria-label="Previous slide">&#8249;</button> -->
  <button class="hero-arrow hero-arrow-next" onclick="heroStep(1)"  aria-label="Next slide">&#8250;</button>

</section>

<!-- ══════════════════════════════════════════════
     SEARCH BAR  –  drop-in replacement for
     <section class="search-section">
     ══════════════════════════════════════════════ -->
<section class="search-section">
  <div class="search-wrap">

    <!-- icon + input + button -->
    <div class="search-bar" style="position:relative">
      <span class="search-icon">
        <!-- magnifier SVG inline so no extra asset needed -->
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.2"
             stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
      </span>
      <input type="text"
             placeholder="Search by quotation number, registration number, vehicle..."
             id="searchInput"
             autocomplete="off"
             oninput="handleSearchInput()"
             onfocus="handleSearchInput()">
      <button onclick="doSearch()">Search</button>

      <!-- suggestions dropdown -->
      <div id="searchSuggestions" class="search-suggestions" style="display:none"></div>
    </div>

    <!-- chips below the bar -->
    <div class="category-chips">
      <span class="chip active" onclick="setChip(this,'registration')">Registration No</span>
      <span class="chip"        onclick="setChip(this,'quotation')">Quotation No</span>
      <span class="chip"        onclick="setChip(this,'vehicle')">Vehicle Number</span>
    </div>

  </div>
</section>

<!-- ── SEARCH RESULTS ── -->
<section class="search-results-section" id="searchResults" style="display:none">
  <div class="search-results-inner">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
      <h3 style="font-size:16px;font-weight:700;color:var(--text)">Search Results</h3>
      <button onclick="closeSearchResults()" style="background:none;border:none;font-size:20px;cursor:pointer;color:var(--text-muted)">&times;</button>
    </div>
    <div id="searchResultsGrid" class="search-results-grid"></div>
  </div>
</section>

<!-- QUOTE FORM -->
<section class="section" style="background:#F9F9FF">
  <div class="form-container">
    <div class="section-head">
      <div class="section-title" style="text-align:center;">
        @if($mode === 'single' && $selectedCompany)
          {{ $selectedCompany->name }} — Quote
        @elseif($mode === 'compare')
          Compare All Insurers
        @else
          Get Your Motor Insurance Quote
        @endif
      </div>
      <div class="section-sub" style="text-align:center;">
        @if($mode === 'single' && $selectedCompany)
          Rate: <strong>{{ $selectedCompany->rates->firstWhere('item_name', 'Premium Rate') ? (float)$selectedCompany->rates->firstWhere('item_name', 'Premium Rate')->percentage : '—' }}%</strong> of vehicle value
        @else
          Complete the form below in three easy steps to receive your personalised quotation.
        @endif
      </div>
    </div>

    <!-- Vehicle Type Selector -->
    <div style="display:flex;gap:12px;justify-content:center;margin-bottom:24px">
      <a href="{{ url()->current() }}?vehicle_type=private"
         style="padding:10px 32px;border-radius:8px;font-weight:700;font-size:15px;text-decoration:none;transition:all .2s;
         {{ $__vehicleType === 'private' ? 'background:#003087;color:#fff;box-shadow:0 2px 12px rgba(0,48,135,0.3)' : 'background:#e5e7eb;color:#374151' }}">
        🚗 Private Vehicle
      </a>
      <a href="{{ url()->current() }}?vehicle_type=psv"
         style="padding:10px 32px;border-radius:8px;font-weight:700;font-size:15px;text-decoration:none;transition:all .2s;
         {{ $__vehicleType === 'psv' ? 'background:#D0021B;color:#fff;box-shadow:0 2px 12px rgba(208,2,27,0.3)' : 'background:#e5e7eb;color:#374151' }}">
        🚌 PSV (Public Service Vehicle)
      </a>
    </div>

    <!-- Step indicators -->
    <div class="form-steps">
      <div class="form-step active" id="step-tab-1" onclick="goStep(1)">
        <div class="step-num">Step 1</div>
        <div class="step-name">Personal Details</div>
      </div>
      <div class="form-step" id="step-tab-2" onclick="goStep(2)">
        <div class="step-num">Step 2</div>
        <div class="step-name">Vehicle Details</div>
      </div>
      <div class="form-step" id="step-tab-3" onclick="goStep(3)">
        <div class="step-num">Step 3</div>
        <div class="step-name">Quote Generation</div>
      </div>
    </div>

    <!-- Step 1: Personal Details -->
    <div class="form-panel active" id="panel-1">
      <div class="form-grid three">
        <div class="form-group">
          <label>Salutation <span class="req">*</span></label>
          <select id="f-salutation">
            <option value="">Select...</option>
            <option>Mr.</option><option>Mrs.</option><option>Miss</option><option>Dr.</option><option>Prof.</option>
          </select>
        </div>
        <div class="form-group">
          <label>First Name <span class="req">*</span></label>
          <input type="text" id="f-first" placeholder="e.g. John">
        </div>
        <div class="form-group">
          <label>Middle Name</label>
          <input type="text" id="f-middle" placeholder="Optional">
        </div>
        <div class="form-group">
          <label>Last Name <span class="req">*</span></label>
          <input type="text" id="f-last" placeholder="e.g. Doe">
        </div>
        <div class="form-group">
          <label>Phone Number <span class="req">*</span></label>
          <input type="tel" id="f-phone" placeholder="07 XX XXX XXX">
        </div>
        <div class="form-group">
          <label>Email Address <span class="req">*</span></label>
          <input type="email" id="f-email" placeholder="email@example.com">
        </div>
      </div>
      <div class="btn-group">
        <button class="btn btn-primary" onclick="goStep(2)">Continue to Vehicle Details →</button>
      </div>
    </div>

    <!-- Step 2: Vehicle Details -->
    <div class="form-panel" id="panel-2">
      <div class="form-grid">
        <div class="form-group">
          <label>Registration Number <span class="req">*</span></label>
          <input type="text" id="f-reg" placeholder="e.g. KDH 724V">
        </div>
        <div class="form-group">
          <label>Chassis / Frame Number</label>
          <input type="text" id="f-chassis" placeholder="e.g. WVWZZZ1KZ9W123456">
        </div>
        <div class="form-group">
          <label>Make of Vehicle <span class="req">*</span></label>
          <div class="vehicle-search-wrap">
            <input type="text" id="f-make" placeholder="Search vehicle make..." autocomplete="off">
            <div class="vehicle-dropdown" id="f-make-dropdown"></div>
          </div>
          <input type="hidden" id="f-make-value" value="">
          <input type="text" id="f-make-other" placeholder="Type your vehicle make..." style="display:none;margin-top:8px">
        </div>
        <div class="form-group">
          <label>Model Name <span class="req">*</span></label>
          <div class="vehicle-search-wrap">
            <input type="text" id="f-model" placeholder="Select a make first..." autocomplete="off" disabled>
            <div class="vehicle-dropdown" id="f-model-dropdown"></div>
          </div>
          <input type="hidden" id="f-model-value" value="">
          <input type="text" id="f-model-other" placeholder="Type your vehicle model..." style="display:none;margin-top:8px">
        </div>
        <div class="form-group">
          <label>Body Type</label>
          <select id="f-body">
            <option value="">Select body type...</option>
            <option>Saloon</option><option>Station Wagon</option><option>SUV/4x4</option>
            <option>Hatchback</option><option>Pick-Up</option><option>Van/Minivan</option><option>Convertible</option><option>Coupe</option>
          </select>
        </div>
        <div class="form-group">
          <label>Year of Manufacture <span class="req">*</span></label>
          <select id="f-year"><option value="">Select year...</option></select>
        </div>
        <div class="form-group">
          <label>Color</label>
          <select id="f-color" onchange="toggleOtherColor(this)">
            <option value="">Select color...</option>
            <option>White</option><option>Black</option><option>Silver/Grey</option><option>Blue</option>
            <option>Red</option><option>Brown/Maroon</option><option>Gold/Champagne</option><option>Green</option>
            <option>Orange</option><option>Purple</option><option>Other</option>
          </select>
          <input type="text" id="f-color-other" placeholder="Type your vehicle color..." style="display:none;margin-top:8px">
        </div>
        <div class="form-group">
          <label>Value of Vehicle (KES) <span class="req">*</span></label>
          <input type="text" id="f-value" placeholder="e.g. 800,000" autocomplete="off">
          <div class="value-range-hint" id="f-value-hint">
            @if($mode === 'single' && $selectedCompany)
              @php
                $__minVal = $minSumInsured ?? 0;
                $__maxVal = $maxSumInsured ?? 10000000;
              @endphp
              Enter between KES {{ number_format($__minVal) }} and KES {{ number_format($__maxVal) }}
            @else
              Enter between KES 800,000 and KES 10,000,000
            @endif
          </div>
          <div class="value-error" id="f-value-error" style="display:none"></div>
        </div>
      </div>
      <div class="btn-group">
        <button class="btn btn-secondary" onclick="goStep(1)">← Back</button>
        <button class="btn btn-primary" onclick="goStep(3)">Continue to Quote Generation →</button>
      </div>
    </div>

    <!-- Step 3: Quote Generation -->
    <div class="form-panel" id="panel-3">
      <div class="valuation-box">
        <h3 style="font-size:15px;font-weight:700;margin-bottom:4px;color:var(--text)">Premium Estimate</h3>
        <p style="font-size:13px;color:var(--text-muted);margin-bottom:16px">
          @if($mode === 'single' && $selectedCompany)
            Your quote from <strong>{{ $selectedCompany->name }}</strong> is ready.
          @else
            Compare premiums from all insurers below.
          @endif
        </p>

        <div id="premium-preview-empty" style="padding:20px;text-align:center;color:var(--text-muted);font-size:13px;border:1.5px dashed var(--border);border-radius:var(--radius)">
          ← Go back to Step 2 and enter the vehicle value to see premium estimates.
        </div>

        <div id="premium-preview" style="display:none">
          <div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:14px">
            @if($mode === 'single' && $selectedCompany)
              Your {{ $selectedCompany->name }} quote
            @else
              Click a card to open the full quotation
            @endif
          </div>
          <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px">

            @if($mode === 'single' && $selectedCompany)
              @php $cd = $companyData->firstWhere('slug', $selectedCompany->slug); @endphp
              @if($cd)
              <div class="preview-card" onclick="guestGuard(function(){ openQuotePage('{{ $cd['slug'] }}'); })" style="background:white;border:2px solid {{ $cd['color'] }};border-radius:var(--radius);padding:16px 14px;text-align:center;cursor:pointer;transition:box-shadow .2s,transform .15s">
                <div style="height:36px;display:flex;align-items:center;justify-content:center;margin-bottom:8px">
                  @if ($cd['logo'])
                  <img src="{{ $cd['logo'] }}" alt="{{ $cd['name'] }}" style="max-height:32px;max-width:90px;object-fit:contain">
                  @endif
                  <span style="font-size:11px;font-weight:800;color:{{ $cd['color'] }}">{{ strtoupper($cd['name']) }}</span>
                </div>
                <div style="font-size:11px;color:{{ $cd['color'] }};font-weight:700;margin-bottom:6px">{{ strtoupper($cd['name']) }}</div>
                <div style="font-size:1.1rem;font-weight:700;color:{{ $cd['color'] }}" id="prev-{{ $cd['slug'] }}">—</div>
                <div style="font-size:11px;color:var(--text-muted);margin-bottom:10px">@ {{ $cd['rate'] * 100 }}%</div>
                <div style="font-size:11px;font-weight:700;color:white;background:{{ $cd['color'] }};border-radius:5px;padding:5px 0">Generate Quote ↗</div>
              </div>
              @endif
            @else
              @foreach ($companyData as $cd)
              <div class="preview-card" onclick="guestGuard(function(){ openQuotePage('{{ $cd['slug'] }}'); })" style="background:white;border:1.5px solid var(--border);border-radius:var(--radius);padding:16px 14px;text-align:center;cursor:pointer;transition:box-shadow .2s,transform .15s">
                <div style="height:36px;display:flex;align-items:center;justify-content:center;margin-bottom:8px">
                  @if ($cd['logo'])
                  <img src="{{ $cd['logo'] }}" alt="{{ $cd['name'] }}" style="max-height:32px;max-width:90px;object-fit:contain">
                  @endif
                  <span style="font-size:11px;font-weight:800;color:{{ $cd['color'] }}">{{ strtoupper($cd['name']) }}</span>
                </div>
                <div style="font-size:11px;color:{{ $cd['color'] }};font-weight:700;margin-bottom:6px">{{ strtoupper($cd['name']) }}</div>
                <div style="font-size:1.1rem;font-weight:700;color:{{ $cd['color'] }}" id="prev-{{ $cd['slug'] }}">—</div>
                <div style="font-size:11px;color:var(--text-muted);margin-bottom:10px">@ {{ $cd['rate'] * 100 }}%</div>
                <div style="font-size:11px;font-weight:700;color:white;background:{{ $cd['color'] }};border-radius:5px;padding:5px 0">Generate Quote ↗</div>
              </div>
              @endforeach
            @endif

          </div>
        </div>
      </div>
      <div class="btn-group">
        <button class="btn btn-secondary" onclick="goStep(2)">← Back to Vehicle Details</button>
      </div>
    </div>

  </div>
</section>

<!-- ── QUOTE FORM (modern / New-Features style) ── -->
<section class="modern-section quote-form" style="display:none" id="quote-form-section">
  <div class="form-container">

    <button type="button" class="quote-form-back" onclick="closeQuoteFlow()">← Back</button>

    <div class="modern-head" style="text-align:center">
      <div class="eyebrow">Motor Insurance Quotation</div>
      <h2 class="modern-title" id="form-title">Get Your Motor Insurance Quote</h2>
      <h3 class="modern-subtitle" id="form-subtitle">Complete the form below in three easy steps to receive your personalised quotation.</h3>
      <p class="modern-desc" id="form-desc">Choose the cover type that matches your vehicle, then fill in your details to compare premiums from leading insurers.</p>
    </div>

    <!-- Vehicle-type toggle: Private vs PSV -->
    <div class="quote-type-toggle" role="tablist" aria-label="Motor vehicle quote type">
      <button type="button" class="quote-type-btn active" id="toggle-private" onclick="setVehicleType('private')" role="tab" aria-selected="true">
        Private Motor Vehicle Quote
      </button>
      <button type="button" class="quote-type-btn" id="toggle-psv" onclick="setVehicleType('psv')" role="tab" aria-selected="false">
        PSV Motor Vehicle Quote
      </button>
    </div>

    <input type="hidden" id="f-vehicle-type" value="private">

    <!-- Step indicators -->
    <div class="form-steps">
      <div class="form-step active" id="step-tab-1" onclick="goStep(1)">
        <div class="step-num">Step 1</div>
        <div class="step-name">Personal Details</div>
      </div>
      <div class="form-step" id="step-tab-2" onclick="goStep(2)">
        <div class="step-num">Step 2</div>
        <div class="step-name">Vehicle Details</div>
      </div>
      <div class="form-step" id="step-tab-3" onclick="goStep(3)">
        <div class="step-num">Step 3</div>
        <div class="step-name">Quote Generation</div>
      </div>
    </div>

    <!-- Step 1: Personal Details -->
    <div class="form-panel active" id="panel-1">
      <div class="form-grid three">
        <div class="form-group">
          <label>Salutation <span class="req">*</span></label>
          <select id="f-salutation">
            <option value="">Select...</option>
            <option>Mr.</option><option>Mrs.</option><option>Miss</option><option>Dr.</option><option>Prof.</option>
          </select>
        </div>
        <div class="form-group">
          <label>First Name <span class="req">*</span></label>
          <input type="text" id="f-first" placeholder="e.g. John">
        </div>
        <div class="form-group">
          <label>Middle Name</label>
          <input type="text" id="f-middle" placeholder="Optional">
        </div>
        <div class="form-group">
          <label>Last Name <span class="req">*</span></label>
          <input type="text" id="f-last" placeholder="e.g. Doe">
        </div>
        <div class="form-group">
          <label>Phone Number <span class="req">*</span></label>
          <input type="tel" id="f-phone" placeholder="07 XX XXX XXX">
        </div>
        <div class="form-group">
          <label>Email Address <span class="req">*</span></label>
          <input type="email" id="f-email" placeholder="email@example.com">
        </div>
      </div>
      <div class="btn-group">
        <button class="btn btn-primary" onclick="goStep(2)">Continue to Vehicle Details →</button>
      </div>
    </div>

    <!-- Step 2: Vehicle Details -->
    <div class="form-panel" id="panel-2">
      <div class="form-grid">
        <div class="form-group">
          <label>Registration Number <span class="req">*</span></label>
          <input type="text" id="f-reg" placeholder="e.g. KDH 724V" oninput="validateReg(this)">
          <div class="reg-error" id="f-reg-error" style="color:var(--red);font-size:11px;display:none;margin-top:2px">Invalid Kenyan registration format (e.g. KDH 291H)</div>
        </div>
        <div class="form-group" id="f-pax-group" style="display:none">
          <label>Number of Passengers (Pax) <span class="req">*</span></label>
          <input type="number" id="f-pax" placeholder="e.g. 14" min="1" value="1">
          <div style="font-size:11px;color:var(--text-muted);margin-top:2px">Used to calculate Passenger Legal Liability (PLL)</div>
        </div>
        <div class="form-group">
          <label>Chassis / Frame Number</label>
          <input type="text" id="f-chassis" placeholder="e.g. WVWZZZ1KZ9W123456">
        </div>
        <div class="form-group">
          <label>Make of Vehicle <span class="req">*</span></label>
          <div class="vehicle-search-wrap">
            <input type="text" id="f-make" placeholder="Search vehicle make..." autocomplete="off">
            <div class="vehicle-dropdown" id="f-make-dropdown"></div>
          </div>
          <input type="hidden" id="f-make-value" value="">
          <input type="text" id="f-make-other" placeholder="Type your vehicle make..." style="display:none;margin-top:8px">
        </div>
        <div class="form-group">
          <label>Model Name <span class="req">*</span></label>
          <div class="vehicle-search-wrap">
            <input type="text" id="f-model" placeholder="Select a make first..." autocomplete="off" disabled>
            <div class="vehicle-dropdown" id="f-model-dropdown"></div>
          </div>
          <input type="hidden" id="f-model-value" value="">
          <input type="text" id="f-model-other" placeholder="Type your vehicle model..." style="display:none;margin-top:8px">
        </div>
        <div class="form-group">
          <label>Body Type</label>
          <select id="f-body">
            <option value="">Select body type...</option>
            <option>Saloon</option><option>Station Wagon (S. Wagon)</option><option>SUV/4x4</option>
            <option>Hatchback</option><option>Pick-Up</option><option>Van/Minivan</option><option>Convertible</option><option>Coupe</option>
          </select>
        </div>
        <div class="form-group">
          <label>Year of Manufacture <span class="req">*</span></label>
          <select id="f-year">
            <option value="">Select year...</option>
          </select>
          <div class="value-error" id="f-year-error" style="display:none;color:var(--red);font-size:11px;margin-top:4px"></div>
        </div>
        <div class="form-group">
          <label>Color</label>
          <select id="f-color" onchange="toggleOtherColor(this)">
            <option value="">Select color...</option>
            <option>White</option>
            <option>Black</option>
            <option>Silver/Grey</option>
            <option>Blue</option>
            <option>Red</option>
            <option>Brown/Maroon</option>
            <option>Gold/Champagne</option>
            <option>Green</option>
            <option>Orange</option>
            <option>Purple</option>
            <option>Other</option>
          </select>
          <input type="text" id="f-color-other" placeholder="Type your vehicle color..." style="display:none;margin-top:8px">
        </div>
        <div class="form-group">
          <label>Value of Vehicle (KES) <span class="req">*</span></label>
          <input type="text" id="f-value" placeholder="e.g. 800,000" autocomplete="off">
          <div class="value-range-hint" id="f-value-hint">Enter the value of your vehicle</div>
          <div class="value-error" id="f-value-error" style="display:none"></div>
          <div class="min-sum-hint" id="f-min-sum-hint" style="font-size:11px;color:var(--text-muted);margin-top:4px"></div>
        </div>
      </div>
      <div class="btn-group">
        <button class="btn btn-secondary" onclick="goStep(1)">← Back</button>
        <button class="btn btn-primary" onclick="validateStep2AndProceed()">Continue to Quote Generation →</button>
      </div>
    </div>

    <!-- Step 3: Quote Generation -->
    <div class="form-panel" id="panel-3">
      <div class="valuation-box">
        <h3 style="font-size:15px;font-weight:700;margin-bottom:4px;color:var(--text)">Premium Estimate</h3>
        <p style="font-size:13px;color:var(--text-muted);margin-bottom:16px">Select an insurer card below to open its full quotation, or compare all side by side.</p>

        <div id="premium-preview-empty" style="padding:20px;text-align:center;color:var(--text-muted);font-size:13px;border:1.5px dashed var(--border);border-radius:var(--radius)">
          ← Go back to Step 2 and enter the vehicle value to see premium estimates.
        </div>

        <div id="premium-preview" style="display:none">
          <div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:14px">Click a card to open the full quotation</div>
          <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px" id="premium-cards-grid"></div>
        </div>
      </div>
      <div class="btn-group">
        <button class="btn btn-secondary" onclick="goStep(2)">← Back to Vehicle Details</button>
      </div>
    </div>

  </div>
</section>


<!-- Insuring a Private Vehicle? -->
<section class="section section-alt" style="background-color: #003087;">
  <div style="text-align:center;margin-bottom:20px">
    <div class="section-title" style="color:#F9F9FF;font-size:20px">🚗 Insuring a Private Vehicle?</div>
    <div class="section-sub" style="color:#F9F9FF;font-size:15px;margin-top:4px">Select an insurer below to generate a tailored quote for your private vehicle.</div>
  </div>
  <div class="company-cards">
    @foreach ($__privateCompanyData as $cd)
    <a href="{{ route('generate.quote', ['company' => $cd['slug'], 'vehicle_type' => 'private']) }}" class="company-card no-underline" style="cursor:pointer;display:block">
      <div class="company-logo-box">
        @if ($cd['logo'])
        <img src="{{ $cd['logo'] }}" alt="{{ $cd['name'] }}" class="nav-logo">
        @else
        <span style="font-size:11px;font-weight:800;color:{{ $cd['color'] }}">{{ strtoupper($cd['name']) }}</span>
        @endif
      </div>
      <div class="company-name">{{ $cd['name'] }}</div>
      <div class="company-rate">Rate: <strong>{{ $cd['rate'] * 100 }}%</strong> of vehicle value</div>
      <button class="btn btn-primary" style="width:100%;justify-content:center;font-size:11px;padding:5px 10px">Generate Quote ↗</button>
    </a>
    @endforeach

    <a href="{{ route('generate.quote', 'compare-all') }}" class="company-card no-underline" style="cursor:pointer;display:block;border:2px solid #D0021B;position:relative;overflow:hidden;transition:all .25s ease;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.08)">
      <div style="position:absolute;top:0;left:0;right:0;height:4px;background:#D0021B"></div>
      <div class="company-logo-box" style="height:48px;margin-bottom:12px">
        <img src="{{ $__compareAllLogoUrl }}" alt="Compare All" style="max-height:40px" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
        <span style="display:none;font-size:18px">📊</span>
      </div>
      <div class="company-name" style="font-size:14px;font-weight:700;margin-bottom:4px;color:#1a1a2e">Compare All</div>
      <div style="display:flex;align-items:center;justify-content:center;gap:16px;margin-bottom:12px">
        <div style="text-align:center">
          <div style="font-size:10px;color:#6b7280;font-weight:500">Insurers</div>
          <div style="font-size:13px;font-weight:800;color:#D0021B">{{ $__privateCompanyData->count() }}</div>
        </div>
      </div>
      <button class="btn btn-primary" style="width:100%;justify-content:center;font-size:12px;padding:8px 10px;background:#D0021B;border-color:#D0021B;border-radius:10px;font-weight:700">Compare All ↗</button>
    </a>
  </div>
</section>

<!-- WHY CHOOSE US -->
<section class="section" style="background:#fff">
  <div class="section-head" style="text-align:center;margin-bottom:40px">
    <div class="section-title">Why Choose Us?</div>
    <div class="section-sub">We make getting motor insurance simple, transparent, and tailored to your needs.</div>
  </div>
  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px;max-width:1100px;margin:0 auto;padding:0 20px">
    <div style="background:#F9F9FF;border-radius:16px;padding:32px 24px;text-align:center;transition:transform .2s,box-shadow .2s" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 25px rgba(0,0,0,0.08)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
      <div style="width:52px;height:52px;background:#003087;border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:24px">⚡</div>
      <h3 style="font-size:16px;font-weight:700;margin-bottom:8px;color:#1a1a2e">Instant Quotes</h3>
      <p style="font-size:13px;color:#6b7280;line-height:1.6">Get your premium estimate in seconds. No paperwork, no waiting — just enter your details and see your quote instantly.</p>
    </div>
    <div style="background:#F9F9FF;border-radius:16px;padding:32px 24px;text-align:center;transition:transform .2s,box-shadow .2s" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 25px rgba(0,0,0,0.08)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
      <div style="width:52px;height:52px;background:#003087;border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:24px">🔄</div>
      <h3 style="font-size:16px;font-weight:700;margin-bottom:8px;color:#1a1a2e">Side-by-Side Comparison</h3>
      <p style="font-size:13px;color:#6b7280;line-height:1.6">Compare premiums from Kenya's top insurers at a glance. Find the best rate and coverage for your vehicle.</p>
    </div>
    <div style="background:#F9F9FF;border-radius:16px;padding:32px 24px;text-align:center;transition:transform .2s,box-shadow .2s" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 25px rgba(0,0,0,0.08)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
      <div style="width:52px;height:52px;background:#003087;border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:24px">🔒</div>
      <h3 style="font-size:16px;font-weight:700;margin-bottom:8px;color:#1a1a2e">Trusted & Secure</h3>
      <p style="font-size:13px;color:#6b7280;line-height:1.6">Your data is safe with us. We work with IRM-authorized providers to ensure your information is always protected.</p>
    </div>
    <div style="background:#F9F9FF;border-radius:16px;padding:32px 24px;text-align:center;transition:transform .2s,box-shadow .2s" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 25px rgba(0,0,0,0.08)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
      <div style="width:52px;height:52px;background:#003087;border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:24px">📄</div>
      <h3 style="font-size:16px;font-weight:700;margin-bottom:8px;color:#1a1a2e">Downloadable Quotations</h3>
      <p style="font-size:13px;color:#6b7280;line-height:1.6">Generate and download a detailed PDF quotation instantly. Share it with your insurer or keep it for your records.</p>
    </div>
    <div style="background:#F9F9FF;border-radius:16px;padding:32px 24px;text-align:center;transition:transform .2s,box-shadow .2s" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 25px rgba(0,0,0,0.08)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
      <div style="width:52px;height:52px;background:#003087;border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:24px">💡</div>
      <h3 style="font-size:16px;font-weight:700;margin-bottom:8px;color:#1a1a2e">Tailored for You</h3>
      <p style="font-size:13px;color:#6b7280;line-height:1.6">Whether it's a private car or a PSV, we have the right cover options designed specifically for your vehicle type.</p>
    </div>
    <div style="background:#F9F9FF;border-radius:16px;padding:32px 24px;text-align:center;transition:transform .2s,box-shadow .2s" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 25px rgba(0,0,0,0.08)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
      <div style="width:52px;height:52px;background:#003087;border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:24px">📞</div>
      <h3 style="font-size:16px;font-weight:700;margin-bottom:8px;color:#1a1a2e">Dedicated Support</h3>
      <p style="font-size:13px;color:#6b7280;line-height:1.6">Our team is here to help. Reach out anytime for assistance with your quote, coverage questions, or claims support.</p>
    </div>
  </div>
</section>

<!-- Insuring a PSV? -->
<section class="section section-alt" style="background-color: #D0021B;">
  <div style="text-align:center;margin-bottom:20px">
    <div class="section-title" style="color:#F9F9FF;font-size:20px">🚌 Insuring a PSV?</div>
    <div class="section-sub" style="color:#F9F9FF;font-size:15px;margin-top:4px">Select an insurer below for a tailored PSV insurance quote.</div>
  </div>
  <div class="company-cards">
    @foreach ($__psvCompanyData as $cd)
    <a href="{{ route('generate.quote', ['company' => $cd['slug'], 'vehicle_type' => 'psv']) }}" class="company-card no-underline" style="cursor:pointer;display:block">
      <div class="company-logo-box">
        @if ($cd['logo'])
        <img src="{{ $cd['logo'] }}" alt="{{ $cd['name'] }}" class="nav-logo">
        @else
        <span style="font-size:11px;font-weight:800;color:{{ $cd['color'] }}">{{ strtoupper($cd['name']) }}</span>
        @endif
      </div>
      <div class="company-name">{{ $cd['name'] }}</div>
      <div class="company-rate">Rate: <strong>{{ $cd['rate'] * 100 }}%</strong> of vehicle value</div>
      <button class="btn btn-primary" style="width:100%;justify-content:center;font-size:11px;padding:5px 10px">Generate Quote ↗</button>
    </a>
    @endforeach

    <a href="{{ route('generate.quote', 'compare-all') }}" class="company-card no-underline" style="cursor:pointer;display:block;border:2px solid #D0021B;position:relative;overflow:hidden;transition:all .25s ease;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.08)">
      <div style="position:absolute;top:0;left:0;right:0;height:4px;background:#D0021B"></div>
      <div class="company-logo-box" style="height:48px;margin-bottom:12px">
        <img src="{{ $__compareAllLogoUrl }}" alt="Compare All" style="max-height:40px" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
        <span style="display:none;font-size:18px">📊</span>
      </div>
      <div class="company-name" style="font-size:14px;font-weight:700;margin-bottom:4px;color:#1a1a2e">Compare All</div>
      <div style="display:flex;align-items:center;justify-content:center;gap:16px;margin-bottom:12px">
        <div style="text-align:center">
          <div style="font-size:10px;color:#6b7280;font-weight:500">Insurers</div>
          <div style="font-size:13px;font-weight:800;color:#D0021B">{{ $__psvCompanyData->count() }}</div>
        </div>
      </div>
      <button class="btn btn-primary" style="width:100%;justify-content:center;font-size:12px;padding:8px 10px;background:#D0021B;border-color:#D0021B;border-radius:10px;font-weight:700">Compare All ↗</button>
    </a>
  </div>
</section>

<!-- RETRIEVE EXISTING QUOTE -->
<section class="section">
  <div class="section-head" style="text-align:center">
    <div class="section-title">Retrieve Existing Quote</div>
    <div class="section-sub">Already received a quotation number? Enter it below to regenerate your quote.</div>
  </div>
  <div class="regen-box">
    <div class="regen-icon">🔍</div>
    <h3>Regenerate Your Quotation</h3>
    <p>Enter your unique quotation number to retrieve your insurance quote.</p>
    <form method="POST" action="{{ route('motor.lookup.quotation') }}" style="display:contents">
      @csrf
      <div class="regen-input-row">
        <input type="text" name="number" placeholder="e.g. GA-2026/001" required>
        <button type="submit" class="btn btn-primary">Retrieve →</button>
      </div>
    </form>
  </div>
</section>

<x-auth-modal/>
<x-public-footer/>

<script>
window.MIC_COMPANIES = @json($companyData);
window.MIC_PRIVATE_COMPANIES = @json($__privateCompanyData);
window.MIC_PSV_COMPANIES = @json($__psvCompanyData);
window.MIC_GUEST = @json(auth()->guest());
window.MIC_MODE = @json($mode);
window.MIC_SELECTED_COMPANY = @json($selectedCompany?->slug);
window.MIC_VEHICLE_TYPE = @json($__vehicleType);

function guestGuard(callback) {
  if (window.MIC_GUEST) {
    if (typeof callback === 'function') {
      var match = callback.toString().match(/openQuotePage\('([^']+)'\)/);
      if (match) {
        sessionStorage.setItem('pending_quote_company', match[1]);
        try {
          var d = getFormData();
          sessionStorage.setItem('micQuoteData', JSON.stringify({...d, mode: match[1]}));
        } catch(e) {}
      }
    }
    sessionStorage.setItem('auth_redirect', '/dashboard?pending_quote=1');
    var alpineEl = document.querySelector('#authModal');
    if (alpineEl && alpineEl.__x) { alpineEl.__x.$data.redirectTo = '/dashboard?pending_quote=1'; }
    openAuthModal();
    return;
  }
  if (typeof callback === 'function') callback();
}

// Populate year select
(function(){
  var sel = document.getElementById('f-year');
  if(sel) for(var y=2026;y>=1990;y--){ var o=document.createElement('option'); o.value=y; o.textContent=y; sel.appendChild(o); }
})();
</script>
<script src="{{ asset('js/home.js') }}"></script>
</body>
</html>
