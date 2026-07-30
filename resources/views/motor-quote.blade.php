@php
    $__companyStats = $companyStats ?? collect();
    $companyColors = ['#003087', '#0066B3', '#00205B', '#D0021B', '#7c3aed', '#0f7a65'];

    $__buildCompanyData = function($companies, $vehicleType) use ($companyColors, $__companyStats) {
        return $companies->filter(fn($c) => in_array($c->company_type, ['both', $vehicleType]))
            ->values()
            ->map(function ($c, $i) use ($companyColors, $__companyStats, $vehicleType) {
                $rate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'Premium Rate');
                $pvtRate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'PVT Rate');
                $pllRate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'PLL Per Person');
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
                    'color' => $companyColors[$i % count($companyColors)],
                    'prefix' => $c->getInitials(),
                    'logoFallback' => $c->logoUrl(),
                    'quote_count' => $stats ? $stats->total : 0,
                    'total_premium' => $stats ? $stats->total_premium : 0,
                ];
            });
    };

    $__privateCompanyData = $__buildCompanyData($companies, 'private');
    $__psvCompanyData = $__buildCompanyData($companies, 'psv');
    $__defaultVehicleType = request()->query('vehicle_type', 'private');
    $__companyData = $__defaultVehicleType === 'psv' ? $__psvCompanyData : $__privateCompanyData;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Get a Motor Insurance Quote | MIC Global Risks</title>
<meta name="description" content="Get a comprehensive motor vehicle insurance quote from Kenya's leading insurers. Compare private and PSV rates instantly.">
<link rel="icon" type="image/x-icon" href="{{ asset('Favicon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@vite(['resources/css/app.css'])
<style>
  .mq-page-hero{
    position:relative;min-height:200px;display:flex;align-items:center;overflow:hidden;
    background:linear-gradient(105deg,rgba(0,30,80,.9),rgba(0,60,130,.7)),url('{{ asset('images/hero/vehicle.jpg') }}') center/cover
  }
  .mq-hero-content{padding:48px 40px;max-width:700px}
  .mq-hero-content h1{color:#fff;font-size:30px;font-weight:700;line-height:1.2;margin-bottom:8px}
  .mq-hero-content p{color:rgba(255,255,255,.8);font-size:15px;line-height:1.6}
</style>
</head>
<body>

<x-public-header/>

<section class="mq-page-hero">
  <div class="mq-hero-content">
    <h1>Get Your Motor Insurance Quote</h1>
    <p>Compare comprehensive cover rates from Kenya's leading insurers. Whether private or PSV, get your quote in minutes.</p>
  </div>
</section>

<section class="section" style="background:#F9F9FF;padding:64px 0" id="vehicle-type-section">
  <div class="form-container">
    <div class="section-head">
      <div class="section-title" style="text-align:center">What type of vehicle do you own?</div>
      <div class="section-sub" style="text-align:center">Select your vehicle category to get a tailored insurance quote.</div>
    </div>
    <div class="vt-grid">
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

<section class="section" style="background:#F9F9FF;display:none" id="quote-form-section">
  <div class="form-container">
    <div class="section-head">
      <button type="button" class="quote-form-back" onclick="closeQuoteFlow()">← Back</button>
      <div class="section-title" style="text-align: center;" id="form-title">Get Your Motor Insurance Quote</div>
      <div class="section-sub" style="text-align: center;" id="form-subtitle">Complete the form below in three easy steps to receive your personalised quotation.</div>
    </div>

    <input type="hidden" id="f-vehicle-type" value="">

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
        <button class="btn btn-primary" onclick="goStep(3)">Continue to Quote Generation →</button>
      </div>
    </div>

    <div class="form-panel" id="panel-3">
      <div class="valuation-box">
        <h3 style="font-size:15px;font-weight:700;margin-bottom:4px;color:var(--text)">Premium Estimate</h3>
        <p style="font-size:13px;color:var(--text-muted);margin-bottom:16px">Select an insurer card below to open its full quotation, or compare all side by side.</p>
        <div id="premium-preview-empty" style="padding:20px;text-align:center;color:var(--text-muted);font-size:13px;border:1.5px dashed var(--border);border-radius:var(--radius)">
          Go back to Step 2 and enter the vehicle value to see premium estimates.
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

<x-public-footer/>

<script>
window.MIC_COMPANIES_PRIVATE = @json($__privateCompanyData);
window.MIC_COMPANIES_PSV = @json($__psvCompanyData);
window.MIC_COMPANIES = @json($__companyData);
window.MIC_VEHICLE_TYPE = '{{ $__defaultVehicleType }}';

function selectVehicleType(type) {
  document.getElementById('f-vehicle-type').value = type;
  window.MIC_VEHICLE_TYPE = type;
  window.MIC_COMPANIES = type === 'psv' ? window.MIC_COMPANIES_PSV : window.MIC_COMPANIES_PRIVATE;
  var paxGroup = document.getElementById('f-pax-group');
  if (paxGroup) paxGroup.style.display = (type === 'psv') ? '' : 'none';
  var title = document.getElementById('form-title');
  var subtitle = document.getElementById('form-subtitle');
  if (type === 'psv') {
    title.textContent = 'Get Your PSV Motor Vehicle Quote';
    subtitle.textContent = 'Complete the form below in three easy steps to receive your PSV quotation.';
  } else {
    title.textContent = 'Get Your Motor Insurance Quote';
    subtitle.textContent = 'Complete the form below in three easy steps to receive your personalised quotation.';
  }
  document.getElementById('vehicle-type-section').style.display = 'none';
  var form = document.getElementById('quote-form-section');
  form.style.display = 'block';
  form.scrollIntoView({ behavior: 'smooth', block: 'start' });
  if (typeof goStep === 'function') goStep(1);
  if (typeof updatePremiumPreview === 'function') updatePremiumPreview();
}

function closeQuoteFlow() {
  document.getElementById('quote-form-section').style.display = 'none';
  document.getElementById('vehicle-type-section').style.display = 'block';
  document.getElementById('vehicle-type-section').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function goStep(n) {
  for (var i = 1; i <= 3; i++) {
    var tab = document.getElementById('step-tab-' + i);
    var panel = document.getElementById('panel-' + i);
    if (tab) tab.classList.toggle('active', i === n);
    if (panel) panel.classList.toggle('active', i === n);
  }
}

function toggleOtherColor(sel) {
  var other = document.getElementById('f-color-other');
  if (other) other.style.display = sel.value === 'Other' ? 'block' : 'none';
}

function validateReg(input) {
  var pattern = /^[A-Za-z]{3}\s?\d{3}[A-Za-z]$/;
  var error = document.getElementById('f-reg-error');
  if (!pattern.test(input.value)) {
    if (error) error.style.display = 'block';
  } else {
    if (error) error.style.display = 'none';
  }
}
</script>
</body>
</html>
