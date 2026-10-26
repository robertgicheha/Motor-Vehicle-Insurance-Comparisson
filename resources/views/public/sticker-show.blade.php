<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Certificate {{ $sticker->sticker_number }} | LOLC Microfinance Bank</title>
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body>

<nav>
  <div class="nav-top">
    <a href="#">About Us</a><a href="#">Contact Us</a><a href="#">Affiliates</a><a href="#">Careers</a>
  </div>
  <div class="nav-main">
    <a href="{{ url('/') }}"><img src="{{ asset('images/LOLC-Kenya.webp') }}" alt="LOLC Microfinance Bank" class="nav-logo" width="417" height="67"></a>
    <div class="nav-links">
      <a href="{{ url('/motor-quote') }}">Motor Quote</a>
      <a href="{{ route('stickers.create') }}">Certificates</a>
      <a href="{{ route('stickers.lookup') }}">Look Up</a>
      <a href="{{ route('login') }}" class="nav-cta">Login</a>
    </div>
  </div>
</nav>

<section class="page-hero">
  <div class="page-hero-inner">
    <div class="breadcrumb">
      <a href="{{ url('/') }}">Home</a>
      <span class="breadcrumb-sep">›</span>
      <a href="{{ url('/motor-quote') }}">Motor Quote</a>
      <span class="breadcrumb-sep">›</span>
      <span>Certificate {{ $sticker->sticker_number }}</span>
    </div>
    <h1>Insurance Certificate</h1>
    <p>{{ $sticker->company->name }} &mdash; {{ $sticker->car_make }} {{ $sticker->car_model }} ({{ $sticker->car_year }})</p>
    <div class="quote-ref-badge"><span>Sticker:</span> <span>{{ $sticker->sticker_number }}</span></div>
  </div>
</section>

<main class="main-content">

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
  @endif

  <div class="summary-grid">
    <div class="summary-card">
      <h4>Certificate Info</h4>
      <div class="summary-row"><span>Number</span><span>{{ $sticker->sticker_number }}</span></div>
      <div class="summary-row"><span>Company</span><span>{{ $sticker->company->name }}</span></div>
      <div class="summary-row"><span>Status</span><span class="badge badge-active">{{ ucfirst($sticker->status) }}</span></div>
      <div class="summary-row"><span>Downloads</span><span>{{ $sticker->download_count ?? 0 }}</span></div>
      <div class="summary-row"><span>Generated</span><span>{{ $sticker->generated_at?->format('d M Y, h:i A') ?? $sticker->created_at->format('d M Y, h:i A') }}</span></div>
    </div>
    <div class="summary-card">
      <h4>Policy Reference</h4>
      @if($sticker->underwriter_sticker_no)<div class="summary-row"><span>Policy No</span><span>{{ $sticker->underwriter_sticker_no }}</span></div>@endif
      <div class="summary-row"><span>Client Type</span><span>{{ ucfirst($sticker->client_type ?? 'individual') }}</span></div>
    </div>
    <div class="summary-card">
      <h4>Premium</h4>
      <div class="summary-row"><span>Valuation</span><span>KES {{ number_format($sticker->car_valuation, 2) }}</span></div>
      @if($sticker->premium_rate)<div class="summary-row"><span>Rate</span><span>{{ $sticker->premium_rate }}%</span></div>@endif
      <div class="summary-row" style="border-top:2px solid var(--blue);margin-top:6px;padding-top:8px">
        <span style="font-weight:800;color:var(--blue)">Premium</span>
        <span style="font-weight:800;color:var(--blue);font-size:16px">KES {{ number_format($sticker->premium, 2) }}</span>
      </div>
    </div>
  </div>

  <div class="summary-grid">
    <div class="summary-card">
      <h4>Vehicle Details</h4>
      <div class="summary-row"><span>Make / Model</span><span>{{ $sticker->car_make }} {{ $sticker->car_model }}</span></div>
      <div class="summary-row"><span>Year</span><span>{{ $sticker->car_year }}</span></div>
      <div class="summary-row"><span>Registration</span><span>{{ $sticker->vehicle_registration ?? 'N/A' }}</span></div>
      <div class="summary-row"><span>Cover Type</span><span>{{ $sticker->cover_type }}</span></div>
    </div>
  </div>

  <div class="action-bar" style="display:flex">
    <span class="action-bar-label">Actions</span>
    <button class="action-btn action-btn-print" onclick="window.print()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
      Print
    </button>
  </div>

  @if($sticker->breakdown && count($sticker->breakdown) > 0)
    <div class="breakdown-table" style="margin-top:20px">
      <table>
        <thead><tr><th>Item</th><th style="text-align:right">Amount (KES)</th></tr></thead>
        <tbody>
          @foreach($sticker->breakdown as $key => $value)
            <tr><td>{{ ucwords(str_replace('_', ' ', $key)) }}</td><td>{{ is_numeric($value) ? number_format($value, 2) : $value }}</td></tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif

</main>

<footer>
  <div class="footer-top">
    <div class="footer-brand">
      <img src="{{ asset('images/LOLC-Kenya.webp') }}" alt="LOLC Microfinance Bank" width="417" height="67">
      <p>Your trusted insurance intermediary — connecting you to Kenya's leading insurers with transparency, speed and expertise.</p>
    </div>
    <div class="footer-col">
      <h5>Company</h5>
      <a href="#">About Us</a><a href="#">Our Team</a><a href="#">Careers</a><a href="#">Affiliates</a>
    </div>
    <div class="footer-col">
      <h5>Services</h5>
      <a href="#">Motor Insurance</a><a href="#">Property Cover</a><a href="#">Travel Insurance</a><a href="#">Medical Cover</a>
    </div>
    <div class="footer-col">
      <h5>Contact</h5>
      <a href="#">6th Floor, 9 West</a><a href="#">Ring Road Parklands</a><a href="#">Nairobi, Kenya</a><a href="#">www.micglobalrisks.com</a>
    </div>
  </div>
  <div class="footer-bottom">
    <span>&copy; 2026 LOLC Microfinance Bank</span>
    <div><a href="#">Privacy Policy</a><a href="#">Terms &amp; Conditions</a><a href="#">Contact Us</a></div>
  </div>
</footer>

<style>
  .alert { padding:14px 20px; border-radius:10px; margin-bottom:20px; font-size:13px; font-weight:600; }
  .alert-success { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; }
  .alert-error { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }
  .badge { display:inline-block; padding:2px 8px; border-radius:4px; font-size:10px; font-weight:700; }
  .badge-active { background:#dcfce7; color:#166534; }
  .email-form { display:flex; gap:10px; margin-top:10px; }
  .email-input-row { display:flex; gap:10px; width:100%; }
  .email-input { flex:1; padding:9px 14px; border:1.5px solid var(--border); border-radius:8px; font-size:13px; font-family:'Montserrat',sans-serif; outline:none; }
  .email-input:focus { border-color:var(--blue); }
  @media print { nav, .page-hero, .action-bar, .email-form, footer { display:none !important; } .main-content { padding:0; } }
</style>
</body>
</html>
