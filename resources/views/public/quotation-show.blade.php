<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quotation {{ $quotation->quotation_number }} | MIC Global Risks</title>
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body>

<nav>
  <div class="nav-top">
    <a href="#">About Us</a><a href="#">Contact Us</a><a href="#">Affiliates</a><a href="#">Careers</a>
  </div>
  <div class="nav-main">
    <a href="{{ url('/') }}"><img src="{{ asset('img/MIC-Global-Risks_logo.png') }}" alt="MIC Global Risks" class="nav-logo"></a>
    <div class="nav-links">
      <a href="{{ url('/motor-quote') }}">Motor Quote</a>
      <a href="{{ route('stickers.create') }}">Certificates</a>
      <a href="{{ route('stickers.lookup') }}">Look Up</a>
      <a href="{{ route('login') }}" class="nav-cta">Login</a>
      <a href="{{ route('register') }}" class="nav-cta" style="background:var(--blue-dark)">Register</a>
    </div>
  </div>
</nav>

@php $breakdown = $quotation->breakdown ?? []; @endphp

<section class="page-hero">
  <div class="page-hero-inner">
    <div class="breadcrumb">
      <a href="{{ url('/') }}">Home</a>
      <span class="breadcrumb-sep">›</span>
      <a href="{{ url('/motor-quote') }}">Motor Quote</a>
      <span class="breadcrumb-sep">›</span>
      <span>Quotation {{ $quotation->quotation_number }}</span>
    </div>
    <h1>Quotation Details</h1>
    <p>{{ $quotation->company->name }} &mdash; {{ $quotation->car_make }} {{ $quotation->car_model }} ({{ $quotation->car_year }})</p>
    <div class="quote-ref-badge" style="display:inline-flex;align-items:center;gap:10px;padding:8px 20px;background:rgba(255,255,255,0.12);border-radius:10px;font-size:18px;font-weight:700;letter-spacing:-0.3px"><span style="opacity:0.7;font-weight:500;font-size:13px">Ref:</span> <span>{{ $quotation->quotation_number }}</span> <button onclick="copyQuoteNumber()" style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.2);color:#fff;padding:3px 10px;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:4px;transition:background .15s" onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> Copy</button></div>
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
      <h4>Quotation Info</h4>
      <div class="summary-row"><span>Number</span><span>{{ $quotation->quotation_number }}</span></div>
      <div class="summary-row"><span>Company</span><span>{{ $quotation->company->name }}</span></div>
      <div class="summary-row"><span>Status</span><span class="badge badge-active">{{ ucfirst($quotation->status) }}</span></div>
      <div class="summary-row"><span>Date</span><span>{{ $quotation->created_at->format('d M Y') }}</span></div>
      <div class="summary-row"><span>Valid Until</span><span>{{ $quotation->created_at->addDays(15)->format('d M Y') }}</span></div>
    </div>
    <div class="summary-card">
      <h4>Vehicle Details</h4>
      <div class="summary-row"><span>Registration</span><span>{{ $quotation->car_registration ?? 'N/A' }}</span></div>
      <div class="summary-row"><span>Make / Model</span><span>{{ $quotation->car_make }} {{ $quotation->car_model }}</span></div>
      <div class="summary-row"><span>Year</span><span>{{ $quotation->car_year }}</span></div>
      <div class="summary-row"><span>Cover Type</span><span>Comprehensive</span></div>
    </div>
    <div class="summary-card">
      <h4>Premium Summary</h4>
      <div class="summary-row"><span>Vehicle Value</span><span>KES {{ number_format($quotation->car_valuation, 2) }}</span></div>
      <div class="summary-row"><span>Basic Premium ({{ ($breakdown['premium_rate'] ?? 6.5) }}%)</span><span>KES {{ number_format($quotation->premium, 2) }}</span></div>
      @php
        $_basePremium = $breakdown['base_premium'] ?? null;
        $_iraLevy = $breakdown['ira_levy'] ?? null;
      @endphp
      @if($_basePremium !== null && $_iraLevy !== null)
      <div class="summary-row" style="padding-left:1rem;font-size:11px;color:var(--text-muted)">
        <span>Base Premium ({{ number_format(($breakdown['premium_rate'] ?? 6.5) - ($breakdown['ira_levy_rate'] ?? 0.5), 1) }}%)</span>
        <span>KES {{ number_format($_basePremium, 2) }}</span>
      </div>
      <div class="summary-row" style="padding-left:1rem;font-size:11px;color:var(--text-muted)">
        <span>IRA Levy ({{ $breakdown['ira_levy_rate'] ?? 0.5 }}%)</span>
        <span>KES {{ number_format($_iraLevy, 2) }}</span>
      </div>
      @endif
      <div class="summary-row"><span>PVT Premium</span><span>KES {{ number_format($quotation->pvt_premium, 2) }}</span></div>
      <div class="summary-row" style="border-top:2px solid var(--blue);margin-top:6px;padding-top:8px">
        <span style="font-weight:800;color:var(--blue)">Total Premium</span>
        <span style="font-weight:800;color:var(--blue);font-size:16px">KES {{ number_format($quotation->total_premium, 2) }}</span>
      </div>
    </div>
  </div>

  <div class="breakdown-table">
    <table>
      <thead><tr><th>Description</th><th style="text-align:right">Amount (KES)</th></tr></thead>
      <tbody>
        <tr><td>Estimated Vehicle Value</td><td>{{ number_format($quotation->car_valuation, 2) }}</td></tr>
        <tr><td>Basic Annual Premium ({{ $breakdown['premium_rate'] ?? 6.5 }}%)</td><td>{{ number_format($quotation->premium, 2) }}</td></tr>
        @if($_basePremium !== null && $_iraLevy !== null)
        <tr style="color:#6b7280;font-size:11px"><td>&nbsp;&nbsp;&nbsp;Base Premium ({{ number_format(($breakdown['premium_rate'] ?? 6.5) - ($breakdown['ira_levy_rate'] ?? 0.5), 1) }}%)</td><td>{{ number_format($_basePremium, 2) }}</td></tr>
        <tr style="color:#6b7280;font-size:11px"><td>&nbsp;&nbsp;&nbsp;IRA Levy ({{ $breakdown['ira_levy_rate'] ?? 0.5 }}%)</td><td>{{ number_format($_iraLevy, 2) }}</td></tr>
        @endif
        <tr><td>PVT Premium</td><td>{{ number_format($quotation->pvt_premium, 2) }}</td></tr>
        @if($quotation->excess_own_damage)
          <tr><td>Excess - Own Damage</td><td>{{ number_format($quotation->excess_own_damage, 2) }}</td></tr>
        @endif
        @if($quotation->excess_theft)
          <tr><td>Excess - Theft</td><td>{{ number_format($quotation->excess_theft, 2) }}</td></tr>
        @endif
      </tbody>
      <tfoot>
        <tr><td>Total Premium</td><td>{{ number_format($quotation->total_premium, 2) }}</td></tr>
      </tfoot>
    </table>
  </div>

  <div class="action-bar" style="display:flex">
    <span class="action-bar-label">Actions</span>
    <a href="{{ route('public.quotation.download', $quotation->quotation_number) }}" class="action-btn action-btn-download">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Download PDF
    </a>
    <button class="action-btn action-btn-print" onclick="window.print()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
      Print
    </button>
  </div>

  <div class="summary-card" style="margin-top:20px">
    <h4>Email This Quotation</h4>
    <form method="POST" action="{{ route('public.quotation.email') }}" class="email-form">
      @csrf
      <input type="hidden" name="number" value="{{ $quotation->quotation_number }}">
      <div class="email-input-row">
        <input type="email" name="email" required placeholder="Enter email address" class="email-input">
        <button type="submit" class="action-btn action-btn-download">Send Email</button>
      </div>
    </form>
  </div>

</main>

<footer>
  <div class="footer-top">
    <div class="footer-brand">
      <img src="{{ asset('img/MIC-Global-Risks_logo_no_background.png') }}" alt="MIC Global Risks">
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
    <span>&copy; 2026 MIC Global Risks Insurance (Kenya) Limited</span>
    <div><a href="#">Privacy Policy</a><a href="#">Terms &amp; Conditions</a><a href="#">Contact Us</a></div>
  </div>
</footer>

<script>
function copyQuoteNumber() {
    var text = '{{ $quotation->quotation_number }}';
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function() {
            var btn = event.currentTarget;
            var orig = btn.innerHTML;
            btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"/></svg> Copied!';
            btn.style.background = 'rgba(16,185,129,0.5)';
            setTimeout(function() { btn.innerHTML = orig; btn.style.background = 'rgba(255,255,255,0.15)'; }, 2000);
        });
    } else {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        alert('Copied!');
    }
}
</script>
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
