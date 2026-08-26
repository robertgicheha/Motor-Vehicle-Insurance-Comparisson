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
    $__companyData = $__companies->map(function ($c, $i) use ($companyColors, $__companyStats, $__vehicleType) {
        $rate = $c->rates->where('vehicle_type', $__vehicleType)->firstWhere('item_name', 'Premium Rate');
        $iraLevy = $c->rates->where('vehicle_type', $__vehicleType)->firstWhere('item_name', 'IRA Levy');
        $pvtRate = $c->rates->where('vehicle_type', $__vehicleType)->firstWhere('item_name', 'PVT Rate');
        $pllRate = $c->rates->where('vehicle_type', $__vehicleType)->firstWhere('item_name', 'PLL Per Person');
        $maxAgeRate = $c->rates->where('vehicle_type', $__vehicleType)->firstWhere('item_name', 'Max Age New Business');
        $stats = $__companyStats->get($c->id);
        return [
            'slug' => $c->slug,
            'name' => $c->name,
            'logo' => $c->logoUrl(),
            'rate' => $rate ? (float) $rate->percentage / 100 : 0,
            'taxRate' => 0,
            'minPremium' => $rate ? (float) $rate->min_premium : 0,
            'isPvtInclusive' => $rate ? (bool) $rate->is_pvt_inclusive : false,
            'pvtRate' => $pvtRate ? (float) $pvtRate->percentage / 100 : 0,
            'pvtMinValue' => $pvtRate ? (float) $pvtRate->min_value : 0,
            'pllPerPerson' => $pllRate ? (float) $pllRate->value : 0,
            'maxAge' => $maxAgeRate ? (int) $maxAgeRate->value : 99,
            'color' => $companyColors[$i % count($companyColors)],
            'prefix' => $c->getInitials(),
            'logoFallback' => $c->logoUrl(),
        'quote_count' => $stats ? $stats->total : 0,
        'total_premium' => $stats ? $stats->total_premium : 0,
    ];
})->values();

    $__privateCompanies = $__companies->whereIn('company_type', ['private','both']);
    $__privateCompanyData = $__privateCompanies->values()->map(function ($c, $i) use ($companyColors, $__companyStats) {
        $rate = $c->rates->where('vehicle_type', 'private')->firstWhere('item_name', 'Premium Rate');
        $pvtRate = $c->rates->where('vehicle_type', 'private')->firstWhere('item_name', 'PVT Rate');
        $pllRate = $c->rates->where('vehicle_type', 'private')->firstWhere('item_name', 'PLL Per Person');
        $maxAgeRate = $c->rates->where('vehicle_type', 'private')->firstWhere('item_name', 'Max Age New Business');
        $stats = $__companyStats->get($c->id);
        return [
            'slug' => $c->slug,
            'name' => $c->name,
            'logo' => $c->logoUrl(),
            'rate' => $rate ? (float) $rate->percentage / 100 : 0,
            'taxRate' => 0,
            'minPremium' => $rate ? (float) $rate->min_premium : 0,
            'isPvtInclusive' => $rate ? (bool) $rate->is_pvt_inclusive : false,
            'pvtRate' => $pvtRate ? (float) $pvtRate->percentage / 100 : 0,
            'pvtMinValue' => $pvtRate ? (float) $pvtRate->min_value : 0,
            'pllPerPerson' => $pllRate ? (float) $pllRate->value : 0,
            'maxAge' => $maxAgeRate ? (int) $maxAgeRate->value : 99,
            'color' => $companyColors[$i % count($companyColors)],
            'prefix' => $c->getInitials(),
            'logoFallback' => $c->logoUrl(),
            'quote_count' => $stats ? $stats->total : 0,
            'total_premium' => $stats ? $stats->total_premium : 0,
        ];
    })->values();
    $__psvCompanyData = $__companyData->filter(fn($cd) => in_array($cd['slug'], $__companies->whereIn('company_type', ['psv','both'])->pluck('slug')->toArray()))->values();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>

<!-- ═══════════════════════════════════════
     CHARSET & VIEWPORT
     ═══════════════════════════════════════ -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<!-- ═══════════════════════════════════════
     PRIMARY META
     ═══════════════════════════════════════ -->

<title>Motor Vehicle Insurance Portal | MIC Global Risks</title>
<meta name="description"
      content="Get an instant motor insurance quote in Kenya. Compare comprehensive premiums from {{ $__companies->pluck('name')->implode(', ') }}. Download your quotation in seconds.">
<meta name="keywords"
      content="motor insurance Kenya, car insurance quote Kenya, comprehensive motor cover, {{ $__companies->pluck('name')->implode(', ') }}, vehicle insurance Nairobi, third party insurance Kenya">
<meta name="author" content="MIC Global Risks Insurance">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<meta name="theme-color" content="#0066B3">
<link rel="canonical" href="https://www.micglobalrisks.com/motor-quote/">

<!-- ═══════════════════════════════════════
     OPEN GRAPH  (Facebook / LinkedIn / WhatsApp)
     ═══════════════════════════════════════ -->
<meta property="og:type"        content="website">
<meta property="og:site_name"   content="MIC Global Risks Insurance">
<meta property="og:title"       content="Motor Insurance Quote Portal | MIC Global Risks">
<meta property="og:description" content="Compare motor insurance premiums from {{ $__companies->pluck('name')->implode(', ') }}. Get your instant quote and download your certificate — all in one place.">
<meta property="og:url"         content="https://www.micglobalrisks.com/motor-quote/">
<meta property="og:image"       content="https://www.micglobalrisks.com/img/MIC-Global-Risks_logo.png">
<meta property="og:image:width"  content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt"   content="MIC Global Risks Motor Insurance Quote Portal">
<meta property="og:locale"      content="en_KE">

<!-- ═══════════════════════════════════════
     TWITTER / X CARD
     ═══════════════════════════════════════ -->
<meta name="twitter:card"        content="summary_large_image">
<meta name="twitter:site"        content="@MICGlobalRisks">
<meta name="twitter:creator"     content="@MICGlobalRisks">
<meta name="twitter:title"       content="Motor Insurance Quote Portal | MIC Global Risks">
<meta name="twitter:description" content="Compare motor insurance premiums from {{ $__companies->pluck('name')->implode(', ') }}. Instant quotes, downloadable certificates.">
<meta name="twitter:image"       content="https://www.micglobalrisks.com/img/MIC-Global-Risks_logo.png">
<meta name="twitter:image:alt"   content="MIC Global Risks Motor Insurance Quote Portal">

<!-- ═══════════════════════════════════════
     GEO / REGIONAL
     ═══════════════════════════════════════ -->
<meta name="geo.region"      content="KE">
<meta name="geo.placename"   content="Nairobi, Kenya">
<meta name="geo.position"    content="-1.286389;36.817223">
<meta name="ICBM"            content="-1.286389, 36.817223">
<meta name="language"        content="English">

<!-- ═══════════════════════════════════════
     FAVICONS
     Place these files in your /img/favicon/ folder.
     Use https://realfavicongenerator.net to generate
     all sizes from a single 512×512 PNG source logo.
     ═══════════════════════════════════════ -->

<!-- Standard favicon (browsers + bookmark bar) -->
<link rel="icon" type="image/x-icon"       href="{{ asset('Favicon.png') }}">
<link rel="icon" type="image/png" sizes="16x16"   href="{{ asset('Favicon.png') }}">
<link rel="icon" type="image/png" sizes="32x32"   href="{{ asset('Favicon.png') }}">
<link rel="icon" type="image/png" sizes="96x96"   href="{{ asset('Favicon.png') }}">

<!-- Apple touch icon (iOS home screen) -->
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('Favicon.png') }}">

<!-- Android / Chrome PWA -->
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('Favicon.png') }}">
<link rel="icon" type="image/png" sizes="512x512" href="{{ asset('Favicon.png') }}">

<!-- Web app manifest (enables "Add to Home Screen" on Android) -->
<link rel="manifest" href="{{ asset('site.webmanifest') }}">

<!-- Safari pinned tab (SVG monochrome silhouette of your logo) -->
<link rel="mask-icon" href="{{ asset('Favicon.png') }}" color="#0066B3">

<!-- Windows tile (optional, for pinned IE/Edge tiles) -->
<meta name="msapplication-TileColor"   content="#0066B3">
<meta name="msapplication-TileImage"   content="{{ asset('Favicon.png') }}">
<meta name="msapplication-config"      content="{{ asset('browserconfig.xml') }}">

<link rel="stylesheet" href="{{ asset('css/home.css') }}">


<!-- ═══════════════════════════════════════
     PERFORMANCE HINTS
     ═══════════════════════════════════════ -->
<!-- Pre-connect to font origins so they load faster -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<!-- Preload the first hero image so LCP is fast -->
<link rel="preload" as="image" href="{{ asset('images/hero/vehicle.jpg') }}" fetchpriority="high">

<!-- Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Stylesheet -->

<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@vite(['resources/css/app.css'])
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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

<!-- ── REGENERATE QUOTE CHOICE (only for logged-in users with a previous quotation) ── -->
@auth
  @php
    // ASSUMPTION: reusing the same relationship pattern as elsewhere ($user->quotations()).
    // Adjust column names below (salutation, middle_name, phone, email, registration_number,
    // chassis_number, car_make, car_model, body_type, year_of_manufacture, color, vehicle_value)
    // to match your actual Quotation model — these are best guesses.
    $__lastQuotation = auth()->user()->quotations()->latest()->first();
  @endphp
  @if ($__lastQuotation)
  <section class="section" style="background:#F9F9FF;padding-bottom:0">
    <div class="form-container" id="regenerate-choice">
      <div class="section-head">
        <div class="section-title" style="text-align:center">Welcome back{{ !empty($__lastQuotation->first_name) ? ', ' . $__lastQuotation->first_name : '' }}!</div>
        <div class="section-sub" style="text-align:center">We found a recent quotation on your account. Would you like to regenerate it for the same car, or start fresh?</div>
      </div>

      <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;max-width:720px;margin:24px auto 0">
        <div class="preview-card" onclick="regenerateSameCar()" style="background:white;border:1.5px solid var(--border);border-radius:var(--radius);padding:24px 18px;text-align:center;cursor:pointer;transition:box-shadow .2s,transform .15s">
          <div style="font-size:28px;margin-bottom:10px">🔁</div>
          <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:6px">Yes, regenerate this quote</div>
          <div style="font-size:12px;color:var(--text-muted);margin-bottom:14px">
            {{ trim(($__lastQuotation->car_make ?? '') . ' ' . ($__lastQuotation->car_model ?? '')) ?: 'Same car as your last quotation' }}
            @if(!empty($__lastQuotation->year_of_manufacture)) ({{ $__lastQuotation->year_of_manufacture }}) @endif
          </div>
          <div style="font-size:11px;font-weight:700;color:white;background:#1756c8;border-radius:5px;padding:7px 0">Use Same Car ↗</div>
        </div>

        <div class="preview-card" onclick="startNewQuote()" style="background:white;border:1.5px solid var(--border);border-radius:var(--radius);padding:24px 18px;text-align:center;cursor:pointer;transition:box-shadow .2s,transform .15s">
          <div style="font-size:28px;margin-bottom:10px">🚗</div>
          <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:6px">No, new car</div>
          <div style="font-size:12px;color:var(--text-muted);margin-bottom:14px">Start a fresh quotation from scratch</div>
          <div style="font-size:11px;font-weight:700;color:white;background:#005F9E;border-radius:5px;padding:7px 0">Start Fresh ↗</div>
        </div>
      </div>
    </div>
  </section>
  @endif
@endauth



<!-- ── QUOTE FORM (modern / New-Features style) ── -->
<section class="modern-section quote-form" id="quote-form-section">
  <div class="form-container">

    <button type="button" class="quote-form-back" onclick="document.getElementById('vehicle-type-section').scrollIntoView({behavior:'smooth', block:'start'})">↑ Back to Vehicle Selection</button>

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
      <div class="form-step active" id="step-tab-1" onclick="goStepMain(1)">
        <div class="step-num">Step 1</div>
        <div class="step-name">Personal Details</div>
      </div>
      <div class="form-step" id="step-tab-2" onclick="goStepMain(2)">
        <div class="step-num">Step 2</div>
        <div class="step-name">Vehicle Details</div>
      </div>
      <div class="form-step" id="step-tab-3" onclick="goStepMain(3)">
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
        <button class="btn btn-primary" onclick="goStepMain(2)">Continue to Vehicle Details →</button>
      </div>
    </div>

    <!-- Step 2: Vehicle Details -->
    <div class="form-panel" id="panel-2">
      <div class="form-grid">
        <div class="form-group">
          <label>Registration Number <span class="req">*</span></label>
          <input type="text" id="f-reg" placeholder="e.g. KDH 724V" oninput="validateRegMain(this)">
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
        <button class="btn btn-secondary" onclick="goStepMain(1)">← Back</button>
        <button class="btn btn-primary" onclick="validateStep2AndProceedMain()">Continue to Quote Generation →</button>
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
        <button class="btn btn-secondary" onclick="goStepMain(2)">← Back to Vehicle Details</button>
      </div>
    </div>

  </div>
</section>


<div class="mic-motor">


<!-- ============================================================ PSV MOTOR VEHICLE SECTION (modern / New-Features style, mirrored) ============================================================ -->
<section class="modern-section psv" id="psv-motor-section">
  <div class="modern-head">
    <div class="eyebrow" style="color:#004F8C">Comprehensive Motor Vehicle Insurance</div>
    <h2 class="modern-title" style="color:#004F8C">Insuring a PSV?</h2>
    <h3 class="modern-subtitle">Get a Comprehensive PSV (Public Service Vehicle) quote in minutes.</h3>
    <p class="modern-desc">Get comprehensive cover tailored for matatus, buses, taxis and other public service vehicles, with clear visibility into every quotation.</p>
  </div>
  <div class="modern-grid">
    <div>
      <div class="feature-list">
        <div class="feature-item">
          <div class="feature-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M13 2 3 14h8l-1 8 10-12h-8l1-8Z"/></svg>
          </div>
          <div>
            <h4>Tailored PSV Cover</h4>
            <p>Quotes built for the specific risks of matatus, buses, taxis and other public service vehicles.</p>
          </div>
        </div>
        <div class="feature-item">
          <div class="feature-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 3v18h18"/><path d="M7 15l4-5 3 3 5-7"/></svg>
          </div>
          <div>
            <h4>Compare Rates From Leading Insurers</h4>
            <p>Compare premiums across our panel of underwriters before you commit to a policy.</p>
          </div>
        </div>
        <div class="feature-item">
          <div class="feature-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
          </div>
          <div>
            <h4>Download Your Quote Instantly</h4>
            <p>Once you're happy with a rate, download your PSV quotation immediately as a PDF.</p>
          </div>
        </div>
      </div>
      <div class="modern-cta">
      <a class="btn-section-cta" href="#quote-form-section-psv" onclick="openQuoteFlow('psv'); return false;">Get a PSV Quote</a>
    </div>
    </div>
    <div class="modern-image">
      <img src="{{ asset('images/hero/family-car.jpg') }}" alt="PSV motor vehicle insurance"/>
    </div>
  </div>
</section>

<!-- ── PSV QUOTE FORM ── -->
<section class="modern-section quote-form" style="display:none" id="quote-form-section-psv">
  <div class="form-container">

    <button type="button" class="quote-form-back" onclick="closeQuoteFlow('psv')">↑ Back to PSV Section</button>

    <div class="modern-head" style="text-align:center">
      <div class="eyebrow">Motor Insurance Quotation</div>
      <h2 class="modern-title">Get Your PSV Motor Insurance Quote</h2>
      <h3 class="modern-subtitle">Complete the form below in three easy steps to receive your personalised quotation.</h3>
      <p class="modern-desc">Choose the cover type that matches your vehicle, then fill in your details to compare premiums from leading insurers.</p>
    </div>

    <input type="hidden" id="f-vehicle-type-psv" value="psv">

    <!-- Step indicators -->
    <div class="form-steps">
      <div class="form-step active" id="step-tab-1-psv" onclick="goStep('psv',1)">
        <div class="step-num">Step 1</div>
        <div class="step-name">Personal Details</div>
      </div>
      <div class="form-step" id="step-tab-2-psv" onclick="goStep('psv',2)">
        <div class="step-num">Step 2</div>
        <div class="step-name">Vehicle Details</div>
      </div>
      <div class="form-step" id="step-tab-3-psv" onclick="goStep('psv',3)">
        <div class="step-num">Step 3</div>
        <div class="step-name">Quote Generation</div>
      </div>
    </div>

    <!-- Step 1: Personal Details -->
    <div class="form-panel active" id="panel-1-psv">
      <div class="form-grid three">
        <div class="form-group">
          <label>Salutation <span class="req">*</span></label>
          <select id="f-salutation-psv">
            <option value="">Select...</option>
            <option>Mr.</option><option>Mrs.</option><option>Miss</option><option>Dr.</option><option>Prof.</option>
          </select>
        </div>
        <div class="form-group">
          <label>First Name <span class="req">*</span></label>
          <input type="text" id="f-first-psv" placeholder="e.g. John">
        </div>
        <div class="form-group">
          <label>Middle Name</label>
          <input type="text" id="f-middle-psv" placeholder="Optional">
        </div>
        <div class="form-group">
          <label>Last Name <span class="req">*</span></label>
          <input type="text" id="f-last-psv" placeholder="e.g. Doe">
        </div>
        <div class="form-group">
          <label>Phone Number <span class="req">*</span></label>
          <input type="tel" id="f-phone-psv" placeholder="07 XX XXX XXX">
        </div>
        <div class="form-group">
          <label>Email Address <span class="req">*</span></label>
          <input type="email" id="f-email-psv" placeholder="email@example.com">
        </div>
      </div>
      <div class="btn-group">
        <button class="btn btn-primary" onclick="goStep('psv',2)">Continue to Vehicle Details →</button>
      </div>
    </div>

    <!-- Step 2: Vehicle Details -->
    <div class="form-panel" id="panel-2-psv">
      <div class="form-grid">
        <div class="form-group">
          <label>Registration Number <span class="req">*</span></label>
          <input type="text" id="f-reg-psv" placeholder="e.g. KDH 724V" oninput="validateReg('psv', this)">
          <div class="reg-error" id="f-reg-error-psv" style="color:var(--red);font-size:11px;display:none;margin-top:2px">Invalid Kenyan registration format (e.g. KDH 291H)</div>
        </div>
        <div class="form-group" id="f-pax-group-psv">
          <label>Number of Passengers (Pax) <span class="req">*</span></label>
          <input type="number" id="f-pax-psv" placeholder="e.g. 14" min="1" value="1">
          <div style="font-size:11px;color:var(--text-muted);margin-top:2px">Used to calculate Passenger Legal Liability (PLL)</div>
        </div>
        <div class="form-group">
          <label>Chassis / Frame Number</label>
          <input type="text" id="f-chassis-psv" placeholder="e.g. WVWZZZ1KZ9W123456">
        </div>
        <div class="form-group">
          <label>Make of Vehicle <span class="req">*</span></label>
          <div class="vehicle-search-wrap">
            <input type="text" id="f-make-psv" placeholder="Search vehicle make..." autocomplete="off">
            <div class="vehicle-dropdown" id="f-make-dropdown-psv"></div>
          </div>
          <input type="hidden" id="f-make-value-psv" value="">
          <input type="text" id="f-make-other-psv" placeholder="Type your vehicle make..." style="display:none;margin-top:8px">
        </div>
        <div class="form-group">
          <label>Model Name <span class="req">*</span></label>
          <div class="vehicle-search-wrap">
            <input type="text" id="f-model-psv" placeholder="Select a make first..." autocomplete="off" disabled>
            <div class="vehicle-dropdown" id="f-model-dropdown-psv"></div>
          </div>
          <input type="hidden" id="f-model-value-psv" value="">
          <input type="text" id="f-model-other-psv" placeholder="Type your vehicle model..." style="display:none;margin-top:8px">
        </div>
        <div class="form-group">
          <label>Body Type</label>
          <select id="f-body-psv">
            <option value="">Select body type...</option>
            <option>Saloon</option><option>Station Wagon (S. Wagon)</option><option>SUV/4x4</option>
            <option>Hatchback</option><option>Pick-Up</option><option>Van/Minivan</option><option>Convertible</option><option>Coupe</option>
          </select>
        </div>
        <div class="form-group">
          <label>Year of Manufacture <span class="req">*</span></label>
          <select id="f-year-psv">
            <option value="">Select year...</option>
          </select>
          <div class="value-error" id="f-year-error-psv" style="display:none;color:var(--red);font-size:11px;margin-top:4px"></div>
        </div>
        <div class="form-group">
          <label>Color</label>
          <select id="f-color-psv" onchange="toggleOtherColor('psv', this)">
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
          <input type="text" id="f-color-other-psv" placeholder="Type your vehicle color..." style="display:none;margin-top:8px">
        </div>
        <div class="form-group">
          <label>Value of Vehicle (KES) <span class="req">*</span></label>
          <input type="text" id="f-value-psv" placeholder="e.g. 800,000" autocomplete="off">
          <div class="value-range-hint" id="f-value-hint-psv">Enter the value of your vehicle</div>
          <div class="value-error" id="f-value-error-psv" style="display:none"></div>
          <div class="min-sum-hint" id="f-min-sum-hint-psv" style="font-size:11px;color:var(--text-muted);margin-top:4px"></div>
        </div>
      </div>
      <div class="btn-group">
        <button class="btn btn-secondary" onclick="goStep('psv',1)">← Back</button>
        <button class="btn btn-primary" onclick="validateStep2AndProceed('psv')">Continue to Quote Generation →</button>
      </div>
    </div>

    <!-- Step 3: Quote Generation -->
    <div class="form-panel" id="panel-3-psv">
      <div class="valuation-box">
        <h3 style="font-size:15px;font-weight:700;margin-bottom:4px;color:var(--text)">Premium Estimate</h3>
        <p style="font-size:13px;color:var(--text-muted);margin-bottom:16px">Select an insurer card below to open its full quotation, or compare all side by side.</p>

        <div id="premium-preview-empty-psv" style="padding:20px;text-align:center;color:var(--text-muted);font-size:13px;border:1.5px dashed var(--border);border-radius:var(--radius)">
          ← Go back to Step 2 and enter the vehicle value to see premium estimates.
        </div>

        <div id="premium-preview-psv" style="display:none">
          <div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:14px">Click a card to open the full quotation</div>
          <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px" id="premium-cards-grid-psv"></div>
        </div>
      </div>
      <div class="btn-group">
        <button class="btn btn-secondary" onclick="goStep('psv',2)">← Back to Vehicle Details</button>
      </div>
    </div>

  </div>
</section>
 
<!-- ============================================================ PSV MOTOR STEPS ============================================================ -->
<section class="steps-section psv-steps" id="psv-steps-section">
  <div class="steps-head">
    <h2 style="color:#fff;">How to Get a PSV Motor Vehicle Quote</h2>
    <p>Five simple steps to your comprehensive quote — <a href="https://www.micglobalrisks.com/motor-quote/">start your quote here</a>.</p>
  </div>
  <div class="steps-indicator">
    <div class="indicator-item"><span class="indicator-dot"></span><span class="indicator-label">Step 1</span></div>
    <span class="indicator-line"></span>
    <div class="indicator-item"><span class="indicator-dot"></span><span class="indicator-label">Step 2</span></div>
    <span class="indicator-line"></span>
    <div class="indicator-item"><span class="indicator-dot"></span><span class="indicator-label">Step 3</span></div>
    <span class="indicator-line"></span>
    <div class="indicator-item"><span class="indicator-dot"></span><span class="indicator-label">Step 4</span></div>
    <span class="indicator-line"></span>
    <div class="indicator-item"><span class="indicator-dot"></span><span class="indicator-label">Step 5</span></div>
  </div>
  <div class="steps-grid">
    <div class="step-item">
      <div class="step-top">
        <span class="step-num">1</span>
        <span class="step-arrow">&#8594;</span>
      </div>
      <h4>Enter Your Personal Details</h4>
      <p>Input your name, contact details and ID/KRA PIN for the PSV owner or operator.</p>
    </div>
    <div class="step-item">
      <div class="step-top">
        <span class="step-num">2</span>
        <span class="step-arrow">&#8594;</span>
      </div>
      <h4>Input Your Vehicle Details</h4>
      <p>Add the PSV's make, model, seating capacity, registration and value.</p>
    </div>
    <div class="step-item">
      <div class="step-top">
        <span class="step-num">3</span>
        <span class="step-arrow">&#8594;</span>
      </div>
      <h4>Generate a Quotation</h4>
      <p>Our system instantly generates a comprehensive PSV cover quotation.</p>
    </div>
    <div class="step-item">
      <div class="step-top">
        <span class="step-num">4</span>
        <span class="step-arrow">&#8594;</span>
      </div>
      <h4>Compare Rates &amp; Premiums</h4>
      <p>Compare rates and premiums across our panel of leading underwriters.</p>
    </div>
    <div class="step-item step-item-last">
      <div class="step-top">
        <span class="step-num">5</span>
      </div>
      <h4>Download Quote</h4>
      <p>Download your preferred quotation instantly and proceed to cover.</p>
    </div>
  </div>
</section>

 
<!-- ============================================================ PRIVATE MOTOR VEHICLE SECTION (modern / New-Features style) ============================================================ -->
<section class="modern-section private" id="private-motor-section">
  <div class="modern-head">
    <div class="eyebrow">Comprehensive Motor Vehicle Insurance</div>
    <h2 class="modern-title">Insuring a Private Vehicle?</h2>
    <h3 class="modern-subtitle">Get a Comprehensive Private Motor Vehicle quote in minutes.</h3>
    <p class="modern-desc">Our motor quote portal makes it simple to get comprehensive cover for your private vehicle, compare rates, and get on the road with confidence.</p>
  </div>
  <div class="modern-grid">
    <div>
      <div class="feature-list">
        <div class="feature-item">
          <div class="feature-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M13 2 3 14h8l-1 8 10-12h-8l1-8Z"/></svg>
          </div>
          <div>
            <h4>Instant Online Quotation</h4>
            <p>Get a comprehensive private motor vehicle quote in minutes, without visiting a branch.</p>
          </div>
        </div>
        <div class="feature-item">
          <div class="feature-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 3v18h18"/><path d="M7 15l4-5 3 3 5-7"/></svg>
          </div>
          <div>
            <h4>Compare Rates From Leading Insurers</h4>
            <p>See side-by-side premiums so you can choose the cover that best suits your vehicle and budget.</p>
          </div>
        </div>
        <div class="feature-item">
          <div class="feature-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
          </div>
          <div>
            <h4>Download Your Quote Instantly</h4>
            <p>Once you're happy with a rate, download your quotation immediately as a PDF.</p>
          </div>
        </div>
      </div>
      <div class="modern-cta">
<a class="btn-section-cta" href="#quote-form-section-private" onclick="openQuoteFlow('private'); return false;">Get a Private Vehicle Quote</a>    </div>
    </div>
    <div class="modern-image">
      <img src="{{ asset('images/hero/vehicle.jpg') }}" alt="Private motor vehicle insurance"/>
    </div>
  </div>
</section>

<!-- ── PRIVATE QUOTE FORM ── -->
<section class="modern-section quote-form" style="display:none" id="quote-form-section-private">
  <div class="form-container">

    <button type="button" class="quote-form-back" onclick="closeQuoteFlow('private')">↑ Back to Private Section</button>

    <div class="modern-head" style="text-align:center">
      <div class="eyebrow">Motor Insurance Quotation</div>
      <h2 class="modern-title">Get Your Private Motor Insurance Quote</h2>
      <h3 class="modern-subtitle">Complete the form below in three easy steps to receive your personalised quotation.</h3>
      <p class="modern-desc">Choose the cover type that matches your vehicle, then fill in your details to compare premiums from leading insurers.</p>
    </div>

    <input type="hidden" id="f-vehicle-type-private" value="private">

    <!-- Step indicators -->
    <div class="form-steps">
      <div class="form-step active" id="step-tab-1-private" onclick="goStep('private',1)">
        <div class="step-num">Step 1</div>
        <div class="step-name">Personal Details</div>
      </div>
      <div class="form-step" id="step-tab-2-private" onclick="goStep('private',2)">
        <div class="step-num">Step 2</div>
        <div class="step-name">Vehicle Details</div>
      </div>
      <div class="form-step" id="step-tab-3-private" onclick="goStep('private',3)">
        <div class="step-num">Step 3</div>
        <div class="step-name">Quote Generation</div>
      </div>
    </div>

    <!-- Step 1: Personal Details -->
    <div class="form-panel active" id="panel-1-private">
      <div class="form-grid three">
        <div class="form-group">
          <label>Salutation <span class="req">*</span></label>
          <select id="f-salutation-private">
            <option value="">Select...</option>
            <option>Mr.</option><option>Mrs.</option><option>Miss</option><option>Dr.</option><option>Prof.</option>
          </select>
        </div>
        <div class="form-group">
          <label>First Name <span class="req">*</span></label>
          <input type="text" id="f-first-private" placeholder="e.g. John">
        </div>
        <div class="form-group">
          <label>Middle Name</label>
          <input type="text" id="f-middle-private" placeholder="Optional">
        </div>
        <div class="form-group">
          <label>Last Name <span class="req">*</span></label>
          <input type="text" id="f-last-private" placeholder="e.g. Doe">
        </div>
        <div class="form-group">
          <label>Phone Number <span class="req">*</span></label>
          <input type="tel" id="f-phone-private" placeholder="07 XX XXX XXX">
        </div>
        <div class="form-group">
          <label>Email Address <span class="req">*</span></label>
          <input type="email" id="f-email-private" placeholder="email@example.com">
        </div>
      </div>
      <div class="btn-group">
        <button class="btn btn-primary" onclick="goStep('private',2)">Continue to Vehicle Details →</button>
      </div>
    </div>

    <!-- Step 2: Vehicle Details -->
    <div class="form-panel" id="panel-2-private">
      <div class="form-grid">
        <div class="form-group">
          <label>Registration Number <span class="req">*</span></label>
          <input type="text" id="f-reg-private" placeholder="e.g. KDH 724V" oninput="validateReg('private', this)">
          <div class="reg-error" id="f-reg-error-private" style="color:var(--red);font-size:11px;display:none;margin-top:2px">Invalid Kenyan registration format (e.g. KDH 291H)</div>
        </div>
        <div class="form-group">
          <label>Chassis / Frame Number</label>
          <input type="text" id="f-chassis-private" placeholder="e.g. WVWZZZ1KZ9W123456">
        </div>
        <div class="form-group">
          <label>Make of Vehicle <span class="req">*</span></label>
          <div class="vehicle-search-wrap">
            <input type="text" id="f-make-private" placeholder="Search vehicle make..." autocomplete="off">
            <div class="vehicle-dropdown" id="f-make-dropdown-private"></div>
          </div>
          <input type="hidden" id="f-make-value-private" value="">
          <input type="text" id="f-make-other-private" placeholder="Type your vehicle make..." style="display:none;margin-top:8px">
        </div>
        <div class="form-group">
          <label>Model Name <span class="req">*</span></label>
          <div class="vehicle-search-wrap">
            <input type="text" id="f-model-private" placeholder="Select a make first..." autocomplete="off" disabled>
            <div class="vehicle-dropdown" id="f-model-dropdown-private"></div>
          </div>
          <input type="hidden" id="f-model-value-private" value="">
          <input type="text" id="f-model-other-private" placeholder="Type your vehicle model..." style="display:none;margin-top:8px">
        </div>
        <div class="form-group">
          <label>Body Type</label>
          <select id="f-body-private">
            <option value="">Select body type...</option>
            <option>Saloon</option><option>Station Wagon (S. Wagon)</option><option>SUV/4x4</option>
            <option>Hatchback</option><option>Pick-Up</option><option>Van/Minivan</option><option>Convertible</option><option>Coupe</option>
          </select>
        </div>
        <div class="form-group">
          <label>Year of Manufacture <span class="req">*</span></label>
          <select id="f-year-private">
            <option value="">Select year...</option>
          </select>
          <div class="value-error" id="f-year-error-private" style="display:none;color:var(--red);font-size:11px;margin-top:4px"></div>
        </div>
        <div class="form-group">
          <label>Color</label>
          <select id="f-color-private" onchange="toggleOtherColor('private', this)">
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
          <input type="text" id="f-color-other-private" placeholder="Type your vehicle color..." style="display:none;margin-top:8px">
        </div>
        <div class="form-group">
          <label>Value of Vehicle (KES) <span class="req">*</span></label>
          <input type="text" id="f-value-private" placeholder="e.g. 800,000" autocomplete="off">
          <div class="value-range-hint" id="f-value-hint-private">Enter the value of your vehicle</div>
          <div class="value-error" id="f-value-error-private" style="display:none"></div>
          <div class="min-sum-hint" id="f-min-sum-hint-private" style="font-size:11px;color:var(--text-muted);margin-top:4px"></div>
        </div>
      </div>
      <div class="btn-group">
        <button class="btn btn-secondary" onclick="goStep('private',1)">← Back</button>
        <button class="btn btn-primary" onclick="validateStep2AndProceed('private')">Continue to Quote Generation →</button>
      </div>
    </div>

    <!-- Step 3: Quote Generation -->
    <div class="form-panel" id="panel-3-private">
      <div class="valuation-box">
        <h3 style="font-size:15px;font-weight:700;margin-bottom:4px;color:var(--text)">Premium Estimate</h3>
        <p style="font-size:13px;color:var(--text-muted);margin-bottom:16px">Select an insurer card below to open its full quotation, or compare all side by side.</p>

        <div id="premium-preview-empty-private" style="padding:20px;text-align:center;color:var(--text-muted);font-size:13px;border:1.5px dashed var(--border);border-radius:var(--radius)">
          ← Go back to Step 2 and enter the vehicle value to see premium estimates.
        </div>

        <div id="premium-preview-private" style="display:none">
          <div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:14px">Click a card to open the full quotation</div>
          <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px" id="premium-cards-grid-private"></div>
        </div>
      </div>
      <div class="btn-group">
        <button class="btn btn-secondary" onclick="goStep('private',2)">← Back to Vehicle Details</button>
      </div>
    </div>

  </div>
</section>
 
<!-- ============================================================ PRIVATE MOTOR STEPS (Effortless-Integration style) ============================================================ -->
<section class="steps-section private-steps" id="private-steps-section">
  <div class="steps-head">
    <h2>How to Get a Private Motor Vehicle Quote</h2>
    <p>Five simple steps to your comprehensive quote — <a href="https://www.micglobalrisks.com/motor-quote/">start your quote here</a>.</p>
  </div>
  <div class="steps-grid">
    <div class="step-item">
      <div class="step-top">
        <span class="step-num">1</span>
        <span class="step-arrow">&#8594;</span>
      </div>
      <h4>Enter Your Personal Details</h4>
      <p>Input your name, contact details and ID/KRA PIN to get started.</p>
    </div>
    <div class="step-item">
      <div class="step-top">
        <span class="step-num">2</span>
        <span class="step-arrow">&#8594;</span>
      </div>
      <h4>Input Your Vehicle Details</h4>
      <p>Add your vehicle's make, model, year, registration and value.</p>
    </div>
    <div class="step-item">
      <div class="step-top">
        <span class="step-num">3</span>
        <span class="step-arrow">&#8594;</span>
      </div>
      <h4>Generate a Quotation</h4>
      <p>Our system instantly generates a comprehensive cover quotation for your vehicle.</p>
    </div>
    <div class="step-item">
      <div class="step-top">
        <span class="step-num">4</span>
        <span class="step-arrow">&#8594;</span>
      </div>
      <h4>Compare Rates &amp; Premiums</h4>
      <p>Compare rates and premiums across our panel of leading underwriters.</p>
    </div>
    <div class="step-item step-item-last">
      <div class="step-top">
        <span class="step-num">5</span>
      </div>
      <h4>Download Quote</h4>
      <p>Download your preferred quotation instantly and proceed to cover.</p>
    </div>
  </div>
</section>
 




 
</div><!-- /.mic-motor -->

<!-- ── VEHICLE TYPE SELECTOR ── -->
<section class="section" style="background:#F9F9FF;padding:88px 0 96px" id="vehicle-type-section">
 
  <div class="form-container">
    <div class="section-head">
      <div class="vt-eyebrow">Comprehensive Motor Vehicle Insurance</div>
      <div class="section-title" style="text-align:center;">What type of vehicle do you own?</div>
      <div class="section-sub" style="text-align:center;">Select your vehicle category to get a tailored insurance quote. Hover a card to see what's covered.</div>
    </div>

    <div class="vt-grid">

      <!-- PRIVATE CAR -->
      <div class="flip-card private">
        <div class="flip-card-inner">
          <div class="flip-card-front">
            <div class="flip-card-icon">
              <img src="{{ asset('images/hero/vehicle.jpg') }}" alt="Private car">
            </div>
            <div class="vt-title">Private Car Owner</div>
            <div class="vt-desc">I own a personal / private motor vehicle and need comprehensive insurance.</div>
            <div class="vt-hint">
              See what's covered
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </div>
          </div>
          <div class="flip-card-back">
            <div>
              <div class="vt-back-title">Private Comprehensive Cover Includes</div>
              <ul>
                <li><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>Accidental damage &amp; own-vehicle repairs</li>
                <li><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>Third-party liability protection</li>
                <li><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>Windscreen, theft &amp; fire cover</li>
                <li><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>24/7 roadside assistance</li>
              </ul>
            </div>
            <button type="button" class="btn-flip" onclick="selectVehicleType('private')">Get Started <span>→</span></button>
          </div>
        </div>
      </div>

      <!-- PSV -->
      <div class="flip-card psv">
        <div class="flip-card-inner">
          <div class="flip-card-front">
            <div class="flip-card-icon">
              <img src="{{ asset('images/hero/family-car.jpg') }}" alt="PSV vehicle">
            </div>
            <div class="vt-title">Public Service Vehicle (PSV) Owner</div>
            <div class="vt-desc">I own a matatu, bus, taxi or other public service vehicle and need PSV insurance.</div>
            <div class="vt-hint">
              See what's covered
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </div>
          </div>
          <div class="flip-card-back">
            <div>
              <div class="vt-back-title">PSV Comprehensive Cover Includes</div>
              <ul>
                <li><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>Comprehensive cover for matatus, buses &amp; taxis</li>
                <li><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>Passenger liability protection</li>
                <li><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>Fleet &amp; multi-vehicle discounts</li>
                <li><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>Fast, dedicated claims processing</li>
              </ul>
            </div>
            <button type="button" class="btn-flip" onclick="selectVehicleType('psv')">Get Started <span>→</span></button>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ── QUOTE FORM (vehicle-type selector) ── -->
<section class="modern-section quote-form" style="display:none" id="quote-form-section-vt">
  <div class="form-container">

    <button type="button" class="quote-form-back" onclick="closeQuoteFlowVT()">↑ Back to Vehicle Selection</button>

    <div class="modern-head" style="text-align:center">
      <div class="eyebrow">Motor Insurance Quotation</div>
      <h2 class="modern-title" id="form-title-vt">Get Your Motor Insurance Quote</h2>
      <h3 class="modern-subtitle" id="form-subtitle-vt">Complete the form below in three easy steps to receive your personalised quotation.</h3>
      <p class="modern-desc">Choose the cover type that matches your vehicle, then fill in your details to compare premiums from leading insurers.</p>
    </div>
  
    <input type="hidden" id="f-vehicle-type-vt" value="">

    <!-- Step indicators -->
    <div class="form-steps">
      <div class="form-step active" id="step-tab-1-vt" onclick="goStepVT(1)">
        <div class="step-num">Step 1</div>
        <div class="step-name">Personal Details</div>
      </div>
      <div class="form-step" id="step-tab-2-vt" onclick="goStepVT(2)">
        <div class="step-num">Step 2</div>
        <div class="step-name">Vehicle Details</div>
      </div>
      <div class="form-step" id="step-tab-3-vt" onclick="goStepVT(3)">
        <div class="step-num">Step 3</div>
        <div class="step-name">Quote Generation</div>
      </div>
    </div>
  
    <!-- Step 1: Personal Details -->
    <div class="form-panel active" id="panel-1-vt">
      <div class="form-grid three">
        <div class="form-group">
          <label>Salutation <span class="req">*</span></label>
          <select id="f-salutation-vt">
            <option value="">Select...</option>
            <option>Mr.</option><option>Mrs.</option><option>Miss</option><option>Dr.</option><option>Prof.</option>
          </select>
        </div>
        <div class="form-group">
          <label>First Name <span class="req">*</span></label>
          <input type="text" id="f-first-vt" placeholder="e.g. John">
        </div>
        <div class="form-group">
          <label>Middle Name</label>
          <input type="text" id="f-middle-vt" placeholder="Optional">
        </div>
        <div class="form-group">
          <label>Last Name <span class="req">*</span></label>
          <input type="text" id="f-last-vt" placeholder="e.g. Doe">
        </div>
        <div class="form-group">
          <label>Phone Number <span class="req">*</span></label>
          <input type="tel" id="f-phone-vt" placeholder="07 XX XXX XXX">
        </div>
        <div class="form-group">
          <label>Email Address <span class="req">*</span></label>
          <input type="email" id="f-email-vt" placeholder="email@example.com">
        </div>
      </div>
      <div class="btn-group">
        <button class="btn btn-primary" onclick="goStepVT(2)">Continue to Vehicle Details →</button>
      </div>
    </div>
  
    <!-- Step 2: Vehicle Details -->
    <div class="form-panel" id="panel-2-vt">
      <div class="form-grid">
        <div class="form-group">
          <label>Registration Number <span class="req">*</span></label>
          <input type="text" id="f-reg-vt" placeholder="e.g. KDH 724V" oninput="validateRegVT(this)">
          <div class="reg-error" id="f-reg-error-vt" style="color:var(--red);font-size:11px;display:none;margin-top:2px">Invalid Kenyan registration format (e.g. KDH 291H)</div>
        </div>
        <div class="form-group" id="f-pax-group-vt" style="display:none">
          <label>Number of Passengers (Pax) <span class="req">*</span></label>
          <input type="number" id="f-pax-vt" placeholder="e.g. 14" min="1" value="1">
          <div style="font-size:11px;color:var(--text-muted);margin-top:2px">Used to calculate Passenger Legal Liability (PLL)</div>
        </div>
        <div class="form-group">
          <label>Chassis / Frame Number</label>
          <input type="text" id="f-chassis-vt" placeholder="e.g. WVWZZZ1KZ9W123456">
        </div>
        <div class="form-group">
          <label>Make of Vehicle <span class="req">*</span></label>
          <div class="vehicle-search-wrap">
            <input type="text" id="f-make-vt" placeholder="Search vehicle make..." autocomplete="off">
            <div class="vehicle-dropdown" id="f-make-dropdown-vt"></div>
          </div>
          <input type="hidden" id="f-make-value-vt" value="">
          <input type="text" id="f-make-other-vt" placeholder="Type your vehicle make..." style="display:none;margin-top:8px">
        </div>
        <div class="form-group">
          <label>Model Name <span class="req">*</span></label>
          <div class="vehicle-search-wrap">
            <input type="text" id="f-model-vt" placeholder="Select a make first..." autocomplete="off" disabled>
            <div class="vehicle-dropdown" id="f-model-dropdown-vt"></div>
          </div>
          <input type="hidden" id="f-model-value-vt" value="">
          <input type="text" id="f-model-other-vt" placeholder="Type your vehicle model..." style="display:none;margin-top:8px">
        </div>
        <div class="form-group">
          <label>Body Type</label>
          <select id="f-body-vt">
            <option value="">Select body type...</option>
            <option>Saloon</option><option>Station Wagon (S. Wagon)</option><option>SUV/4x4</option>
            <option>Hatchback</option><option>Pick-Up</option><option>Van/Minivan</option><option>Convertible</option><option>Coupe</option>
          </select>
        </div>
        <div class="form-group">
          <label>Year of Manufacture <span class="req">*</span></label>
          <select id="f-year-vt">
            <option value="">Select year...</option>
          </select>
          <div class="value-error" id="f-year-error-vt" style="display:none;color:var(--red);font-size:11px;margin-top:4px"></div>
        </div>
        <div class="form-group">
          <label>Color</label>
          <select id="f-color-vt">
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
          <input type="text" id="f-color-other-vt" placeholder="Type your vehicle color..." style="display:none;margin-top:8px">
        </div>
        <div class="form-group">
          <label>Value of Vehicle (KES) <span class="req">*</span></label>
          <input type="text" id="f-value-vt" placeholder="e.g. 800,000" autocomplete="off">
          <div class="value-range-hint" id="f-value-hint-vt">Enter the value of your vehicle</div>
          <div class="value-error" id="f-value-error-vt" style="display:none"></div>
          <div class="min-sum-hint" id="f-min-sum-hint-vt" style="font-size:11px;color:var(--text-muted);margin-top:4px"></div>
        </div>
      </div>
      <div class="btn-group">
        <button class="btn btn-secondary" onclick="goStepVT(1)">← Back</button>
        <button class="btn btn-primary" onclick="validateStep2AndProceedVT()">Continue to Quote Generation →</button>
      </div>
    </div>
  
    <!-- Step 3: Quote Generation -->
    <div class="form-panel" id="panel-3-vt">
      <div class="valuation-box">
        <h3 style="font-size:15px;font-weight:700;margin-bottom:4px;color:var(--text)">Premium Estimate</h3>
        <p style="font-size:13px;color:var(--text-muted);margin-bottom:16px">Select an insurer card below to open its full quotation, or compare all side by side.</p>
  
        <div id="premium-preview-empty-vt" style="padding:20px;text-align:center;color:var(--text-muted);font-size:13px;border:1.5px dashed var(--border);border-radius:var(--radius)">
          ← Go back to Step 2 and enter the vehicle value to see premium estimates.
        </div>
  
        <div id="premium-preview-vt" style="display:none">
          <div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:14px">Click a card to open the full quotation</div>
          <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px" id="premium-cards-grid-vt"></div>
        </div>
      </div>
      <div class="btn-group">
        <button class="btn btn-secondary" onclick="goStepVT(2)">← Back to Vehicle Details</button>
      </div>
    </div>
  
  </div>
</section>

@auth
@if ($__lastQuotation)
<script>
  // ASSUMPTION: field names on $__lastQuotation match the Quotation model's actual
  // columns. If any of these don't exist on the model, that field will just come
  // through as blank/undefined below — it won't throw, but won't prefill.
  function regenerateSameCar() {
    var q = @json($__lastQuotation);

    var setVal = function (id, val) {
      var el = document.getElementById(id);
      if (el && val !== undefined && val !== null) el.value = val;
    };

    setVal('f-salutation', q.salutation);
    setVal('f-first', q.first_name);
    setVal('f-middle', q.middle_name);
    setVal('f-last', q.last_name);
    setVal('f-phone', q.phone);
    setVal('f-email', q.email);

    setVal('f-reg', q.registration_number);
    setVal('f-chassis', q.chassis_number);

    // Make/model use a hidden "value" input alongside the visible search input —
    // populate both so the existing search/dropdown logic still treats them as selected.
    setVal('f-make', q.car_make);
    setVal('f-make-value', q.car_make);
    var modelInput = document.getElementById('f-model');
    if (modelInput) modelInput.disabled = false;
    setVal('f-model', q.car_model);
    setVal('f-model-value', q.car_model);

    setVal('f-body', q.body_type);
    setVal('f-year', q.year_of_manufacture);
    setVal('f-color', q.color);
    setVal('f-value', q.vehicle_value);

    goStep(3);
    document.getElementById('quote-form-section').scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  function startNewQuote() {
    goStep(1);
    document.getElementById('quote-form-section').scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
</script>
@endif
@endauth

<!-- ── QUOTE FORM ── -->
{{-- <section class="section" style="background:#F9F9FF">
  <div class="form-container">
    <div class="section-head">
      <div class="section-title" style="text-align: center;">Get Your Motor Insurance Quote</div>
      <div class="section-sub"style="text-align: center;">Complete the form below in three easy steps to receive your personalised quotation.</div>
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
            <option>Saloon</option><option>Station Wagon (S. Wagon)</option><option>SUV/4x4</option>
            <option>Hatchback</option><option>Pick-Up</option><option>Van/Minivan</option><option>Convertible</option><option>Coupe</option>
          </select>
        </div>
        <div class="form-group">
          <label>Year of Manufacture <span class="req">*</span></label>
          <select id="f-year">
            <option value="">Select year...</option>
          </select>
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
          <div class="value-range-hint" id="f-value-hint">Enter between KES 800,000 and KES 1,300,000</div>
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
        <p style="font-size:13px;color:var(--text-muted);margin-bottom:16px">Select an insurer card below to open its full quotation, or compare all three side by side.</p>
 
        <div id="premium-preview-empty" style="padding:20px;text-align:center;color:var(--text-muted);font-size:13px;border:1.5px dashed var(--border);border-radius:var(--radius)">
          ← Go back to Step 2 and enter the vehicle value to see premium estimates.
        </div>
 
        <div id="premium-preview" style="display:none">
          <div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:14px">Click a card to open the full quotation</div>
          <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px">

            @foreach ($__companyData as $cd)
            <div class="preview-card" onclick="guestGuard(function(){ openQuotePage('{{ $cd['slug'] }}'); })" style="background:white;border:1.5px solid var(--border);border-radius:var(--radius);padding:16px 14px;text-align:center;cursor:pointer;transition:box-shadow .2s,transform .15s">
              <div style="height:36px;display:flex;align-items:center;justify-content:center;margin-bottom:8px">
                @if ($cd['logo'])
                <img src="{{ $cd['logo'] }}" alt="{{ $cd['name'] }}" style="max-height:32px;max-width:90px;object-fit:contain" onerror="fallbackLogo(this,'{{ $cd['slug'] }}')">
                @endif
                <span style="{{ $cd['logo'] ? 'display:none' : '' }};font-size:11px;font-weight:800;color:{{ $cd['color'] }}">{{ strtoupper($cd['name']) }}</span>
              </div>
              <div style="font-size:11px;color:{{ $cd['color'] }};font-weight:700;margin-bottom:6px">{{ strtoupper($cd['name']) }}</div>
              <div style="font-size:1.1rem;font-weight:700;color:{{ $cd['color'] }}" id="prev-{{ $cd['slug'] }}">—</div>
              <div style="font-size:11px;color:var(--text-muted);margin-bottom:10px">@ {{ $cd['rate'] * 100 }}%</div>
              <div style="font-size:11px;font-weight:700;color:white;background:{{ $cd['color'] }};border-radius:5px;padding:5px 0">View Quote ↗</div>
            </div>
            @endforeach

            <!-- Compare All -->
            <div class="preview-card" onclick="guestGuard(function(){ openQuotePage('ALL'); })" style="background:white;border:1.5px solid #D0021B;border-radius:var(--radius);padding:16px 14px;text-align:center;cursor:pointer;transition:box-shadow .2s,transform .15s">
              <div style="height:36px;display:flex;align-items:center;justify-content:center;margin-bottom:8px">
                <img src="{{ $__compareAllLogoUrl }}" alt="Compare All" style="max-height:32px;max-width:90px;object-fit:contain" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
                <span style="display:none;font-size:18px">📊</span>
              </div>
              <div style="font-size:11px;color:#D0021B;font-weight:700;margin-bottom:6px">COMPARE ALL</div>
              <div style="font-size:1.1rem;font-weight:700;color:#D0021B">{{ $__companyData->count() }} Quotes</div>
              <div style="font-size:11px;color:var(--text-muted);margin-bottom:10px">All insurers</div>
              <div style="font-size:11px;font-weight:700;color:white;background:#D0021B;border-radius:5px;padding:5px 0">Compare ↗</div>
            </div>

          </div>
        </div>
      </div>
      <div class="btn-group">
        <button class="btn btn-secondary" onclick="goStep(2)">← Back to Vehicle Details</button>
      </div>
    </div>
 
  </div>
</section> --}}

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




<!-- ── REGENERATE QUOTE ── -->
{{-- <section class="section">
  <div class="section-head" style="text-align:center">
    <div class="section-title">Retrieve Existing Quote</div>
    <div class="section-sub">Already received a quotation number? Enter it below to regenerate your quote.</div>
  </div>
  <div class="regen-box">
    <div class="regen-icon">🔍</div>
    <h3>Regenerate Your Quotation</h3>
    <p>Enter your unique quotation number (e.g. {{ $__companyData->first()['prefix'] ?? 'GA' }}-2026/001) to retrieve and regenerate your insurance quote.</p>
    <form method="POST" action="{{ route('motor.lookup.quotation') }}" style="display:contents">
      @csrf
      <div class="regen-input-row">
        <input type="text" name="number" id="regenInput" placeholder="e.g. GA-2026/001" required>
        <button type="submit" class="btn btn-primary">Retrieve →</button>
      </div>
    </form>
    <div id="regen-result" style="margin-top:14px;font-size:13.5px;color:var(--text-muted)"></div>
  </div>
</section> --}}

<!-- ── RETRIEVE QUOTE ── -->
<section class="section quote-lookup-section" id="quote-lookup">
  <div class="ql-banner">
    <div class="ql-copy">
      <div class="ql-eyebrow">Already insured with us?</div>
      <h2 class="ql-headline">REGENERATE<br><span class="ql-accent">YOUR QUOTE</span></h2>
      <p class="ql-sub">Enter your vehicle registration number to regenerate a previous quote.</p>

      <form method="POST" action="{{ route('quotations.regenerate') }}" class="ql-form">
        @csrf
        <div class="regen-input-row">
          <input type="text" name="registration_number" placeholder="e.g. KDH 724V" required autocomplete="off">
          <button type="submit" class="ql-cta" id="regenSubmitBtn">Regenerate</button>
        </div>
      </form>

      <div id="regen-result" class="ql-result-msg"></div>
    </div>

    <div class="ql-image">
      <img src="{{ asset('images/hero/cars-parked.jpg') }}" alt="Regenerate your motor insurance quote">
    </div>
  </div>

  <!-- Results table, hidden until a match is found -->
  <div id="regen-results-wrap" class="ql-results-wrap" style="display:none">
    <table class="ql-results-table">
      <thead>
        <tr>
          <th>Quotation No</th>
          <th>Make</th>
          <th>Model</th>
          <th>Year</th>
          <th>Chassis No</th>
          <th>Valuation</th>
          <th>Premium</th>
        </tr>
      </thead>
      <tbody id="regen-results-body"></tbody>
    </table>
    <div id="regen-login-cta" class="ql-login-cta" style="display:none">
      <span>Login to view your full quotation details.</span>
      <button type="button" class="ql-cta ql-cta-dark" onclick="loginToViewQuote()">Login to View →</button>
    </div>
  </div>
</section>

<!-- ── QUOTE PAGE OVERLAY ── -->
<div class="page-overlay" id="quotePage">
  <div class="quote-page">
    <div class="quote-header">
      <button class="quote-back" onclick="closeQuotePage()">← Back to Portal</button>
      <div>
        <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px">Your Quotation Number</div>
        <div class="quote-num-copy">
          <div class="quote-num-badge" id="quoteNumDisplay">—</div>
          <button class="copy-btn" onclick="copyQuoteNum()">Copy</button>
        </div>
      </div>
    </div>

    <div style="font-size:1.2rem;font-weight:700;margin-bottom:20px;color:var(--text)">Quote Summary</div>

    <div class="summary-grid">
      <div class="summary-card">
        <h4>Personal Details</h4>
        <div class="summary-row"><span>Full Name</span><span id="sq-name">—</span></div>
        <div class="summary-row"><span>Phone</span><span id="sq-phone">—</span></div>
        <div class="summary-row"><span>Email</span><span id="sq-email">—</span></div>
      </div>
      <div class="summary-card">
        <h4>Vehicle Details</h4>
        <div class="summary-row"><span>Registration</span><span id="sq-reg">—</span></div>
        <div class="summary-row"><span>Make / Model</span><span id="sq-make">—</span></div>
        <div class="summary-row"><span>Year / Color</span><span id="sq-year">—</span></div>
        <div class="summary-row"><span>Estimated Value</span><span id="sq-value">—</span></div>
      </div>
    </div>

    <div style="font-size:1rem;font-weight:700;margin-bottom:16px;color:var(--text)">Select Insurer to Download Quote</div>
    <div class="quote-company-cards" id="qcCards">
      <!-- populated by JS -->
    </div>

    <div style="background:var(--blue-light);border:1.5px solid var(--blue);border-radius:var(--radius-lg);padding:16px 20px;font-size:13.5px;color:var(--blue-dark);margin-top:8px">
      <strong>Note:</strong> Quotation is valid for 15 days. Subject to satisfactory valuation and standard policy terms and conditions.
    </div>
  </div>
</div>

<!-- ── INDIVIDUAL QUOTE PAGE ── -->
<div class="individual-quote" id="indQuotePage">
  <div class="iq-inner">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
      <button class="quote-back" id="indBackBtn" onclick="closeIndividual()">← Back</button>
      <div class="quote-num-copy">
        <div class="quote-num-badge" id="indQuoteNum">—</div>
        <button class="copy-btn" onclick="copyIndQuoteNum()">Copy</button>
      </div>
    </div>
    <div id="indQuoteContent"><!-- rendered by JS --></div>
    <div class="doc-actions" style="padding:0;margin-top:20px;background:transparent">
      <button class="btn btn-secondary" onclick="confirmAndDownload()">Preview & Download</button>
      <button class="btn btn-primary" onclick="window.print()">🖨 Print</button>
      <button class="btn btn-secondary" onclick="closeIndividual()">← Back</button>
    </div>
  </div>
</div>

<!-- ── CONFIRM MODAL ── -->
<div class="modal-overlay" id="confirmModal">
  <div class="modal">
    <div class="modal-head">
      <h3>Confirm Your Details</h3>
      <button class="modal-close" onclick="closeModal()">×</button>
    </div>
    <div class="modal-body">
      <p style="font-size:13.5px;color:var(--text-muted);margin-bottom:20px">Please review all details before downloading your quotation document.</p>
      <div class="modal-section">
        <h4>Personal Details</h4>
        <div class="modal-row"><span>Full Name</span><span id="m-name">—</span></div>
        <div class="modal-row"><span>Phone</span><span id="m-phone">—</span></div>
        <div class="modal-row"><span>Email</span><span id="m-email">—</span></div>
      </div>
      <div class="modal-section">
        <h4>Vehicle Details</h4>
        <div class="modal-row"><span>Registration</span><span id="m-reg">—</span></div>
        <div class="modal-row"><span>Make / Model</span><span id="m-make">—</span></div>
        <div class="modal-row"><span>Year / Color</span><span id="m-year">—</span></div>
        <div class="modal-row"><span>Estimated Value</span><span id="m-value">—</span></div>
        <div class="modal-row"><span>Body Type</span><span id="m-body">—</span></div>
        <div class="modal-row"><span>Chassis No.</span><span id="m-chassis">—</span></div>
      </div>
      <div class="modal-section">
        <h4>Quotation</h4>
        <div class="modal-row"><span>Insurer</span><span id="m-insurer">—</span></div>
        <div class="modal-row"><span>Rate</span><span id="m-rate">—</span></div>
        <div class="modal-row"><span>Basic Premium</span><span id="m-premium">—</span></div>
        <div class="modal-row"><span>PVT Premium</span><span id="m-pvt">—</span></div>
        <div class="modal-row"><span>PLL Premium</span><span id="m-pll">—</span></div>
        <div class="modal-row"><span>Levis (0.45%)</span><span id="m-levis">—</span></div>
        <div class="modal-row"><span>Stamp Duty</span><span id="m-stamp">—</span></div>
        <div class="modal-row" style="font-weight:700"><span>Total Premium</span><span id="m-total">—</span></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal()">Edit Details</button>
      <button class="btn btn-primary" onclick="proceedDownload()">Confirm & Download</button>
    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast"></div>

<x-auth-modal/>

<x-public-footer/>

@php
    $__hardcodedLogos = collect(\App\Models\Company::hardcodedLogoMap())
        ->mapWithKeys(fn ($p, $s) => [$s => asset($p)]);
@endphp

<script>
window.MIC_COMPANIES_ALL = @json($__companyData);
window.MIC_COMPANIES_PSV = @json($__psvCompanyData);
window.MIC_COMPANIES_PRIVATE = @json($__privateCompanyData);
window.MIC_COMPANIES = window.MIC_COMPANIES_ALL;
window.MIC_GUEST = @json(auth()->guest());
window.MIC_MODE = 'all';
window.MIC_SELECTED_COMPANY = null;
window.MIC_VEHICLE_TYPE = 'psv';
window.MIC_MIN_SUM_PSV = @json($__companies->pluck('min_sum_insured_psv', 'slug')->filter());
window.MIC_MIN_SUM_PRIVATE = @json($__companies->pluck('min_sum_insured_private', 'slug')->filter());
window.hardcodedLogos = @json($__hardcodedLogos);
function selectVehicleType(type) {
  window.MIC_VEHICLE_TYPE = type;
  document.getElementById('f-vehicle-type-vt').value = type;
  var companies = type === 'psv' ? window.MIC_COMPANIES_PSV : window.MIC_COMPANIES_PRIVATE;
  window.MIC_COMPANIES = companies;

  var vtForm = document.getElementById('quote-form-section-vt');
  vtForm.style.display = 'block';

  var paxGroup = document.getElementById('f-pax-group-vt');
  if (type === 'psv') {
    paxGroup.style.display = 'block';
  } else {
    paxGroup.style.display = 'none';
  }

  var title = document.getElementById('form-title-vt');
  var subtitle = document.getElementById('form-subtitle-vt');
  if (type === 'psv') {
    title.textContent = 'Get Your PSV Insurance Quote';
    subtitle.textContent = 'Complete the form below for your Public Service Vehicle insurance quotation.';
  } else {
    title.textContent = 'Get Your Private Car Insurance Quote';
    subtitle.textContent = 'Complete the form below for your private motor vehicle insurance quotation.';
  }

  var minHints = [];
  if (type === 'psv') {
    var slugs = ['sanlam-allianz', 'ga-insurance-ltd', 'definite-insurance'];
    slugs.forEach(function(s) {
      var val = window.MIC_MIN_SUM_PSV[s];
      if (val) {
        var name = companies.find(function(c) { return c.slug === s; });
        minHints.push((name ? name.name : s) + ': KES ' + Number(val).toLocaleString('en-KE'));
      }
    });
  } else {
    var slugs = ['sanlam-allianz', 'ga-insurance-ltd', 'britam'];
    slugs.forEach(function(s) {
      var val = window.MIC_MIN_SUM_PRIVATE[s];
      if (val) {
        var name = companies.find(function(c) { return c.slug === s; });
        minHints.push((name ? name.name : s) + ': KES ' + Number(val).toLocaleString('en-KE'));
      }
    });
  }
  var hintEl = document.getElementById('f-min-sum-hint-vt');
  if (minHints.length) {
    hintEl.innerHTML = '<strong>Minimum Sum Insured:</strong> ' + minHints.join(' | ');
  } else {
    hintEl.innerHTML = '';
  }

  goStepVT(1);
  vtForm.scrollIntoView({behavior: 'smooth', block: 'start'});
}

function goStepVT(n) {
  var panels = document.querySelectorAll('#quote-form-section-vt .form-panel');
  var steps = document.querySelectorAll('#quote-form-section-vt .form-step');
  panels.forEach(function(p) { p.classList.remove('active'); });
  steps.forEach(function(s) { s.classList.remove('active'); s.classList.remove('done'); });
  for (var i = 0; i < n; i++) { if (steps[i]) steps[i].classList.add('done'); }
  if (steps[n - 1]) steps[n - 1].classList.add('done');
  var panel = document.getElementById('panel-' + n + '-vt');
  if (panel) panel.classList.add('active');
  if (steps[n - 1]) steps[n - 1].classList.add('active');
  document.getElementById('quote-form-section-vt').scrollIntoView({behavior: 'smooth', block: 'start'});
}

function closeQuoteFlowVT() {
  document.getElementById('quote-form-section-vt').style.display = 'none';
  document.getElementById('vehicle-type-section').scrollIntoView({behavior: 'smooth', block: 'start'});
}

function validateRegVT(input) {
  var val = input.value.trim().toUpperCase();
  var err = document.getElementById('f-reg-error-vt');
  var regex = /^[A-Z]{3}\s?\d{3}[A-Z]$/;
  if (val.length > 0 && !regex.test(val)) {
    err.style.display = 'block';
  } else {
    err.style.display = 'none';
  }
}

function validateStep2AndProceedVT() {
  var required = [
    {id: 'f-reg-vt', label: 'Registration Number'},
    {id: 'f-make-value-vt', label: 'Make of Vehicle'},
    {id: 'f-model-value-vt', label: 'Model Name'},
    {id: 'f-year-vt', label: 'Year of Manufacture'},
    {id: 'f-value-vt', label: 'Value of Vehicle'}
  ];
  var missing = [];
  required.forEach(function(r) {
    var el = document.getElementById(r.id);
    if (!el || !el.value.trim()) missing.push(r.label);
  });
  if (missing.length) {
    alert('Please fill in the following required fields:\n\u2022 ' + missing.join('\n\u2022 '));
    return;
  }
  var type = window.MIC_VEHICLE_TYPE;
  var companies = window.MIC_COMPANIES || [];
  var vehicleValue = parseFloat(document.getElementById('f-value-vt').value.replace(/[^0-9.]/g, ''));
  var vehicleYear = parseInt(document.getElementById('f-year-vt').value);
  var currentYear = new Date().getFullYear();
  var minHints = [];
  var errors = [];
  if (type === 'psv') {
    var slugs = ['sanlam-allianz', 'ga-insurance-ltd', 'definite-insurance'];
    slugs.forEach(function(s) {
      var val = window.MIC_MIN_SUM_PSV[s];
      if (val && vehicleValue < val) {
        var name = companies.find(function(c) { return c.slug === s; });
        errors.push((name ? name.name : s) + ' requires minimum sum insured of KES ' + Number(val).toLocaleString('en-KE'));
      }
    });
  } else {
    var slugs = ['sanlam-allianz', 'ga-insurance-ltd', 'britam'];
    slugs.forEach(function(s) {
      var val = window.MIC_MIN_SUM_PRIVATE[s];
      if (val && vehicleValue < val) {
        var name = companies.find(function(c) { return c.slug === s; });
        errors.push((name ? name.name : s) + ' requires minimum sum insured of KES ' + Number(val).toLocaleString('en-KE'));
      }
    });
  }
  if (errors.length) {
    alert('The following insurers cannot accept your vehicle value:\n\u2022 ' + errors.join('\n\u2022 '));
    return;
  }
  var hintEl = document.getElementById('f-min-sum-hint-vt');
  if (minHints.length) {
    hintEl.innerHTML = '<strong>Minimum Sum Insured:</strong> ' + minHints.join(' | ');
  } else {
    hintEl.innerHTML = '';
  }
  if (typeof updatePremiumPreview === 'function') updatePremiumPreview('vt');
  goStepVT(3);
}

function guestGuard(callback) {
  if (typeof callback === 'function') {
    var match = callback.toString().match(/openQuotePage\('([^']+)'(?:,\s*'([^']*)')?\)/);
    if (match && match[1] !== 'ALL') {
      try {
        var type = match[2] !== undefined ? match[2] : (document.getElementById('f-reg-vt') ? 'vt' : (document.getElementById('f-reg-private') ? 'private' : (document.getElementById('f-reg-psv') ? 'psv' : '')));
        var d = typeof getFormData === 'function' ? getFormData(type) : {};
        sessionStorage.setItem('pending_quote_company', match[1]);
        sessionStorage.setItem('micQuoteData', JSON.stringify({
          ...d,
          firstName: d.firstName || d.first || '',
          lastName: d.lastName || d.last || '',
          mode: match[1]
        }));
      } catch(e) {}
    }
    if (window.MIC_GUEST) {
      if (match && match[1] !== 'ALL') {
        sessionStorage.setItem('auth_redirect', '/dashboard?pending_quote=1');
        var alpineEl = document.querySelector('#authModal');
        if (alpineEl && alpineEl.__x) { alpineEl.__x.$data.redirectTo = '/dashboard?pending_quote=1'; }
        openAuthModal();
        return;
      }
      callback();
      return;
    }
    if (match && match[1] !== 'ALL') {
      window.location.href = '/dashboard?pending_quote=1';
      return;
    }
  }
  if (typeof callback === 'function') callback();
}
function fallbackLogo(el, slug) {
  var hardcoded = (window.hardcodedLogos || {})[slug];
  if (hardcoded && el.src !== hardcoded) {
    el.onerror = function() { el.style.display = 'none'; if (el.nextElementSibling) el.nextElementSibling.style.display = 'block'; };
    el.src = hardcoded;
    return;
  }
  el.style.display = 'none';
  if (el.nextElementSibling) el.nextElementSibling.style.display = 'block';
}
</script>
<script src="{{ asset('js/home.js') }}"></script>
<script>
// Init value input listeners for suffixed forms
['psv', 'private', 'vt'].forEach(function(sfx) {
  if (typeof initValueInput === 'function') initValueInput(sfx);
});

// Wire up passenger inputs to trigger live premium preview
function wirePaxInput(sfx) {
  var el = document.getElementById('f-pax' + (sfx ? '-' + sfx : ''));
  if (el) {
    el.addEventListener('input', function() {
      if (typeof updatePremiumPreview === 'function') updatePremiumPreview(sfx);
    });
    el.addEventListener('change', function() {
      if (typeof updatePremiumPreview === 'function') updatePremiumPreview(sfx);
    });
  }
}
wirePaxInput('psv');
wirePaxInput('vt');
wirePaxInput('');    // main form
</script>
@yield('scripts')
</body>
</html>