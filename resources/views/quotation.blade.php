@extends('layouts.quotation')

@section('title', 'Motor Insurance Quotation | MIC Global Risks')

@section('styles')

@endsection

@section('content')
<main class="main-content">

  <div class="toast-container" id="toast-container"></div>

  <div class="hero-card">
    <div class="hero-card-left">
      <h2>Motor Insurance Quotation</h2>
      <div class="quote-number">
        <span id="hero-quote-num">—</span>
        <button class="copy-btn" id="copy-quote-btn" onclick="copyQuoteNumber()">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
          Copy
        </button>
      </div>
      <p style="font-size:13px;color:rgba(255,255,255,.65);margin-top:4px">Your personalised premium breakdown</p>
    </div>
    <div class="hero-card-right">
      <img id="hero-company-logo" src="" alt="Company Logo" style="display:none">
    </div>
  </div>

  <div class="section-label">Customer &amp; Vehicle Summary</div>
  <div class="summary-grid">
    <div class="summary-card">
      <h4>Personal Details</h4>
      <div class="summary-row"><span>Full Name</span><span id="s-name">—</span></div>
      <div class="summary-row"><span>Phone</span><span id="s-phone">—</span></div>
      <div class="summary-row"><span>Email</span><span id="s-email">—</span></div>
    </div>
    <div class="summary-card">
      <h4>Vehicle Details</h4>
      <div class="summary-row"><span>Registration</span><span id="s-reg">—</span></div>
      <div class="summary-row"><span>Make / Model</span><span id="s-make">—</span></div>
      <div class="summary-row"><span>Year / Body</span><span id="s-year">—</span></div>
      <div class="summary-row"><span>Color</span><span id="s-color">—</span></div>
      <div class="summary-row"><span>Chassis No.</span><span id="s-chassis">—</span></div>
    </div>
    <div class="summary-card">
      <h4>Valuation</h4>
      <div class="summary-row"><span>Estimated Value</span><span id="s-value">—</span></div>
      <div class="summary-row"><span>Cover Type</span><span>Comprehensive</span></div>
      <div class="summary-row"><span>Frequency</span><span>Annually</span></div>
      <div class="summary-row"><span>Geo. Limit</span><span>Kenya / Uganda / TZ</span></div>
    </div>
  </div>

  <div id="single-breakdown-wrap">
    <div class="section-label">Quotation Summary — <span id="breakdown-company-label"></span></div>
    <div class="breakdown-table">
      <table>
        <thead>
          <tr>
            <th>Description</th>
            <th>Rate / Notes</th>
            <th style="text-align:right">Amount (KES)</th>
          </tr>
        </thead>
        <tbody id="breakdown-rows"></tbody>
        <tfoot>
          <tr>
            <td colspan="2">Total Premium (incl. taxes)</td>
            <td id="breakdown-total">—</td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <div class="section-label">Compare Premiums Across Insurers</div>
  <div class="comparison-grid" id="comparison-grid"></div>

  <div id="compare-all-wrap" class="compare-table-wrap">
    <div style="padding:18px 20px 10px;font-size:13px;font-weight:700;color:var(--text)">Detailed Premium Comparison — All Insurers</div>
    <table>
      <thead>
        <tr>
          <th>Description</th>
          <th id="cmp-head-0">Insurer 1</th>
          <th id="cmp-head-1">Insurer 2</th>
          <th id="cmp-head-2">Insurer 3</th>
        </tr>
      </thead>
      <tbody id="compare-rows"></tbody>
      <tfoot>
        <tr>
          <td>Total Premium (incl. taxes)</td>
          <td id="cmp-total-0">—</td>
          <td id="cmp-total-1">—</td>
          <td id="cmp-total-2">—</td>
        </tr>
      </tfoot>
    </table>
  </div>

  <div class="section-label" id="dl-cards-label">Download Quote</div>
  <div class="download-cards" id="dl-cards"></div>

  <div class="action-bar" id="action-bar">
    <span class="action-bar-label">Quotation Actions</span>
    <button class="action-btn action-btn-download" onclick="actionDownload()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Download PDF
    </button>
    <button class="action-btn action-btn-print" onclick="actionPrint()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
      Print
    </button>
    <button class="action-btn action-btn-preview" id="btn-preview" onclick="actionTogglePreview()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
      Preview
    </button>
  </div>

  <div class="pdf-preview" id="pdf-preview"></div>

</main>
@endsection

@section('scripts')
<script>
window.quotationData = @json($companyData);
window.quotationTaxRate = {{ $taxRate }};
window.quotationCompanies = @json($companies->map(fn($c) => ['slug' => $c->slug, 'name' => $c->name, 'logo' => $c->logoUrl()]));
window.hardcodedLogos = @json($hardcodedLogos ?? []);
window.selectedCompanySlug = {{ $selectedCompany ? json_encode($selectedCompany->slug) : 'null' }};
@php
    $_quotationDbData = $userQuotation ? [
        'firstName' => $userQuotation->user->first_name ?? '',
        'lastName' => $userQuotation->user->last_name ?? '',
        'salutation' => '',
        'phone' => $userQuotation->user->mobile ?? '',
        'email' => $userQuotation->user->email ?? '',
        'reg' => $userQuotation->car_registration ?? '',
        'chassis' => '',
        'make' => $userQuotation->car_make ?? '',
        'model' => $userQuotation->car_model ?? '',
        'body' => '',
        'year' => $userQuotation->car_year ?? '',
        'color' => '',
        'value' => (float)($userQuotation->car_valuation ?? 0),
        'quoteNum' => $userQuotation->quotation_number ?? '',
    ] : null;
@endphp
window.quotationDbData = @json($_quotationDbData);
window.MIC_AUTH = @json(auth()->check());
</script>
@endsection