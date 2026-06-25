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
      content="Get an instant motor insurance quote in Kenya. Compare comprehensive premiums from GA Insurance, Britam, and Sanlam Allianz. Download your quotation in seconds.">
<meta name="keywords"
      content="motor insurance Kenya, car insurance quote Kenya, comprehensive motor cover, GA Insurance, Britam motor, Sanlam Allianz, vehicle insurance Nairobi, third party insurance Kenya">
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
<meta property="og:description" content="Compare motor insurance premiums from GA Insurance, Britam, and Sanlam Allianz. Get your instant quote and download your certificate — all in one place.">
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
<meta name="twitter:description" content="Compare motor insurance premiums from GA Insurance, Britam, and Sanlam Allianz. Instant quotes, downloadable certificates.">
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

@inject('companyModel', 'App\Models\Company')
@inject('settingModel', 'App\Models\Setting')
@php
    $__companies = $companies ?? $companyModel->active()->with('rates')->get();
    $__compareAllLogo = $settingModel->getValue('compare_all_logo');
    $__compareAllLogoUrl = $__compareAllLogo ? asset('storage/' . $__compareAllLogo) : asset('images/benchmarking.png');
    $companyColors = ['#003087', '#0066B3', '#00205B', '#D0021B'];
    $__companyData = $__companies->map(function ($c, $i) use ($companyColors) {
        $rate = $c->rates->firstWhere('value_type', 'percentage');
        return [
            'slug' => $c->slug,
            'name' => $c->name,
            'logo' => $c->logoUrl(),
            'rate' => $rate ? (float) $rate->percentage / 100 : 0,
            'taxRate' => 0.005,
            'color' => $companyColors[$i % count($companyColors)],
            'prefix' => $c->getInitials(),
            'logoFallback' => $c->logoUrl(),
        ];
    })->values();
@endphp

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
    <div class="search-bar">
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
             id="searchInput">
      <button onclick="doSearch()">Search</button>
    </div>

    <!-- chips below the bar -->
    <div class="category-chips">
      <span class="chip active" onclick="setChip(this,'Registration No')">Registration No</span>
      <span class="chip"        onclick="setChip(this,'Quotation No')">Quotation No</span>
      <span class="chip"        onclick="setChip(this,'Vehicle Number')">Vehicle Number</span>
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

<!-- ── QUOTE FORM ── -->
<section class="section" style="background:#F9F9FF">
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
</section>

<section class="section section-alt" style="background-color: #005F9E;">
  <div style="text-align:center;margin-bottom:20px">
    <div class="section-title" style="color:#F9F9FF;font-size:20px">Choose Your Insurer</div>
    <div class="section-sub" style="color:#F9F9FF;font-size:15px;margin-top:4px">Select an insurer to generate a tailored quote.</div>
  </div>
  <div class="company-cards">

    @foreach ($__companyData as $cd)
    <a href="{{ route('generate.quote', $cd['slug']) }}" class="company-card no-underline" style="cursor:pointer;display:block">
      <div class="company-logo-box">
        @if ($cd['logo'])
        <img src="{{ $cd['logo'] }}" alt="{{ $cd['name'] }}" class="nav-logo" onerror="fallbackLogo(this,'{{ $cd['slug'] }}')">
        @else
        <span style="font-size:11px;font-weight:800;color:{{ $cd['color'] }}">{{ strtoupper($cd['name']) }}</span>
        @endif
      </div>
      <div class="company-name">{{ $cd['name'] }}</div>
      <div class="company-rate">Rate: <strong>{{ $cd['rate'] * 100 }}%</strong> of vehicle value</div>
      <button class="btn btn-primary" style="width:100%;justify-content:center;font-size:11px;padding:5px 10px">Generate Quote ↗</button>
    </a>
    @endforeach

    <a href="{{ route('generate.quote', 'compare-all') }}" class="company-card no-underline" style="cursor:pointer;display:block">
      <div class="company-logo-box"><img src="{{ $__compareAllLogoUrl }}" alt="Compare All" onerror="this.style.display='none';this.nextElementSibling.style.display='block'"><span style="display:none;font-size:18px">📊</span></div>
      <div class="company-name">Compare All</div>
      <div class="company-rate">Compare rates from all insurers</div>
      <button class="btn btn-primary" style="width:100%;justify-content:center;font-size:11px;padding:5px 10px">Compare All ↗</button>
    </a>

  </div>
</section>

<!-- ── REGENERATE QUOTE ── -->
<section class="section">
  <div class="section-head" style="text-align:center">
    <div class="section-title">Retrieve Existing Quote</div>
    <div class="section-sub">Already received a quotation number? Enter it below to regenerate your quote.</div>
  </div>
  <div class="regen-box">
    <div class="regen-icon">🔍</div>
    <h3>Regenerate Your Quotation</h3>
    <p>Enter your unique quotation number (e.g. GA-2026/001, BRITAM-2026/003) to retrieve and regenerate your insurance quote.</p>
    <form method="POST" action="{{ route('motor.lookup.quotation') }}" style="display:contents">
      @csrf
      <div class="regen-input-row">
        <input type="text" name="number" id="regenInput" placeholder="e.g. GA-2026/001" required>
        <button type="submit" class="btn btn-primary">Retrieve →</button>
      </div>
    </form>
    <div id="regen-result" style="margin-top:14px;font-size:13.5px;color:var(--text-muted)"></div>
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
        <div class="modal-row"><span>Taxes (0.5%)</span><span id="m-tax">—</span></div>
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

<script>
window.MIC_COMPANIES = @json($__companyData);
window.MIC_GUEST = @json(auth()->guest());
window.MIC_MODE = 'all';
window.MIC_SELECTED_COMPANY = null;
window.hardcodedLogos = @json(collect(\App\Models\Company::hardcodedLogoMap())->mapWithKeys(fn($p, $s) => [$s => asset($p)]));
function guestGuard(callback) {
  if (window.MIC_GUEST) { openAuthModal(); return; }
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
@yield('scripts')
</body>
</html>