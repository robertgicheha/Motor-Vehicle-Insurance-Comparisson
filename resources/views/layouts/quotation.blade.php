<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
 @yield('title')
<title>Motor Insurance Quotation | MIC Global Risks</title>
<link rel="stylesheet" href="{{ asset('css/quotation.css') }}">
</head>
<body>

<!-- NAV -->
<nav>
  <div class="nav-top">
    <a href="#">About Us</a><a href="#">Contact Us</a><a href="#">Affiliates</a><a href="#">Careers</a>
  </div>
  <div class="nav-main">
    <img src="img/MIC-Global-Risks_logo.png" alt="MIC Global Risks" class="nav-logo">
    <div class="nav-links">
      <a href="#">Insurance</a>
      <a href="#">About Us</a>
      <a href="#">Contact Us</a>
      <a href="index.html">Motor Quote</a>
      <a href="#" class="active">Quotation</a>
      <a href="#" class="nav-cta">Get a Quote</a>
    </div>
  </div>
</nav>

<!-- PAGE HERO -->
<section class="page-hero">
  <div class="page-hero-inner">
    <div class="breadcrumb">
      <a href="#">Home</a>
      <span class="breadcrumb-sep">›</span>
      <a href="index.html">Motor Quote</a>
      <span class="breadcrumb-sep">›</span>
      <span>Quotation</span>
    </div>
    <h1>Motor Insurance Quotation</h1>
    <p>Your personalised premium breakdown from Kenya's leading motor insurers. Review, compare and download your quotation below.</p>
    <div class="quote-ref-badge"><span>Ref:</span> <span id="hero-quote-num">—</span></div>
  </div>
</section>

@yield('content')
<!-- MAIN -->
<main class="main-content">

  <!-- ── 1. CUSTOMER SUMMARY ── -->
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

  <!-- ── 2. QUOTATION BREAKDOWN (single company mode) ── -->
  <div id="single-breakdown-wrap">
    <div class="section-label">Quotation Summary — <span id="breakdown-company-label">GA Insurance</span></div>
    <div class="breakdown-table">
      <table>
        <thead>
          <tr>
            <th>Description</th>
            <th>Rate / Notes</th>
            <th style="text-align:right">Amount (KES)</th>
          </tr>
        </thead>
        <tbody id="breakdown-rows">
          <!-- populated by JS -->
        </tbody>
        <tfoot>
          <tr>
            <td colspan="2">Total Premium (incl. taxes)</td>
            <td id="breakdown-total">—</td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <!-- ── 3. COMPARE ALL TABLE ── -->
  <div id="compare-all-wrap" class="compare-table-wrap">
    <div style="padding:18px 20px 10px;font-size:13px;font-weight:700;color:var(--text)">Premium Comparison — All Insurers</div>
    <table>
      <thead>
        <tr>
          <th>Description</th>
          <th>GA Insurance</th>
          <th>Britam</th>
          <th>Sanlam Allianz</th>
        </tr>
      </thead>
      <tbody id="compare-rows">
        <!-- populated by JS -->
      </tbody>
      <tfoot>
        <tr>
          <td>Total Premium (incl. taxes)</td>
          <td id="cmp-total-ga">—</td>
          <td id="cmp-total-britam">—</td>
          <td id="cmp-total-sanlam">—</td>
        </tr>
      </tfoot>
    </table>
  </div>

  <!-- ── 4. DOWNLOAD CARDS ── -->
  <div class="section-label" id="dl-cards-label">Download Quote</div>
  <div class="download-cards" id="dl-cards">
    <!-- populated by JS based on mode -->
  </div>

  <!-- ── ACTION BAR ── -->
  <div class="action-bar" id="action-bar">
    <span class="action-bar-label">Quotation Actions</span>
    <button class="action-btn action-btn-download" id="btn-download" onclick="actionDownload()">
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

  <!-- ── 5. VALIDITY NOTE ── -->
  <!-- <div class="validity-note">
    <strong>Note:</strong> This quotation is valid for <strong>15 days</strong> from the date of issue.
    Comprehensive cover is subject to a satisfactory valuation report conducted within the first month of cover.
    Subject to sanction limitation and exclusion clause and standard policy terms and conditions.
    Geographical limit: Kenya / Uganda / Tanzania (Yellow Card required for TPO outside Kenya).
  </div> -->

  <!-- ── PDF PREVIEW (print target) ── -->
  <div class="pdf-preview" id="pdf-preview">
    <!-- populated by JS -->
  </div>

</main>

<!-- FOOTER -->
<footer>
  <div class="footer-top">
    <div class="footer-brand">
      <img src="img/MIC-Global-Risks_logo_no_background.png" alt="MIC Global Risks">
      <p>Your trusted insurance intermediary — connecting you to Kenya's leading insurers with transparency, speed and expertise.</p>
      <div class="footer-regions" style="margin-top:14px">
        <span class="region-pill">Kenya</span>
        <span class="region-pill">Uganda</span>
        <span class="region-pill">Tanzania</span>
      </div>
    </div>
    <div class="footer-col">
      <h5>Company</h5>
      <a href="#">About Us</a>
      <a href="#">Our Team</a>
      <a href="#">Careers</a>
      <a href="#">Affiliates</a>
    </div>
    <div class="footer-col">
      <h5>Services</h5>
      <a href="#">Motor Insurance</a>
      <a href="#">Property Cover</a>
      <a href="#">Travel Insurance</a>
      <a href="#">Medical Cover</a>
    </div>
    <div class="footer-col">
      <h5>Contact</h5>
      <a href="#">6th Floor, 9 West</a>
      <a href="#">Ring Road Parklands</a>
      <a href="#">Nairobi, Kenya</a>
      <a href="#">www.micglobalrisks.com</a>
    </div>
  </div>
  <div class="footer-bottom">
    <span>© 2026 MIC Global Risks Insurance (Kenya) Limited</span>
    <div>
      <a href="#">Privacy Policy</a>
      <a href="#">Terms &amp; Conditions</a>
      <a href="#">Contact Us</a>
    </div>
  </div>
</footer>


@yield('scripts')
<script src="{{ asset('js/quotation.js') }}"></script>
</body>
</html>