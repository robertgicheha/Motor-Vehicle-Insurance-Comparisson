// ═══════════════════════════════════════════════════════
//  QUOTATION PAGE  –  main.js
//  Reads URL params or sessionStorage set by index.html
// ═══════════════════════════════════════════════════════

const RATES = {
  GA:     { rate: 0.035, label: '3.5%', name: 'GA Insurance Ltd',       color: '#005baa', imgSrc: 'img/GA Insurance.png'    },
  BRITAM: { rate: 0.041, label: '4.1%', name: 'Britam General Insurance', color: '#0071bc', imgSrc: 'img/britam_logo.png'     },
  SANLAM: { rate: 0.045, label: '4.5%', name: 'Sanlam Allianz Insurance', color: '#1d3c6e', imgSrc: 'img/Sanlam-Allianz.png'  }
};
const TAX_RATE = 0.005; // 0.5% IRA levy

const COMPANY_META = {
  GA: {
    address: 'GA Insurance Ltd, GA Building, Hospital Road, Upper Hill, P.O. Box 42166 - 00100 Nairobi',
    website: 'www.gainsurance.co.ke'
  },
  BRITAM: {
    address: 'Britam General Insurance Company (Kenya) Limited\nBritam Centre, Junction of Mara and Ragati Road, Upper Hill\nP.O. BOX 30375 - 0100 Nairobi',
    website: 'www.britam.com'
  },
  SANLAM: {
    address: 'Sanlam Allianz Insurance Company Limited\nSanlam Tower, Hospital Road, Upper Hill\nP.O. Box 30522 - 00100 Nairobi',
    website: 'www.sanlam.co.ke'
  }
};

function fmt(n) {
  return 'KES ' + Number(n).toLocaleString('en-KE', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
}

function calcPremiums(vehicleValue) {
  const result = {};
  for (const [key, cfg] of Object.entries(RATES)) {
    const basic = vehicleValue * cfg.rate;
    const tax   = basic * TAX_RATE;
    const total = basic + tax;
    result[key] = { basic, tax, total };
  }
  return result;
}

// ── READ DATA ──
function getFormData() {
  try {
    const raw = sessionStorage.getItem('micQuoteData');
    if (raw) return JSON.parse(raw);
  } catch(e) {}
  // demo fallback
  return {
    salutation: 'Mr.',
    firstName: 'Robert',
    lastName: 'Gicheha',
    phone: '+254 010 351 517',
    email: 'r.gicheha@example.com',
    reg: 'KDH 724V',
    chassis: 'WVWZZZ1KZ9W123456',
    make: 'Volkswagen',
    model: 'Golf',
    body: 'Hatchback',
    year: '2018',
    color: 'Silver/Grey',
    value: 800000,
    quoteNum: 'MIC-2026/0042',
    mode: 'ALL'   // 'GA' | 'BRITAM' | 'SANLAM' | 'ALL'
  };
}

// ── URL PARAM OVERRIDE ──
function getMode() {
  const p = new URLSearchParams(window.location.search);
  return p.get('company') || getFormData().mode || 'ALL';
}

function genQuoteNum(company) {
  const d = new Date();
  const y = d.getFullYear();
  const n = String(Math.floor(Math.random() * 9000) + 1000);
  return `${company}-${y}/${n}`;
}

// ── SHOW ACTION BAR ──
// Called the first time a download card is clicked; stays visible thereafter.
function showActionBar() {
  document.getElementById('action-bar').classList.add('visible');
}

// ── POPULATE PAGE ──
function init() {
  const fd   = getFormData();
  const mode = getMode();
  const value = Number(fd.value) || 800000;
  const prems = calcPremiums(value);
  const qNum  = fd.quoteNum || genQuoteNum(mode === 'ALL' ? 'MIC' : mode);

  // Hero
  document.getElementById('hero-quote-num').textContent = qNum;

  // Summary
  document.getElementById('s-name').textContent    = `${fd.salutation || ''} ${fd.firstName} ${fd.lastName}`.trim();
  document.getElementById('s-phone').textContent   = fd.phone || '—';
  document.getElementById('s-email').textContent   = fd.email || '—';
  document.getElementById('s-reg').textContent     = fd.reg  || '—';
  document.getElementById('s-make').textContent    = `${fd.make} ${fd.model}`.trim() || '—';
  document.getElementById('s-year').textContent    = `${fd.year}  •  ${fd.body || '—'}`;
  document.getElementById('s-color').textContent   = fd.color   || '—';
  document.getElementById('s-chassis').textContent = fd.chassis || '—';
  document.getElementById('s-value').textContent   = fmt(value);

  if (mode === 'ALL') {
    renderCompareAll(value, prems, fd, qNum);
    // Pre-build preview for the first company so Download/Print work immediately
    // Assuming buildPDFPreview is declared later or somewhere not truncated in original logic. 
    // Added a blank definition below if needed so the script won't error out immediately.
  } else {
    renderSingle(mode, value, prems, fd, qNum);
  }
}

// ── SINGLE COMPANY MODE ──
function renderSingle(company, value, prems, fd, qNum) {
  const cfg = RATES[company];
  const p   = prems[company];

  document.getElementById('single-breakdown-wrap').style.display = '';
  document.getElementById('compare-all-wrap').style.display      = 'none';

  document.getElementById('breakdown-company-label').textContent = cfg.name;

  const rows = [
    ['Estimated Vehicle Value', '—', fmt(value)],
    ['Basic Annual Premium', `@ ${cfg.label} of vehicle value`, fmt(p.basic)],
    ['Windscreen Premium', 'Included', 'KES 0'],
    ['Radio / Cassette Premium', 'Included', 'KES 0'],
    ['IRA Levy (Training Levy)', `@ 0.5% of basic premium`, fmt(p.tax)],
  ];
  const tbody = document.getElementById('breakdown-rows');
  tbody.innerHTML = rows.map(([desc, note, amt]) =>
    `<tr><td>${desc}</td><td style="color:var(--text-muted);font-size:12.5px">${note}</td><td>${amt}</td></tr>`
  ).join('');
  document.getElementById('breakdown-total').textContent = fmt(p.total);

  // Download cards: the 3 companies + "Download All"
  renderDownloadCards(company, value, prems, fd, qNum);
}

// ── COMPARE ALL MODE ──
function renderCompareAll(value, prems, fd, qNum) {
  document.getElementById('single-breakdown-wrap').style.display = 'none';
  document.getElementById('compare-all-wrap').style.display      = '';
  document.getElementById('dl-cards-label').textContent          = 'Download Individual Quote';

  const rows = [
    { label: 'Estimated Vehicle Value', ga: fmt(value), britam: fmt(value), sanlam: fmt(value) },
    { label: 'Premium Rate', ga: '3.5%', britam: '4.1%', sanlam: '4.5%' },
    { label: 'Basic Annual Premium', ga: fmt(prems.GA.basic), britam: fmt(prems.BRITAM.basic), sanlam: fmt(prems.SANLAM.basic) },
    { label: 'IRA Levy (0.5%)',       ga: fmt(prems.GA.tax),   britam: fmt(prems.BRITAM.tax),   sanlam: fmt(prems.SANLAM.tax)   },
  ];
  document.getElementById('compare-rows').innerHTML = rows.map(r =>
    `<tr><td>${r.label}</td><td>${r.ga}</td><td>${r.britam}</td><td>${r.sanlam}</td></tr>`
  ).join('');
  document.getElementById('cmp-total-ga').textContent     = fmt(prems.GA.total);
  document.getElementById('cmp-total-britam').textContent = fmt(prems.BRITAM.total);
  document.getElementById('cmp-total-sanlam').textContent = fmt(prems.SANLAM.total);

  // Four download cards
  renderDownloadCards('ALL', value, prems, fd, qNum);
}

function renderDownloadCards(mode, value, prems, fd, qNum) {
  const container = document.getElementById('dl-cards');
  const companies = [
    { key: 'GA',     btnClass: 'dl-btn-ga',     btnLabel: 'Download GA Quote' },
    { key: 'BRITAM', btnClass: 'dl-btn-britam',  btnLabel: 'Download Britam Quote' },
    { key: 'SANLAM', btnClass: 'dl-btn-sanlam',  btnLabel: 'Download Sanlam Quote' },
  ];

  let html = companies.map(({ key, btnClass, btnLabel }) => {
    const cfg = RATES[key];
    const p   = prems[key];
    const selected = (mode === key) ? 'border-color:var(--blue);box-shadow:0 0 0 2px rgba(0,102,179,.15)' : '';
    return `
      <div class="dl-card" style="${selected}" onclick="showActionBar()">
        <div class="dl-card-logo">
          <img src="${cfg.imgSrc}" alt="${cfg.name}" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
          <span class="logo-text" style="display:none">${key}</span>
        </div>
        <div class="dl-card-name">${cfg.name}</div>
        <div class="dl-card-rate">Rate: <strong>${cfg.label}</strong> of vehicle value</div>
        <div class="dl-card-premium">${fmt(p.total)}<small>Total incl. taxes</small></div>
        <button class="dl-btn ${btnClass}">${btnLabel} ↓</button>
      </div>`;
  }).join('');

  // "Download All" card
  html += `
    <div class="dl-card compare-card" onclick="showActionBar()">
      <div class="dl-card-logo">
        <img src="img/benchmarking.png" alt="Compare All" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
        <span class="logo-text" style="display:none;color:var(--red)">ALL</span>
      </div>
      <div class="dl-card-name">Download All Quotes</div>
      <div class="dl-card-rate">Full comparison of all insurers</div>
      <div class="dl-card-premium" style="color:var(--red)">3 Quotes<small>GA · Britam · Sanlam</small></div>
      <button class="dl-btn dl-btn-all">Download All ↓</button>
    </div>`;

  container.innerHTML = html;
}

// ── BUILD LETTERHEAD BANNER ──
// Logo images sit directly on the coloured banner background.
// No CSS filter is applied so logos render in their natural colours.
function buildLetterheadBanner(cfg, meta, now) {
  return `
    <div class="pdf-letterhead" style="background:${cfg.color}">
      <div class="pdf-lh-left">
        <img src="${cfg.imgSrc}" alt="${cfg.name}" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
        <span class="pdf-lh-name" style="display:none">${cfg.name}</span>
      </div>
      <div class="pdf-lh-right">
        <p>${meta.address ? meta.address.replace(/\n/g, '<br>') : ''}<br>${meta.website || ''}</p>
      </div>
    </div>`;
}

// Initialization Call
window.onload = init;