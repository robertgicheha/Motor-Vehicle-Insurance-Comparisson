function fmt(n) {
  return 'KES ' + Number(n).toLocaleString('en-KE', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
}

function getCompanyData() {
  return window.quotationData || [];
}

function getCompanyBySlug(slug) {
  return getCompanyData().find(function(c) { return c.slug === slug; }) || null;
}

function calcPremiums(vehicleValue, company) {
  var basic = vehicleValue * company.rate;
  var tax = basic * (company.taxRate || 0.005);
  var total = basic + tax;
  return { basic: basic, tax: tax, total: total };
}

function getFormData() {
  if (window.quotationDbData) {
    return window.quotationDbData;
  }
  try {
    var raw = sessionStorage.getItem('micQuoteData');
    if (raw) {
      var d = JSON.parse(raw);
      return {
        salutation: d.salutation || '',
        firstName: d.firstName || d.first || '',
        lastName: d.lastName || d.last || '',
        phone: d.phone || '',
        email: d.email || '',
        reg: d.reg || '',
        chassis: d.chassis || '',
        make: d.make || '',
        model: d.model || '',
        body: d.body || '',
        year: d.year || '',
        color: d.color || '',
        value: Number(d.value) || 0,
        quoteNum: d.quoteNum || '',
      };
    }
  } catch(e) {}
  return {
    salutation: '',
    firstName: '',
    lastName: '',
    phone: '',
    email: '',
    reg: '',
    chassis: '',
    make: '',
    model: '',
    body: '',
    year: '',
    color: '',
    value: 0,
    quoteNum: '',
  };
}

function genQuoteNum(company) {
  var d = new Date();
  var y = d.getFullYear();
  var n = String(Math.floor(Math.random() * 9000) + 1000);
  return (company.prefix || 'MIC') + '-' + y + '/' + n;
}

function showActionBar() {
  document.getElementById('action-bar').classList.add('visible');
}

function copyQuoteNumber() {
  var el = document.getElementById('hero-quote-num');
  var text = el.textContent;
  if (!text || text === '—') return;
  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(text).then(function() {
      var btn = document.getElementById('copy-quote-btn');
      btn.classList.add('copied');
      btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Copied';
      setTimeout(function() {
        btn.classList.remove('copied');
        btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> Copy';
      }, 2000);
    });
  }
}

function showToast(message, type) {
  type = type || 'success';
  var container = document.getElementById('toast-container');
  if (!container) return;
  var toast = document.createElement('div');
  toast.className = 'toast ' + type;
  var icon = type === 'success' ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>' : '';
  toast.innerHTML = icon + message;
  container.appendChild(toast);
  setTimeout(function() { toast.remove(); }, 3000);
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

function renderCompanyLogo(slug) {
  var companies = window.quotationCompanies || [];
  var company = companies.find(function(c) { return c.slug === slug; });
  var img = document.getElementById('hero-company-logo');
  if (company && company.logo) {
    img.src = company.logo;
    img.style.display = 'block';
    img.onerror = function() { fallbackLogo(img, slug); };
  } else {
    img.style.display = 'none';
  }
}

function renderComparisonGrid(companies, value, selectedSlug) {
  var container = document.getElementById('comparison-grid');
  if (!container) return;
  var html = companies.map(function(c) {
    var p = calcPremiums(value, c);
    var isSelected = c.slug === selectedSlug;
    var logo = c.logo || '';
    return '<div class="comparison-card' + (isSelected ? ' selected' : '') + '" onclick="downloadQuote(\'' + c.slug + '\')">' +
      '<div class="comparison-card-header">' +
        (logo ? '<img src="' + logo + '" alt="' + c.name + '" onerror="fallbackLogo(this,\'' + c.slug + '\')">' : '') +
        '<span class="initials" style="color:' + c.color + '">' + (c.prefix || c.slug.toUpperCase()) + '</span>' +
      '</div>' +
      '<div class="comparison-card-body">' +
        '<div class="cmp-row"><span>Rate</span><span>' + (c.rate * 100).toFixed(1) + '%</span></div>' +
        '<div class="cmp-row"><span>Basic Premium</span><span>' + fmt(p.basic) + '</span></div>' +
        '<div class="cmp-row"><span>IRA Levy</span><span>' + fmt(p.tax) + '</span></div>' +
        '<div class="cmp-total"><span>Total</span><span>' + fmt(p.total) + '</span></div>' +
      '</div>' +
    '</div>';
  }).join('');
  container.innerHTML = html;
}

function renderCompareTable(companies, value) {
  var names = companies.map(function(c) { return c.name; });
  var maxCols = 3;
  for (var i = 0; i < maxCols; i++) {
    var th = document.getElementById('cmp-head-' + i);
    var td = document.getElementById('cmp-total-' + i);
    if (companies[i]) {
      if (th) th.textContent = companies[i].name;
      var p = calcPremiums(value, companies[i]);
      if (td) td.textContent = fmt(p.total);
    } else {
      if (th) { th.textContent = '—'; th.style.display = 'none'; }
      if (td) { td.textContent = '—'; td.style.display = 'none'; }
    }
  }

  var rows = [
    { label: 'Estimated Vehicle Value', vals: companies.map(function() { return fmt(value); }) },
    { label: 'Premium Rate', vals: companies.map(function(c) { return (c.rate * 100).toFixed(1) + '%'; }) },
    { label: 'Basic Annual Premium', vals: companies.map(function(c) { return fmt(calcPremiums(value, c).basic); }) },
    { label: 'IRA Levy (0.5%)', vals: companies.map(function(c) { return fmt(calcPremiums(value, c).tax); }) },
  ];
  var tbody = document.getElementById('compare-rows');
  if (!tbody) return;
  tbody.innerHTML = rows.map(function(r) {
    var cells = '<td>' + r.label + '</td>';
    for (var i = 0; i < maxCols; i++) {
      cells += '<td>' + (r.vals[i] || '—') + '</td>';
    }
    return '<tr>' + cells + '</tr>';
  }).join('');
}

function renderDownloadCards(activeCompany, value, prems, fd, qNum) {
  var container = document.getElementById('dl-cards');
  var companies = getCompanyData();

  var html = companies.map(function(c) {
    var p = calcPremiums(value, c);
    var selected = (c.slug === activeCompany.slug) ? 'border-color:var(--blue);box-shadow:0 0 0 2px rgba(0,102,179,.15)' : '';
    var logo = c.logo || '';
    return '<div class="dl-card" style="' + selected + '" onclick="downloadQuote(\'' + c.slug + '\')">' +
      '<div class="dl-card-logo">' +
        (logo ? '<img src="' + logo + '" alt="' + c.name + '" onerror="fallbackLogo(this,\'' + c.slug + '\')">' : '') +
        '<span class="logo-text" style="display:' + (logo ? 'none' : 'block') + '">' + (c.prefix || c.slug) + '</span>' +
      '</div>' +
      '<div class="dl-card-name">' + c.name + '</div>' +
      '<div class="dl-card-rate">Rate: <strong>' + (c.rate * 100).toFixed(1) + '%</strong> of vehicle value</div>' +
      '<div class="dl-card-premium">' + fmt(p.total) + '<small>Total incl. taxes</small></div>' +
      '<button class="dl-btn" style="background:' + c.color + ';color:#fff">Download ' + c.name.split(' ')[0] + ' Quote ↓</button>' +
    '</div>';
  }).join('');

  container.innerHTML = html;
}

function buildPDFPreview(company, value, prem, fd, qNum) {
  var name = [fd.salutation, fd.firstName, fd.lastName].filter(Boolean).join(' ') || '—';
  var now = new Date().toLocaleString('en-KE', { dateStyle: 'long', timeStyle: 'short' });
  var logo = company.logo || '';
  var html =
    '<div class="pdf-letterhead" style="background:' + company.color + '">' +
      '<div class="pdf-lh-left">' +
        (logo ? '<img src="' + logo + '" alt="' + company.name + '" onerror="fallbackLogo(this,\'' + company.slug + '\')">' : '') +
        '<span class="pdf-lh-name" style="display:' + (logo ? 'none' : 'block') + ';color:#fff;font-size:22px;font-weight:800">' + (company.prefix || company.slug) + '</span>' +
      '</div>' +
      '<div class="pdf-lh-right"><p style="color:rgba(255,255,255,.92);font-size:12px;line-height:1.75;margin:0">' + company.name + '<br>Website: micglobalrisks.com</p></div>' +
    '</div>' +
    '<div class="pdf-body">' +
      '<p style="margin-top:4px">' + now + '</p>' +
      '<p>Dear ' + name + ',</p>' +
      '<h2>RE: PRIVATE CAR INSURANCE QUOTE</h2>' +
      '<p class="pdf-date">Quotation Reference: <strong>' + qNum + '</strong></p>' +
      '<p>Thank you for requesting us to provide a quote for the above mentioned. We are pleased to submit the quote</p>' +
      '<table><thead><tr><th>Owner\'s Name</th><th>Vehicle Make</th><th>Year of Manufacture</th></tr></thead><tbody><tr><td>' + name + '</td><td>' + (fd.make || '—') + '</td><td>' + (fd.year || '—') + '</td></tr></tbody></table>' +
      '<table><thead><tr><th>Registration Number</th><th>Vehicle Estimated Price</th><th>Phone Number</th></tr></thead><tbody><tr><td>' + (fd.reg || '—') + '</td><td>' + fmt(value) + '</td><td>' + (fd.phone || '—') + '</td></tr></tbody></table>' +
      '<table><thead><tr><th colspan="2">COVER TYPE</th></tr></thead><tbody><tr><td>Scope of Cover</td><td>Comprehensive</td></tr><tr><td>Frequency</td><td>Annually</td></tr></tbody></table>' +
      '<table><thead><tr><th colspan="2" style="background:' + company.color + ';color:#fff;border-color:' + company.color + '">SCHEDULE OF INSURANCE</th></tr></thead><tbody>' +
        '<tr><td>Basic Annual Premium</td><td>' + fmt(prem.basic) + '</td></tr>' +
        '<tr><td>Annual Premium + Taxes</td><td>' + fmt(prem.total) + '</td></tr>' +
        '<tr><td>Windscreen Annual Premium</td><td>KES 0</td></tr>' +
        '<tr><td>Radio Cassette Premium</td><td>KES 0</td></tr>' +
      '</tbody></table>' +
    '</div>' +
    '<div class="pdf-footer-text">KENYA | UGANDA | TANZANIA | Rate subject to satisfactory valuation report</div>';

  var wrap = document.getElementById('pdf-preview');
  wrap.innerHTML = html;
  wrap.style.display = 'none';
}

function saveSessionQuotation(fd, slug) {
  if (window.quotationDbData) return;
  if (!window.MIC_AUTH) return;
  try {
    var raw = sessionStorage.getItem('micQuoteData');
    if (!raw) return;
    var data = JSON.parse(raw);
    var company = slug ? getCompanyBySlug(slug) : null;
    if (!company) return;
    fetch('/api/quotation/from-session', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
      body: JSON.stringify({
        company_slug: slug,
        car_make: data.make || '',
        car_model: data.model || '',
        car_year: data.year || '',
        car_registration: data.reg || '',
        car_valuation: Number(data.value) || 0,
      }),
    }).then(function(r) { return r.json(); }).then(function(res) {
      if (res.success) {
        window.quotationDbData = fd;
        window.quotationDbData.quoteNum = res.quotation.quotation_number;
        document.getElementById('hero-quote-num').textContent = res.quotation.quotation_number;
        try { sessionStorage.removeItem('micQuoteData'); } catch(e) {}
        try { sessionStorage.removeItem('pending_quote_company'); } catch(e) {}
        showToast('Quotation generated', 'success');
      }
    }).catch(function(e) { console.warn('Failed to save quotation:', e); });
  } catch(e) {}
}

function init() {
  var fd = getFormData();
  var companies = getCompanyData();
  var slug = window.selectedCompanySlug;
  var value = Number(fd.value) || 800000;
  var company = slug ? getCompanyBySlug(slug) : (companies[0] || null);

  if (!company) {
    document.getElementById('hero-quote-num').textContent = fd.quoteNum || '—';
    return;
  }

  saveSessionQuotation(fd, slug);

  var prems = calcPremiums(value, company);
  var qNum = fd.quoteNum || genQuoteNum(company);

  renderCompanyLogo(slug);

  document.getElementById('hero-quote-num').textContent = qNum;

  document.getElementById('s-name').textContent = [fd.salutation, fd.firstName, fd.lastName].filter(Boolean).join(' ') || '—';
  document.getElementById('s-phone').textContent = fd.phone || '—';
  document.getElementById('s-email').textContent = fd.email || '—';
  document.getElementById('s-reg').textContent = fd.reg || '—';
  document.getElementById('s-make').textContent = ((fd.make || '') + ' ' + (fd.model || '')).trim() || '—';
  document.getElementById('s-year').textContent = (fd.year || '—') + '  •  ' + (fd.body || '—');
  document.getElementById('s-color').textContent = fd.color || '—';
  document.getElementById('s-chassis').textContent = fd.chassis || '—';
  document.getElementById('s-value').textContent = fmt(value);

  document.getElementById('single-breakdown-wrap').style.display = '';
  document.getElementById('compare-all-wrap').style.display = '';
  document.getElementById('breakdown-company-label').textContent = company.name;

  var rows = [
    ['Estimated Vehicle Value', '—', fmt(value)],
    ['Basic Annual Premium', '@ ' + (company.rate * 100).toFixed(1) + '% of vehicle value', fmt(prems.basic)],
    ['Windscreen Premium', 'Included', 'KES 0'],
    ['Radio / Cassette Premium', 'Included', 'KES 0'],
    ['IRA Levy (Training Levy)', '@ 0.5% of basic premium', fmt(prems.tax)],
  ];
  var tbody = document.getElementById('breakdown-rows');
  tbody.innerHTML = rows.map(function(r) {
    return '<tr><td>' + r[0] + '</td><td style="color:var(--text-muted);font-size:12.5px">' + r[1] + '</td><td>' + r[2] + '</td></tr>';
  }).join('');
  document.getElementById('breakdown-total').textContent = fmt(prems.total);

  renderComparisonGrid(companies, value, slug);
  renderCompareTable(companies, value);
  renderDownloadCards(company, value, prems, fd, qNum);
  buildPDFPreview(company, value, prems, fd, qNum);

  try {
    var raw = sessionStorage.getItem('micQuoteData');
    if (raw) showToast('Quotation generated', 'success');
  } catch(e) {}
}

function actionDownload() {
  var wrap = document.getElementById('pdf-preview');
  if (!wrap.innerHTML.trim()) return;
  wrap.style.display = 'block';
  setTimeout(function() {
    window.print();
    setTimeout(function() {
      if (!document.getElementById('btn-preview').classList.contains('active')) {
        wrap.style.display = 'none';
      }
    }, 500);
  }, 150);
  showToast('Quotation downloaded', 'success');
}

function actionPrint() {
  var wrap = document.getElementById('pdf-preview');
  if (!wrap.innerHTML.trim()) return;
  wrap.style.display = 'block';
  setTimeout(function() { window.print(); }, 150);
  showToast('Quotation printed', 'success');
}

function actionTogglePreview() {
  var wrap = document.getElementById('pdf-preview');
  var btn = document.getElementById('btn-preview');
  if (wrap.style.display === 'none' || !wrap.style.display) {
    wrap.style.display = 'block';
    btn.classList.add('active');
    wrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
  } else {
    wrap.style.display = 'none';
    btn.classList.remove('active');
  }
}

function downloadQuote(slug) {
  var company = getCompanyBySlug(slug);
  if (!company) return;
  var fd = getFormData();
  var value = Number(fd.value) || 800000;
  var prems = calcPremiums(value, company);
  var qNum = fd.quoteNum || genQuoteNum(company);
  buildPDFPreview(company, value, prems, fd, qNum);
  document.getElementById('btn-preview').classList.remove('active');
  showActionBar();
  document.getElementById('pdf-preview').style.display = 'block';
  setTimeout(function() { window.print(); }, 150);
  showToast('Quotation downloaded', 'success');
}

window.addEventListener('message', function(e) {
  if (e.data === 'quote-generated') showToast('Quotation generated', 'success');
  if (e.data === 'quote-downloaded') showToast('Quotation downloaded', 'success');
  if (e.data === 'quote-printed') showToast('Quotation printed', 'success');
});

document.addEventListener('DOMContentLoaded', init);
