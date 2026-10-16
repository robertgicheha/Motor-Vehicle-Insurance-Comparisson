@inject('settingModel', 'App\Models\Setting')
@php
    $__logoPath = $settingModel->getValue('logo_path', 'images/LOLC-Kenya.webp');
    $__logoUrl = $__logoPath ? (str_starts_with($__logoPath, 'images/') ? asset($__logoPath) : asset('storage/' . $__logoPath)) : asset('images/LOLC-Kenya.webp');
    $__primaryColor = '#ED1D25';
    $__primaryDark = '#B6161C';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $selectedCompany->name }} PSV Insurance | LOLC Microfinance Bank</title>
<meta name="description" content="Get a PSV motor insurance quote from {{ $selectedCompany->name }}. Comprehensive cover for public service vehicles.">
<link rel="icon" type="image/x-icon" href="{{ asset('Favicon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@vite(['resources/css/app.css'])
<style>
  .page-hero{position:relative;min-height:380px;display:flex;align-items:center;overflow:hidden;background:linear-gradient(105deg,rgba(120,0,0,.85),rgba(180,10,10,.65)),url('{{ asset('images/hero/family-car.jpg') }}') center/cover}
  .page-hero-content{position:relative;z-index:2;padding:60px 48px;max-width:700px}
  .page-hero-eyebrow{color:#fca5a5;font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:.12em;margin-bottom:8px}
  .page-hero h1{color:#fff;font-size:32px;font-weight:700;line-height:1.2;margin-bottom:12px}
  .page-hero p{color:rgba(255,255,255,.85);font-size:16px;line-height:1.6;margin-bottom:24px}
  .page-hero .btn-hero{display:inline-flex;align-items:center;gap:8px;background:{{ $__primaryColor }};color:#fff;padding:12px 28px;border-radius:30px;font-weight:600;font-size:14px;text-decoration:none;transition:background .2s}
  .page-hero .btn-hero:hover{background:{{ $__primaryDark }}}
  .page-section{padding:70px 40px;max-width:1200px;margin:0 auto}
  .page-section-alt{background:#F9F9FF}
  .section-label{color:{{ $__primaryColor }};font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.1em;margin-bottom:6px}
  .section-title{font-size:26px;font-weight:700;color:var(--text);margin-bottom:12px;text-align:center}
  .section-sub{font-size:14px;color:var(--text-muted);text-align:center;max-width:640px;margin:0 auto 40px}
  .benefits-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
  .benefit-card{background:#fff;border:1.5px solid var(--border);border-radius:var(--radius-lg);padding:24px 20px;text-align:center;transition:box-shadow .2s,transform .15s}
  .benefit-card:hover{box-shadow:0 4px 16px rgba(0,0,0,.08);transform:translateY(-2px)}
  .benefit-icon{width:48px;height:48px;border-radius:12px;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;color:{{ $__primaryColor }}}
  .benefit-card h4{font-size:14px;font-weight:600;margin-bottom:6px}
  .benefit-card p{font-size:12.5px;color:var(--text-muted);line-height:1.5}
  .company-logo-hero{height:52px;margin-bottom:16px;object-fit:contain}
  .form-container{max-width:820px;margin:0 auto;background:#fff;border-radius:var(--radius-lg);padding:40px 36px;box-shadow:0 2px 20px rgba(0,0,0,.06)}
  .form-steps{display:flex;gap:0;margin-bottom:28px;border-radius:10px;overflow:hidden;border:1.5px solid var(--border)}
  .form-step{flex:1;padding:14px 10px;text-align:center;font-size:12px;font-weight:600;cursor:pointer;background:#fff;color:var(--text-muted);transition:all .2s;border-right:1.5px solid var(--border)}
  .form-step:last-child{border-right:none}
  .form-step.active{background:{{ $__primaryColor }};color:#fff}
  .form-step .step-num{font-size:10px;font-weight:400;opacity:.75}
  .form-panel{display:none}
  .form-panel.active{display:block}
  .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
  .form-grid.three{grid-template-columns:1fr 1fr 1fr}
  .form-group{margin-bottom:0}
  .form-group label{display:block;font-size:12px;font-weight:600;margin-bottom:4px;color:var(--text)}
  .form-group label .req{color:{{ $__primaryColor }}}
  .form-group input,.form-group select{width:100%;padding:10px 12px;border:1.5px solid var(--border);border-radius:var(--radius);font-size:13px;font-family:'Montserrat',sans-serif;outline:none;transition:border-color .2s;box-sizing:border-box}
  .form-group input:focus,.form-group select:focus{border-color:{{ $__primaryColor }}}
  .btn-group{display:flex;gap:10px;justify-content:flex-end;margin-top:20px}
  .btn{padding:10px 24px;border-radius:8px;font-weight:600;font-size:13px;cursor:pointer;border:none;transition:all .2s;font-family:'Montserrat',sans-serif}
  .btn-primary{background:{{ $__primaryColor }};color:#fff}
  .btn-primary:hover{background:{{ $__primaryDark }}}
  .btn-secondary{background:#e5e7eb;color:#374151}
  .btn-secondary:hover{background:#d1d5db}
  .valuation-box{background:#F9F9FF;border-radius:var(--radius);padding:24px}
  .preview-card{transition:box-shadow .2s,transform .15s}
  .preview-card:hover{box-shadow:0 4px 16px rgba(0,0,0,.1);transform:translateY(-2px)}
  .company-card{background:#fff;border:1.5px solid var(--border);border-radius:var(--radius-lg);padding:20px;text-align:center;transition:box-shadow .2s,transform .15s}
  .company-card:hover{box-shadow:0 4px 16px rgba(0,0,0,.08);transform:translateY(-2px)}
  .cta-section{background:linear-gradient(105deg,#8a0000,{{ $__primaryColor }});padding:60px 40px;text-align:center;color:#fff}
  .cta-section h2{font-size:28px;font-weight:700;margin-bottom:12px}
  .cta-section p{font-size:15px;opacity:.85;margin-bottom:24px;max-width:600px;margin-left:auto;margin-right:auto}
  .cta-section .btn-cta{display:inline-flex;align-items:center;gap:8px;background:#fff;color:{{ $__primaryColor }};padding:14px 36px;border-radius:30px;font-weight:600;font-size:15px;text-decoration:none;transition:background .2s}
  .cta-section .btn-cta:hover{background:#f3f4f6}
  .regen-box{max-width:500px;margin:0 auto;text-align:center;background:#F9F9FF;border-radius:var(--radius-lg);padding:40px 32px}
  .regen-box .regen-icon{font-size:36px;margin-bottom:12px}
  .regen-box h3{font-size:17px;font-weight:700;margin-bottom:8px}
  .regen-box p{font-size:13px;color:var(--text-muted);margin-bottom:20px}
  .regen-input-row{display:flex;gap:8px}
  .regen-input-row input{flex:1;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius);font-size:13px;font-family:'Montserrat',sans-serif;outline:none}
  .regen-input-row input:focus{border-color:{{ $__primaryColor }}}
  .regen-input-row button{white-space:nowrap}
  .vehicle-search-wrap{position:relative}
  .vehicle-dropdown{position:absolute;top:100%;left:0;right:0;background:#fff;border:1.5px solid var(--border);border-top:none;border-radius:0 0 var(--radius) var(--radius);max-height:200px;overflow-y:auto;z-index:10;display:none}
  .vehicle-option{padding:8px 12px;font-size:13px;cursor:pointer}
  .vehicle-option:hover{background:var(--blue-light)}
  .value-range-hint{font-size:11px;color:var(--text-muted);margin-top:2px}
  .value-error{color:{{ $__primaryColor }};font-size:11px;margin-top:2px}
  .min-sum-hint{font-size:11px;color:var(--text-muted);margin-top:4px}
  .reg-error{color:{{ $__primaryColor }};font-size:11px;display:none;margin-top:2px}
  .company-cards{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;max-width:1100px;margin:0 auto;padding:0 20px}
  .company-logo-box{height:44px;display:flex;align-items:center;justify-content:center;margin-bottom:10px}
  .company-logo-box img{max-height:40px;max-width:120px;object-fit:contain}
  .company-name{font-size:14px;font-weight:700;margin-bottom:4px}
  .company-rate{font-size:12px;color:var(--text-muted);margin-bottom:12px}
  .no-underline{text-decoration:none;color:inherit}
  @media(max-width:768px){.benefits-grid,.company-cards,.form-grid.three{grid-template-columns:1fr 1fr}.form-grid{grid-template-columns:1fr}.page-hero-content{padding:40px 24px}.page-section{padding:40px 20px}.form-container{padding:24px 16px}.company-cards{grid-template-columns:repeat(2,1fr)}}
</style>
</head>
<body>

<x-public-header/>

<section class="page-hero">
  <div class="page-hero-content">
    @if ($selectedCompany->logoUrl())
    <img src="{{ $selectedCompany->logoUrl() }}" alt="{{ $selectedCompany->name }}" class="company-logo-hero">
    @endif
    <div class="page-hero-eyebrow">PSV Motor Insurance</div>
    <h1>{{ $selectedCompany->name }}<br>PSV Comprehensive Cover</h1>
    <p>Protect your public service vehicle with {{ $selectedCompany->name }}'s comprehensive PSV insurance. Get an instant quote, compare premiums, and download your policy — all online.</p>
    <a href="javascript:void(0)" onclick="document.getElementById('quoteForm').scrollIntoView({behavior:'smooth'})" class="btn-hero">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 18 15 12 9 6"/></svg>
      Get Your PSV Quote
    </a>
  </div>
</section>

<section class="page-section">
  <div class="section-label" style="text-align:center">Comprehensive Cover</div>
  <h2 class="section-title">What Your PSV Motor Insurance Covers</h2>
  <p class="section-sub">Our comprehensive PSV motor vehicle policy includes the following benefits and limits designed for public service vehicles.</p>
  <div class="benefits-grid">
    <div class="benefit-card">
      <div class="benefit-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M13 2 3 14h8l-1 8 10-12h-8l1-8Z"/></svg>
      </div>
      <h4>Windscreen / Glass Cover</h4>
      <p>Cover for windscreen and window glass damage up to Kshs. 50,000 (Sanlam), Kshs. 75,000 (GA) or Kshs. 30,000 (Definite).</p>
    </div>
    <div class="benefit-card">
      <div class="benefit-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
      </div>
      <h4>Passenger Liability</h4>
      <p>Passenger legal liability cover up to Kshs. 3,000,000 per person and Kshs. 20,000,000 per event.</p>
    </div>
    <div class="benefit-card">
      <div class="benefit-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/></svg>
      </div>
      <h4>Towing &amp; Recovery</h4>
      <p>Towing and recovery expenses after an accident covered up to Kshs. 50,000 (Sanlam), Kshs. 75,000 (GA) or Kshs. 50,000 (Definite).</p>
    </div>
    <div class="benefit-card">
      <div class="benefit-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
      </div>
      <h4>Medical Expenses</h4>
      <p>Emergency medical cover for occupants up to Kshs. 50,000 (Sanlam &amp; GA) or Kshs. 30,000 (Definite).</p>
    </div>
    <div class="benefit-card">
      <div class="benefit-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      </div>
      <h4>Authorized Repair</h4>
      <p>Repair authority cover up to Kshs. 50,000 (Sanlam &amp; Definite) or Kshs. 100,000 (GA).</p>
    </div>
    <div class="benefit-card">
      <div class="benefit-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      </div>
      <h4>PLL (Passenger Legal Liability)</h4>
      <p>Passenger legal liability calculated at Kshs. 500 per passenger seat across all PSV insurers.</p>
    </div>
  </div>
</section>

<section class="page-section page-section-alt" id="quoteForm">
  <div class="form-container">
    <div class="section-head" style="margin-bottom:20px">
      <div class="section-title" style="font-size:20px">{{ $selectedCompany->name }} — PSV Quote</div>
      <div class="section-sub" style="margin-bottom:0">
        Rate: <strong>
          @php
            $__rate = $selectedCompany->rates->where('vehicle_type', 'psv')->firstWhere('item_name', 'Premium Rate');
          @endphp
          {{ $__rate ? (float)$__rate->percentage . '%' : '—' }}
        </strong> of vehicle value
      </div>
    </div>

    <div class="form-steps">
      <div class="form-step active" onclick="goStep(1)">
        <div class="step-num">Step 1</div>
        <div>Personal Details</div>
      </div>
      <div class="form-step" onclick="goStep(2)">
        <div class="step-num">Step 2</div>
        <div>Vehicle Details</div>
      </div>
      <div class="form-step" onclick="goStep(3)">
        <div class="step-num">Step 3</div>
        <div>Quote Generation</div>
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
        <button class="btn btn-primary" onclick="validateStep1AndProceed('', function(){ goStep(2) })">Continue to Vehicle Details →</button>
      </div>
    </div>

    <div class="form-panel" id="panel-2">
      <div class="form-grid">
        <div class="form-group">
          <label>Registration Number <span class="req">*</span></label>
          <input type="text" id="f-reg" placeholder="e.g. KDH 724V">
          <div class="reg-error" id="f-reg-error">Invalid Kenyan registration format (e.g. KDH 291H)</div>
        </div>
        <div class="form-group" id="f-pax-group">
          <label>Number of Passengers (Pax) <span class="req">*</span></label>
          <input type="number" id="f-pax" placeholder="e.g. 14" min="1" value="1">
          <div style="font-size:11px;color:var(--text-muted);margin-top:2px">Used to calculate Passenger Legal Liability (PLL)</div>
        </div>
        <div class="form-group">
          <label>Chassis / Frame Number</label>
          <input type="text" id="f-chassis" placeholder="e.g. WVWZZZ1KZ9W123456">
        </div>
        <div class="form-group">
          <label>Engine No</label>
          <input type="text" id="f-engine" placeholder="e.g. CBA1234567">
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
          <label>Year of Manufacture <span class="req">*</span></label>
          <select id="f-year"><option value="">Select year...</option></select>
        </div>
        <div class="form-group">
          <label>Body Type <span class="req">*</span></label>
          <input type="text" id="f-body" placeholder="e.g. Saloon, SUV, Pick-up" pattern="[A-Za-z\s\-]{2,50}" title="Letters only, 2-50 characters" autocomplete="off">
          <div class="value-error" id="f-body-error" style="display:none">Please enter a valid body type (letters only).</div>
        </div>
        <div class="form-group">
          <label>Value of Vehicle (KES) <span class="req">*</span></label>
          <input type="text" id="f-value" placeholder="e.g. 800,000" autocomplete="off">
          <div class="value-error" id="f-value-error" style="display:none"></div>
        </div>
      </div>
      <div class="btn-group">
        <button class="btn btn-secondary" onclick="goStep(1)">← Back</button>
        <button class="btn btn-primary" onclick="validateStep2AndProceed()">Continue to Quote Generation →</button>
      </div>
    </div>

    <div class="form-panel" id="panel-3">
      <div class="valuation-box">
        <h3 style="font-size:15px;font-weight:700;margin-bottom:4px;color:var(--text)">Premium Estimate</h3>
        <p style="font-size:13px;color:var(--text-muted);margin-bottom:16px">Your quote from <strong>{{ $selectedCompany->name }}</strong> is ready.</p>

        <div id="premium-preview-empty" style="padding:20px;text-align:center;color:var(--text-muted);font-size:13px;border:1.5px dashed var(--border);border-radius:var(--radius)">
          ← Go back to Step 2 and enter the vehicle value to see premium estimates.
        </div>

        <div id="premium-preview" style="display:none">
          <div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:14px">Your {{ $selectedCompany->name }} quote</div>
          <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(min(160px,100%),1fr));gap:12px" id="premium-cards-grid"></div>
        </div>
      </div>
      <div class="btn-group">
        <button class="btn btn-secondary" onclick="goStep(2)">← Back to Vehicle Details</button>
      </div>
    </div>
  </div>
</section>

<section class="page-section" style="background:#fff">
  <div class="section-head" style="text-align:center;margin-bottom:40px">
    <div class="section-title">Why Choose {{ $selectedCompany->name }} for PSV?</div>
    <div class="section-sub">{{ $selectedCompany->name }} provides comprehensive PSV insurance tailored for public service vehicles.</div>
  </div>
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(min(260px,100%),1fr));gap:24px;max-width:1100px;margin:0 auto;padding:0 20px">
    <div style="background:#F9F9FF;border-radius:16px;padding:32px 24px;text-align:center;transition:transform .2s,box-shadow .2s"
         onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 25px rgba(0,0,0,0.08)'"
         onmouseout="this.style.transform='';this.style.boxShadow=''">
      <div style="width:52px;height:52px;background:{{ $__primaryColor }};border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:24px">🛡️</div>
      <h3 style="font-size:16px;font-weight:700;margin-bottom:8px;color:#1a1a2e">Comprehensive PSV Cover</h3>
      <p style="font-size:13px;color:#6b7280;line-height:1.6">Full coverage designed specifically for public service vehicles, including passenger liability and third-party risks.</p>
    </div>
    <div style="background:#F9F9FF;border-radius:16px;padding:32px 24px;text-align:center;transition:transform .2s,box-shadow .2s"
         onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 25px rgba(0,0,0,0.08)'"
         onmouseout="this.style.transform='';this.style.boxShadow=''">
      <div style="width:52px;height:52px;background:{{ $__primaryColor }};border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:24px">⚡</div>
      <h3 style="font-size:16px;font-weight:700;margin-bottom:8px;color:#1a1a2e">Instant PSV Quotes</h3>
      <p style="font-size:13px;color:#6b7280;line-height:1.6">Get your {{ $selectedCompany->name }} PSV premium estimate in seconds. No paperwork, no waiting.</p>
    </div>
    <div style="background:#F9F9FF;border-radius:16px;padding:32px 24px;text-align:center;transition:transform .2s,box-shadow .2s"
         onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 25px rgba(0,0,0,0.08)'"
         onmouseout="this.style.transform='';this.style.boxShadow=''">
      <div style="width:52px;height:52px;background:{{ $__primaryColor }};border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:24px">📄</div>
      <h3 style="font-size:16px;font-weight:700;margin-bottom:8px;color:#1a1a2e">Downloadable Quotations</h3>
      <p style="font-size:13px;color:#6b7280;line-height:1.6">Generate and download a detailed PDF quotation instantly from {{ $selectedCompany->name }}.</p>
    </div>
    <div style="background:#F9F9FF;border-radius:16px;padding:32px 24px;text-align:center;transition:transform .2s,box-shadow .2s"
         onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 25px rgba(0,0,0,0.08)'"
         onmouseout="this.style.transform='';this.style.boxShadow=''">
      <div style="width:52px;height:52px;background:{{ $__primaryColor }};border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:24px">🔒</div>
      <h3 style="font-size:16px;font-weight:700;margin-bottom:8px;color:#1a1a2e">Trusted &amp; Secure</h3>
      <p style="font-size:13px;color:#6b7280;line-height:1.6">Your data is safe with us. We work with IRM-authorized providers.</p>
    </div>
    <div style="background:#F9F9FF;border-radius:16px;padding:32px 24px;text-align:center;transition:transform .2s,box-shadow .2s"
         onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 25px rgba(0,0,0,0.08)'"
         onmouseout="this.style.transform='';this.style.boxShadow=''">
      <div style="width:52px;height:52px;background:{{ $__primaryColor }};border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:24px">💡</div>
      <h3 style="font-size:16px;font-weight:700;margin-bottom:8px;color:#1a1a2e">Tailored for PSV</h3>
      <p style="font-size:13px;color:#6b7280;line-height:1.6">Cover options designed specifically for your public service vehicle type and passenger capacity.</p>
    </div>
    <div style="background:#F9F9FF;border-radius:16px;padding:32px 24px;text-align:center;transition:transform .2s,box-shadow .2s"
         onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 25px rgba(0,0,0,0.08)'"
         onmouseout="this.style.transform='';this.style.boxShadow=''">
      <div style="width:52px;height:52px;background:{{ $__primaryColor }};border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:24px">📞</div>
      <h3 style="font-size:16px;font-weight:700;margin-bottom:8px;color:#1a1a2e">Dedicated Support</h3>
      <p style="font-size:13px;color:#6b7280;line-height:1.6">Our team is here to help with your PSV insurance needs, from quotes to claims support.</p>
    </div>
  </div>
</section>

<section class="cta-section">
  <h2>Ready to Protect Your PSV Vehicle?</h2>
  <p>Get your {{ $selectedCompany->name }} PSV insurance quote in minutes. Compare premiums, download your policy, and drive with confidence.</p>
  <a href="javascript:void(0)" onclick="document.getElementById('quoteForm').scrollIntoView({behavior:'smooth'})" class="btn-cta">Get Your PSV Quote →</a>
</section>

<section class="page-section">
  <div class="regen-box">
    <div class="regen-icon">🔁</div>
    <h3>Regenerate Your Quote</h3>
    <p>Enter your vehicle registration number to regenerate a previous quote.</p>
    <form method="POST" action="{{ route('quotations.regenerate') }}" style="display:contents">
      @csrf
      <div class="regen-input-row">
        <input type="text" name="registration_number" placeholder="e.g. KDH 724V" required>
        <button type="submit" class="btn btn-primary">Regenerate →</button>
      </div>
    </form>
  </div>
</section>

<x-auth-modal/>
<x-public-footer/>

<script>
window.MIC_COMPANIES = @json($companyData);
window.MIC_VEHICLE_TYPE = 'psv';
window.MIC_GUEST = @json(auth()->guest());
window.MIC_MODE = 'single';
window.MIC_SELECTED_COMPANY = @json($selectedCompany->slug);

(function(){
  var sel = document.getElementById('f-year');
  if(sel) for(var y=2026;y>=1990;y--){ var o=document.createElement('option'); o.value=y; o.textContent=y; sel.appendChild(o); }
})();
</script>
<script src="{{ asset('js/mic-toast.js') }}"></script>
<script src="{{ asset('js/home.js') }}"></script>
</body>
</html>
