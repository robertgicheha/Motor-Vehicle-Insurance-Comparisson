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
@vite(['resources/css/app.css'])
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<x-public-header/>

<!-- HERO -->
<section class="hero">
  <div class="hero-carousel" id="heroCarousel">
    <div class="hero-slide active" style="background-image:url('{{ asset('images/hero/vehicle.jpg') }}')">
      <div class="hero-slide-overlay"></div>
    </div>
  </div>
  <div class="hero-content">
    <div class="hero-eyebrow">Motor Insurance</div>
    <h1>
      @if($mode === 'single' && $selectedCompany)
        {{ $selectedCompany->name }}<br><span style="font-size:0.6em">Insurance Quote</span>
      @elseif($mode === 'compare')
        Compare All Insurers<br><span style="font-size:0.6em">Side-by-Side Comparison</span>
      @else
        Protect Your <span>Vehicle</span><br>With Confidence
      @endif
    </h1>
    <p>
      @if($mode === 'single' && $selectedCompany)
        Get an instant comprehensive motor insurance quote from {{ $selectedCompany->name }}. Enter your details below for a tailored premium.
      @elseif($mode === 'compare')
        Compare premiums from all leading insurers side by side. Find the best rate for your vehicle.
      @else
        Get an instant comprehensive motor insurance quote from Kenya's leading insurers.
      @endif
    </p>
    <button class="btn btn-red hero-cta" onclick="document.querySelector('.section').scrollIntoView({behavior:'smooth'})">Get a Quote ↓</button>
  </div>
  <div class="hero-dots" id="heroDots">
    <button class="hero-dot active" onclick="heroGoTo(0)" aria-label="Slide 1"></button>
  </div>
  <button class="hero-arrow hero-arrow-next" onclick="heroStep(1)" aria-label="Next slide">&#8250;</button>
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
          Rate: <strong>{{ $selectedCompany->rates->firstWhere('item_name', 'Premium Rate')?->percentage ?? '—' }}%</strong> of vehicle value
        @else
          Complete the form below in three easy steps to receive your personalised quotation.
        @endif
      </div>
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

<!-- CHOOSE YOUR INSURER -->
<section class="section section-alt" style="background-color: #005F9E;">
  <div style="text-align:center;margin-bottom:20px">
    <div class="section-title" style="color:#F9F9FF;font-size:20px">Choose Your Insurer</div>
    <div class="section-sub" style="color:#F9F9FF;font-size:15px;margin-top:4px">Select an insurer to generate a tailored quote.</div>
  </div>
  <div class="company-cards">
    @foreach ($companyData as $cd)
    <a href="{{ route('generate.quote', $cd['slug']) }}" class="company-card no-underline" style="cursor:pointer;display:block">
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
    <a href="{{ route('generate.quote', 'compare-all') }}" class="company-card no-underline" style="cursor:pointer;display:block">
      <div class="company-logo-box"><img src="{{ asset('img/benchmarking.png') }}" alt="Compare All"></div>
      <div class="company-name">Compare All</div>
      <div class="company-rate">Compare rates from all insurers</div>
      <button class="btn btn-primary" style="width:100%;justify-content:center;font-size:11px;padding:5px 10px">Compare All ↗</button>
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
window.MIC_GUEST = @json(auth()->guest());
window.MIC_MODE = @json($mode);
window.MIC_SELECTED_COMPANY = @json($selectedCompany?->slug);

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
    sessionStorage.setItem('auth_redirect', '/quotations?pending_quote=1');
    var alpineEl = document.querySelector('#authModal');
    if (alpineEl && alpineEl.__x) { alpineEl.__x.$data.redirectTo = '/quotations?pending_quote=1'; }
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
