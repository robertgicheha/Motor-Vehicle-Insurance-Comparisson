@inject('settingModel', 'App\Models\Setting')
@php
    $__logoPath = $settingModel->getValue('logo_path', 'images/MIC-Global-Risks_logo.png');
    $__logoUrl = $__logoPath ? (str_starts_with($__logoPath, 'images/') ? asset($__logoPath) : asset('storage/' . $__logoPath)) : asset('images/MIC-Global-Risks_logo.png');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Private Motor Vehicle Comprehensive Cover | MIC Global Risks</title>
<meta name="description" content="Get a comprehensive private motor vehicle insurance quote from Kenya's leading insurers. Compare rates from Sanlam Allianz, GA Insurance and BRITAM.">
<link rel="icon" type="image/x-icon" href="{{ asset('Favicon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@vite(['resources/css/app.css'])
<style>
  .page-hero{position:relative;min-height:340px;display:flex;align-items:center;overflow:hidden;background:linear-gradient(105deg,rgba(0,30,80,.85),rgba(0,60,130,.6)),url('{{ asset('images/hero/vehicle.jpg') }}') center/cover}
  .page-hero-content{position:relative;z-index:2;padding:60px 48px;max-width:700px}
  .page-hero-eyebrow{color:#93c5fd;font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:.12em;margin-bottom:8px}
  .page-hero h1{color:#fff;font-size:32px;font-weight:700;line-height:1.2;margin-bottom:12px}
  .page-hero p{color:rgba(255,255,255,.8);font-size:16px;line-height:1.6;margin-bottom:24px}
  .page-hero .btn-hero{display:inline-flex;align-items:center;gap:8px;background:#D0021B;color:#fff;padding:12px 28px;border-radius:30px;font-weight:600;font-size:14px;text-decoration:none;transition:background .2s}
  .page-hero .btn-hero:hover{background:#a00015}
  .page-section{padding:70px 40px;max-width:1200px;margin:0 auto}
  .page-section-alt{background:#F9F9FF}
  .section-label{color:var(--blue);font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.1em;margin-bottom:6px}
  .section-title{font-size:26px;font-weight:700;color:var(--text);margin-bottom:12px;text-align:center}
  .section-sub{font-size:14px;color:var(--text-muted);text-align:center;max-width:640px;margin:0 auto 40px}
  .benefits-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
  .benefit-card{background:#fff;border:1.5px solid var(--border);border-radius:var(--radius-lg);padding:24px 20px;text-align:center;transition:box-shadow .2s,transform .15s}
  .benefit-card:hover{box-shadow:0 4px 16px rgba(0,0,0,.08);transform:translateY(-2px)}
  .benefit-icon{width:48px;height:48px;border-radius:12px;background:var(--blue-light);display:flex;align-items:center;justify-content:center;margin:0 auto 14px;color:var(--blue)}
  .benefit-card h4{font-size:14px;font-weight:600;margin-bottom:6px}
  .benefit-card p{font-size:12.5px;color:var(--text-muted);line-height:1.5}
  .rate-cards-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px}
  .rate-card{background:#fff;border:1.5px solid var(--border);border-radius:var(--radius);padding:20px;text-align:center}
  .rate-card .rate-value{font-size:28px;font-weight:700;color:var(--blue);margin:8px 0 4px}
  .rate-card .rate-label{font-size:12px;color:var(--text-muted);margin-bottom:6px}
  .rate-card .rate-desc{font-size:11px;color:var(--text-muted);line-height:1.4}
  .company-compare{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-top:24px}
  .company-card{background:#fff;border:1.5px solid var(--border);border-radius:var(--radius-lg);padding:24px 20px;text-align:center}
  .company-card .cc-logo{height:44px;margin-bottom:14px;object-fit:contain}
  .company-card h4{font-size:15px;font-weight:700;margin-bottom:10px}
  .company-card .cc-rate{font-size:22px;font-weight:700;color:var(--blue);margin-bottom:4px}
  .company-card .cc-label{font-size:11px;color:var(--text-muted);margin-bottom:12px}
  .company-card .cc-features{text-align:left;font-size:12px;line-height:1.8;color:var(--text);margin-bottom:16px}
  .company-card .cc-features li{list-style:none;display:flex;align-items:center;gap:6px}
  .company-card .cc-features li::before{content:'\2713';color:#16a34a;font-weight:700}
  .company-card .btn-cc{display:block;background:var(--blue);color:#fff;text-align:center;padding:8px;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none}
  .company-card .btn-cc:hover{background:var(--blue-dark)}
  .steps-alt{display:grid;grid-template-columns:repeat(5,1fr);gap:16px}
  .step-alt-card{background:#fff;border:1.5px solid var(--border);border-radius:var(--radius);padding:20px 16px;text-align:center;position:relative}
  .step-alt-num{width:36px;height:36px;border-radius:50%;background:var(--blue);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;margin:0 auto 12px}
  .step-alt-card h4{font-size:13px;font-weight:600;margin-bottom:6px}
  .step-alt-card p{font-size:11.5px;color:var(--text-muted);line-height:1.5}
  .step-connector{display:flex;align-items:center;justify-content:center;color:var(--border);font-size:20px}
  .cta-section{background:linear-gradient(105deg,#004f8c,#0066B3);padding:60px 40px;text-align:center;color:#fff}
  .cta-section h2{font-size:28px;font-weight:700;margin-bottom:12px}
  .cta-section p{font-size:15px;opacity:.85;margin-bottom:24px;max-width:600px;margin-left:auto;margin-right:auto}
  .cta-section .btn-cta{display:inline-flex;align-items:center;gap:8px;background:#D0021B;color:#fff;padding:14px 36px;border-radius:30px;font-weight:600;font-size:15px;text-decoration:none;transition:background .2s}
  .cta-section .btn-cta:hover{background:#a00015}
  .contact-grid{display:grid;grid-template-columns:1fr 1fr;gap:40px;align-items:start}
  .contact-info h3{font-size:18px;font-weight:700;margin-bottom:12px}
  .contact-info p{font-size:13px;color:var(--text-muted);line-height:1.7;margin-bottom:16px}
  .contact-info .ci-item{display:flex;align-items:center;gap:10px;margin-bottom:10px;font-size:13px;color:var(--text)}
  .contact-info .ci-item svg{flex-shrink:0;color:var(--blue)}
  .contact-form .form-group{margin-bottom:14px}
  .contact-form label{display:block;font-size:12px;font-weight:600;margin-bottom:4px}
  .contact-form input,.contact-form textarea,.contact-form select{width:100%;padding:10px 12px;border:1.5px solid var(--border);border-radius:var(--radius);font-size:13px;font-family:'Montserrat',sans-serif;outline:none}
  .contact-form input:focus,.contact-form textarea:focus,.contact-form select:focus{border-color:var(--blue)}
  .contact-form .btn-submit{background:var(--blue);color:#fff;padding:10px 28px;border:none;border-radius:30px;font-weight:600;font-size:13px;cursor:pointer}
  .contact-form .btn-submit:hover{background:var(--blue-dark)}
  @media(max-width:768px){.benefits-grid,.rate-cards-grid{grid-template-columns:1fr 1fr}.company-compare,.steps-alt,.contact-grid{grid-template-columns:1fr}.page-hero-content{padding:40px 24px}.page-section{padding:40px 20px}.rate-cards-grid{grid-template-columns:repeat(2,1fr)}}
</style>
</head>
<body>

<x-public-header/>

<section class="page-hero">
  <div class="page-hero-content">
    <div class="page-hero-eyebrow">Motor Insurance</div>
    <h1>Private Motor Vehicle<br>Comprehensive Cover</h1>
    <p>Protect your personal vehicle with comprehensive insurance from Kenya's leading insurers. Get instant quotes, compare premiums, and download your policy — all online.</p>
    <a href="javascript:void(0)" onclick="showPrivateQuoteForm()" class="btn-hero">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 18 15 12 9 6"/></svg>
      Get a Private Vehicle Quote
    </a>
  </div>
</section>

<section class="page-section">
  <div class="section-label" style="text-align:center">Comprehensive Cover</div>
  <h2 class="section-title">What Your Private Motor Insurance Covers</h2>
  <p class="section-sub">Our comprehensive private motor vehicle policy includes the following benefits and limits.</p>
  <div class="benefits-grid">
    <div class="benefit-card">
      <div class="benefit-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M13 2 3 14h8l-1 8 10-12h-8l1-8Z"/></svg>
      </div>
      <h4>Windscreen / Window Glass</h4>
      <p>Cover for windscreen and window glass damage up to Kshs. 75,000 (Sanlam &amp; GA) or Kshs. 50,000 (BRITAM).</p>
    </div>
    <div class="benefit-card">
      <div class="benefit-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
      </div>
      <h4>Entertainment Unit</h4>
      <p>Cover for your car's entertainment system up to Kshs. 75,000 (Sanlam &amp; GA) or Kshs. 50,000 (BRITAM).</p>
    </div>
    <div class="benefit-card">
      <div class="benefit-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/></svg>
      </div>
      <h4>Towing &amp; Recovery</h4>
      <p>Towing and recovery expenses after an accident covered up to Kshs. 75,000 (Sanlam &amp; GA) or Kshs. 50,000 (BRITAM).</p>
    </div>
    <div class="benefit-card">
      <div class="benefit-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      </div>
      <h4>Authorized Repair</h4>
      <p>Repair authority cover up to Kshs. 100,000 (Sanlam &amp; GA) or Kshs. 50,000 (BRITAM).</p>
    </div>
    <div class="benefit-card">
      <div class="benefit-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
      </div>
      <h4>Emergency Medical Expenses</h4>
      <p>Emergency medical cover for occupants (Kshs. 50,000) and third party (Kshs. 50,000 for Sanlam &amp; BRITAM).</p>
    </div>
    <div class="benefit-card">
      <div class="benefit-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      </div>
      <h4>Vehicle Valuation</h4>
      <p>Free vehicle valuation at renewal and inception across all insurers.</p>
    </div>
  </div>
</section>

<section class="page-section page-section-alt">
  <div class="section-label" style="text-align:center">Key Rates</div>
  <h2 class="section-title">Premium Rates at a Glance</h2>
  <p class="section-sub">Understanding the key rates that determine your comprehensive motor vehicle premium.</p>
  <div class="rate-cards-grid">
    <div class="rate-card">
      <div class="rate-label">Premium Rate</div>
      <div class="rate-value">4%</div>
      <div class="rate-desc">of vehicle value for most insurers (Sanlam: 4% up to 2.5M, 3% above; BRITAM: 3.5% incl. PVT)</div>
    </div>
    <div class="rate-card">
      <div class="rate-label">PVT (Private Vehicle Tax)</div>
      <div class="rate-value">0.25%</div>
      <div class="rate-desc">Min Kshs. 2,500-3,000 per unit depending on insurer. Included in BRITAM's rate.</div>
    </div>
    <div class="rate-card">
      <div class="rate-label">Excess Protector</div>
      <div class="rate-value">0.25%</div>
      <div class="rate-desc">Reinstatement at 0.25% of sum insured. Min Kshs. 2,500-5,000 depending on insurer.</div>
    </div>
    <div class="rate-card">
      <div class="rate-label">Min Sum Insured</div>
      <div class="rate-value">Kshs. 500,000</div>
      <div class="rate-desc">Minimum vehicle value for comprehensive cover across all partner insurers.</div>
    </div>
  </div>
</section>

<section class="page-section">
  <div class="section-label" style="text-align:center">Our Insurers</div>
  <h2 class="section-title">Compare Private Motor Insurers</h2>
  <p class="section-sub">Choose from Kenya's leading insurance companies. Compare their rates and benefits side by side.</p>
  <div class="company-compare">
    @foreach($companyData as $cd)
    <div class="company-card" style="border-top:4px solid {{ $cd['color'] }}">
      @if($cd['logo'])
      <img src="{{ $cd['logo'] }}" alt="{{ $cd['name'] }}" class="cc-logo" onerror="this.style.display='none'">
      @endif
      <h4>{{ $cd['name'] }}</h4>
      <div class="cc-rate">{{ $cd['rate_display'] }}</div>
      <div class="cc-label">Premium Rate</div>
      <ul class="cc-features">
        <li>Comprehensive cover</li>
        <li>Windscreen &amp; entertainment cover</li>
        <li>Towing &amp; recovery expenses</li>
        <li>Third party liability</li>
        <li>Excess protector available</li>
      </ul>
      <a href="javascript:void(0)" onclick="showPrivateQuoteForm()" class="btn-cc">Get Quote →</a>
    </div>
    @endforeach
  </div>
</section>

<!-- ── PRIVATE QUOTE FORM ── -->
<section class="section" style="background:#F9F9FF;display:none" id="quote-form-section-private">
  <div class="form-container">
   <div class="section-head">
    <button type="button" class="quote-form-back" onclick="closeQuoteFlow('private')">← Back</button>
     <div class="section-title" style="text-align: center;">Get Your Motor Insurance Quote</div>
      <div class="section-sub" style="text-align: center;">Complete the form below in three easy steps to receive your personalised quotation.</div>
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
          <label>Engine No</label>
          <input type="text" id="f-engine-private" placeholder="e.g. CBA1234567">
        </div>
        <div class="form-group">
          <label>Make of Vehicle <span class="req">*</span></label>
          <input type="text" id="f-make-private" placeholder="e.g. Toyota, Nissan, Mazda">
        </div>
        <div class="form-group">
          <label>Model Name <span class="req">*</span></label>
          <input type="text" id="f-model-private" placeholder="e.g. Corolla, Hilux, Premio">
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

<section class="page-section page-section-alt">
  <div class="section-label" style="text-align:center">How It Works</div>
  <h2 class="section-title">Get Your Private Motor Quote in 5 Simple Steps</h2>
  <p class="section-sub">Follow these steps to receive your comprehensive motor vehicle quotation.</p>
  <div style="display:flex;align-items:flex-start;gap:0;margin-top:8px">
    <div class="steps-alt" style="flex:1">
      <div class="step-alt-card">
        <div class="step-alt-num">1</div>
        <h4>Enter Personal Details</h4>
        <p>Input your name, contact information and ID/KRA PIN to get started.</p>
      </div>
      <div class="step-alt-card">
        <div class="step-alt-num">2</div>
        <h4>Add Vehicle Details</h4>
        <p>Provide your vehicle's make, model, year of manufacture, registration and value.</p>
      </div>
      <div class="step-alt-card">
        <div class="step-alt-num">3</div>
        <h4>Generate Quotation</h4>
        <p>Our system generates a comprehensive cover quotation instantly.</p>
      </div>
      <div class="step-alt-card">
        <div class="step-alt-num">4</div>
        <h4>Compare Insurers</h4>
        <p>View and compare premiums from all leading underwriters side by side.</p>
      </div>
      <div class="step-alt-card">
        <div class="step-alt-num">5</div>
        <h4>Download &amp; Proceed</h4>
        <p>Download your preferred quotation and proceed to request for a sticker.</p>
      </div>
    </div>
  </div>
</section>

<section class="cta-section">
  <h2>Ready to Protect Your Vehicle?</h2>
  <p>Get your instant comprehensive motor vehicle insurance quote today. It takes just a few minutes.</p>
  <a href="javascript:void(0)" onclick="showPrivateQuoteForm()" class="btn-cta">
    Get Your Private Vehicle Quote
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 18 15 12 9 6"/></svg>
  </a>
</section>

<section class="page-section" style="max-width:900px">
  <div class="section-label" style="text-align:center">Contact Us</div>
  <h2 class="section-title">Have Questions About Private Motor Cover?</h2>
  <p class="section-sub">Get in touch with our team for personalised assistance with your motor insurance needs.</p>
  <div class="contact-grid">
    <div class="contact-info">
      <h3>Get In Touch</h3>
      <p>Our team is ready to help you with any questions about private motor vehicle comprehensive insurance. Reach out via phone, email, or visit our office.</p>
      <div class="ci-item">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        <span>+254 (0) 20 123 4567</span>
      </div>
      <div class="ci-item">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        <span>info@micglobalrisks.com</span>
      </div>
      <div class="ci-item">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        <span>6th Floor, 9 West, Ring Road Parklands, Nairobi</span>
      </div>
    </div>
    <form class="contact-form" method="POST" action="#">
      @csrf
      <div class="form-group">
        <label>Your Name <span style="color:var(--red)">*</span></label>
        <input type="text" name="name" required placeholder="John Doe">
      </div>
      <div class="form-group">
        <label>Email Address <span style="color:var(--red)">*</span></label>
        <input type="email" name="email" required placeholder="john@example.com">
      </div>
      <div class="form-group">
        <label>Phone Number</label>
        <input type="tel" name="phone" placeholder="07XX XXX XXX">
      </div>
      <div class="form-group">
        <label>Subject <span style="color:var(--red)">*</span></label>
        <select name="subject" required>
          <option value="">Select...</option>
          <option>Private Motor Insurance Quote</option>
          <option>Policy Inquiry</option>
          <option>Claims Assistance</option>
          <option>General Enquiry</option>
        </select>
      </div>
      <div class="form-group">
        <label>Message <span style="color:var(--red)">*</span></label>
        <textarea name="message" rows="4" required placeholder="How can we help you?"></textarea>
      </div>
      <button type="submit" class="btn-submit">Send Message →</button>
    </form>
  </div>
</section>

<x-public-footer/>
<script src="{{ asset('js/home.js') }}"></script>
<script>
window.MIC_COMPANIES = @json($companyData);
window.MIC_VEHICLE_TYPE = 'private';
(function() {
  var sel = document.getElementById('f-year-private');
  if (sel) {
    for (var y = 2026; y >= 1990; y--) {
      var o = document.createElement('option');
      o.value = y; o.textContent = y; sel.appendChild(o);
    }
  }
})();

document.addEventListener('DOMContentLoaded', function() {
  if (typeof initValueInput === 'function') initValueInput('private');
});

function showPrivateQuoteForm() {
  var form = document.getElementById('quote-form-section-private');
  if (form) {
    form.style.display = 'block';
    form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    if (typeof goStep === 'function') goStep('private', 1);
  }
}

function closeQuoteFlow(type) {
  var form = document.getElementById(type === 'psv' ? 'quote-form-section-psv' : 'quote-form-section-private');
  if (form) form.style.display = 'none';
}

function validateReg(type, input) {
  var pattern = /^[A-Za-z]{3}\s?\d{3}[A-Za-z]$/;
  var error = document.getElementById('f-reg-error-' + type);
  if (!pattern.test(input.value)) {
    if (error) error.style.display = 'block';
  } else {
    if (error) error.style.display = 'none';
  }
}

function toggleOtherColor(type, sel) {
  var other = document.getElementById('f-color-other-' + type);
  if (other) other.style.display = sel.value === 'Other' ? 'block' : 'none';
}
</script>
</body>
</html>
