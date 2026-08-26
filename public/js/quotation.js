function fmt(n) {
  return 'KES ' + Number(n).toLocaleString('en-KE', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
}

function getCompanyData() {
  return window.quotationData || [];
}

function getCompanyBySlug(slug) {
  return getCompanyData().find(function(c) { return c.slug === slug; }) || null;
}

function calcPremiums(vehicleValue, company, numPassengers) {
  var rate = company.rate || 0;
  var minPremium = company.minPremium || 0;
  var basicPremium = vehicleValue * rate;
  if (basicPremium < minPremium) basicPremium = minPremium;
  var isPvtInclusive = company.isPvtInclusive || false;
  var pvt = 0;
  if (!isPvtInclusive) {
    var pvtRate = company.pvtRate || 0;
    pvt = vehicleValue * pvtRate;
    var pvtMin = company.pvtMinValue || 0;
    if (pvt < pvtMin) pvt = pvtMin;
  }
  var pllPerPerson = company.pllPerPerson || 0;
  var pll = pllPerPerson * (numPassengers || 1);
  var levis = (basicPremium + pvt + pll) * 0.0045;
  var stampDuty = 40;
  var total = basicPremium + pvt + pll + levis + stampDuty;
  return { basic: basicPremium, pvt: pvt, pll: pll, levis: levis, stampDuty: stampDuty, total: total };
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
    salutation: '', firstName: '', lastName: '', phone: '', email: '',
    reg: '', chassis: '', make: '', model: '', body: '', year: '', color: '',
    value: 0, quoteNum: '',
  };
}

function genQuoteNum(company) {
  var d = new Date();
  var y = d.getFullYear();
  var n = String(Math.floor(Math.random() * 9000) + 1000);
  return (company.prefix || 'MIC') + '-' + y + '/' + n;
}

// Action bar is always visible in the new layout; kept for parity with the old call sites.
function showActionBar() {
  var bar = document.getElementById('action-bar');
  if (bar) bar.classList.add('visible');
}

function copyQuoteNumber() {
  var el = document.getElementById('hero-quote-num');
  var text = el ? el.textContent : '';
  if (!text || text === '—') return;
  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(text).then(function() {
      var btn = document.getElementById('copy-quote-btn');
      btn.classList.add('copied');
      btn.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Copied';
      setTimeout(function() {
        btn.classList.remove('copied');
        btn.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> Copy';
      }, 2000);
    });
  }
}

function showToast(message, type) {
  type = type || 'success';
  if (type === 'success') {
    if (window.showToastSuccess) return window.showToastSuccess(message);
  } else {
    if (window.showToastError) return window.showToastError(message);
  }
  // Fallback if toast component not present
  var container = document.getElementById('toast-container');
  if (container) {
    var toast = document.createElement('div');
    toast.className = 'toast ' + type;
    toast.textContent = message;
    container.appendChild(toast);
    setTimeout(function() { toast.remove(); }, 3000);
  }
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
    img.classList.remove('hidden');
    img.onerror = function() { fallbackLogo(img, slug); };
  } else if (img) {
    img.style.display = 'none';
  }
}

function renderComparisonGrid(companies, value, selectedSlug) {
  var container = document.getElementById('comparison-grid');
  if (!container) return;
  var html = companies.map(function(c) {
    var p = calcPremiums(value, c, window.quotationNumPassengers);
    var isSelected = c.slug === selectedSlug;
    var logo = c.logo || '';
    return '<button type="button" onclick="downloadQuote(\'' + c.slug + '\')" class="text-left rounded-2xl border bg-white dark:bg-gray-800 shadow-sm p-5 transition hover:-translate-y-0.5 hover:shadow-md ' +
      (isSelected ? 'border-blue-500 ring-2 ring-blue-500/20' : 'border-gray-200 dark:border-gray-700') + '">' +
        '<div class="flex items-center gap-3 mb-3 h-10">' +
          (logo ? '<img src="' + logo + '" alt="' + c.name + '" class="max-h-9 max-w-[120px] object-contain" onerror="fallbackLogo(this,\'' + c.slug + '\')">' : '') +
          '<span class="text-[11px] font-extrabold tracking-wider text-blue-600 dark:text-blue-400">' + (c.prefix || c.slug.toUpperCase()) + '</span>' +
        '</div>' +
        '<div class="space-y-1.5 text-xs">' +
          '<div class="flex justify-between"><span class="text-gray-500">Rate</span><span class="font-semibold text-gray-800 dark:text-white">' + (c.rate * 100).toFixed(1) + '%</span></div>' +
          '<div class="flex justify-between"><span class="text-gray-500">Basic Premium</span><span class="font-semibold text-gray-800 dark:text-white">' + fmt(p.basic) + '</span></div>' +
          '<div class="flex justify-between"><span class="text-gray-500">PVT</span><span class="font-semibold text-gray-800 dark:text-white">' + fmt(p.pvt) + '</span></div>' +
          '<div class="flex justify-between pt-1.5 border-t border-gray-100 dark:border-gray-700"><span class="font-semibold text-gray-800 dark:text-white">Total</span><span class="font-extrabold text-blue-600 dark:text-blue-400">' + fmt(p.total) + '</span></div>' +
        '</div>' +
      '</button>';
  }).join('');
  container.innerHTML = html;
}

function renderCompareTable(companies, value) {
  var maxCols = 3;
  for (var i = 0; i < maxCols; i++) {
    var th = document.getElementById('cmp-head-' + i);
    var td = document.getElementById('cmp-total-' + i);
    if (companies[i]) {
      if (th) th.textContent = companies[i].name;
      var p = calcPremiums(value, companies[i], window.quotationNumPassengers);
      if (td) td.textContent = fmt(p.total);
    } else {
      if (th) { th.textContent = '—'; th.style.display = 'none'; }
      if (td) { td.textContent = '—'; td.style.display = 'none'; }
    }
  }

  var rows = [
    { label: 'Estimated Vehicle Value', vals: companies.map(function() { return fmt(value); }) },
    { label: 'Premium Rate', vals: companies.map(function(c) { return (c.rate * 100).toFixed(1) + '%'; }) },
    { label: 'Basic Annual Premium', vals: companies.map(function(c) { return fmt(calcPremiums(value, c, window.quotationNumPassengers).basic); }) },
    { label: 'PVT Premium', vals: companies.map(function(c) { return fmt(calcPremiums(value, c, window.quotationNumPassengers).pvt); }) },
    { label: 'PLL Premium', vals: companies.map(function(c) { return fmt(calcPremiums(value, c, window.quotationNumPassengers).pll); }) },
    { label: 'Levis & Stamp Duty', vals: companies.map(function(c) { var pp = calcPremiums(value, c, window.quotationNumPassengers); return fmt(pp.levis + pp.stampDuty); }) },
  ];
  var tbody = document.getElementById('compare-rows');
  if (!tbody) return;
  tbody.innerHTML = rows.map(function(r) {
    var cells = '<td class="py-2 text-gray-800 dark:text-white">' + r.label + '</td>';
    for (var i = 0; i < maxCols; i++) {
      cells += '<td class="py-2 text-right font-semibold text-gray-800 dark:text-white">' + (r.vals[i] || '—') + '</td>';
    }
    return '<tr class="border-t border-gray-100 dark:border-gray-700">' + cells + '</tr>';
  }).join('');
}

function renderDownloadCards(activeCompany, value, prems, fd, qNum) {
  var container = document.getElementById('dl-cards');
  var companies = getCompanyData();
  var html = companies.map(function(c) {
    var p = calcPremiums(value, c, window.quotationNumPassengers);
    var selected = (c.slug === activeCompany.slug);
    var logo = c.logo || '';
    return '<button type="button" onclick="downloadQuote(\'' + c.slug + '\')" class="text-left rounded-2xl border bg-white dark:bg-gray-800 shadow-sm p-4 text-center transition hover:-translate-y-0.5 hover:shadow-md ' +
      (selected ? 'border-blue-500 ring-2 ring-blue-500/20' : 'border-gray-200 dark:border-gray-700') + '">' +
        '<div class="h-11 flex items-center justify-center mb-3">' +
          (logo ? '<img src="' + logo + '" alt="' + c.name + '" class="max-h-9 max-w-[110px] object-contain" onerror="fallbackLogo(this,\'' + c.slug + '\')">' : '') +
          '<span class="text-[12px] font-extrabold tracking-wider text-blue-600 dark:text-blue-400" style="display:' + (logo ? 'none' : 'block') + '">' + (c.prefix || c.slug) + '</span>' +
        '</div>' +
        '<div class="text-xs font-bold text-gray-800 dark:text-white">' + c.name + '</div>' +
        '<div class="text-[11px] text-gray-400 mb-3">Rate: <strong>' + (c.rate * 100).toFixed(1) + '%</strong> of vehicle value</div>' +
        '<div class="text-lg font-extrabold text-blue-600 dark:text-blue-400 mb-3">' + fmt(p.total) + '<small class="block text-[10px] font-semibold text-gray-400">Total incl. taxes</small></div>' +
        '<span class="block w-full rounded-lg py-2 text-xs font-bold text-white" style="background:' + (c.color || '#005F9E') + '">Download ' + c.name.split(' ')[0] + ' Quote ↓</span>' +
      '</button>';
  }).join('');
  container.innerHTML = html;
}

function buildPDFPreview(company, value, prem, fd, qNum) {
  var name = [fd.salutation, fd.firstName, fd.lastName].filter(Boolean).join(' ') || '—';
  var now = new Date().toLocaleString('en-KE', { dateStyle: 'long', timeStyle: 'short' });
  var logo = company.logo || '';
  var html =
    '<div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 mb-6" style="background:' + company.color + '">' +
      '<div class="flex justify-between items-stretch min-h-[90px]">' +
        '<div class="flex items-center p-5">' +
          (logo ? '<img src="' + logo + '" alt="' + company.name + '" class="max-h-11 max-w-[160px] object-contain" onerror="fallbackLogo(this,\'' + company.slug + '\')">' : '') +
          '<span class="text-white text-2xl font-extrabold" style="display:' + (logo ? 'none' : 'block') + '">' + (company.prefix || company.slug) + '</span>' +
        '</div>' +
        '<div class="flex items-center justify-end p-5 text-right">' +
          '<p class="text-[12px] leading-7 text-white/90 m-0">' + company.name + '<br>Website: micglobalrisks.com</p>' +
        '</div>' +
      '</div>' +
    '</div>' +
    '<div class="px-2">' +
      '<p class="text-[13px]">' + now + '</p>' +
      '<p class="text-gray-800 dark:text-white">Dear ' + name + '</p>' +
      '<h2 class="text-[15px] font-bold uppercase tracking-wider mb-1">RE: PRIVATE CAR INSURANCE QUOTE</h2>' +
      '<p class="text-[12px] text-gray-500 mb-4">Quotation Reference: <strong>' + qNum + '</strong></p>' +
      '<p class="text-[13px] leading-7 mb-4 text-gray-700 dark:text-gray-300">Thank you for requesting us to provide a quote for the above mentioned. We are pleased to submit the quote</p>' +
      '<table class="w-full text-[13px] border-collapse mb-4"><thead><tr><th class="bg-gray-100 dark:bg-gray-700 text-left p-2 border border-gray-200 dark:border-gray-600">Owner\'s Name</th><th class="bg-gray-100 dark:bg-gray-700 text-left p-2 border border-gray-200 dark:border-gray-600">Vehicle Make</th><th class="bg-gray-100 dark:bg-gray-700 text-left p-2 border border-gray-200 dark:border-gray-600">Year of Manufacture</th></tr></thead><tbody><tr><td class="p-2 border border-gray-200 dark:border-gray-600">' + name + '</td><td class="p-2 border border-gray-200 dark:border-gray-600">' + (fd.make || '—') + '</td><td class="p-2 border border-gray-200 dark:border-gray-600">' + (fd.year || '—') + '</td></tr></tbody></table>' +
      '<table class="w-full text-[13px] border-collapse mb-4"><thead><tr><th class="bg-gray-100 dark:bg-gray-700 text-left p-2 border border-gray-200 dark:border-gray-600">Registration Number</th><th class="bg-gray-100 dark:bg-gray-700 text-left p-2 border border-gray-200 dark:border-gray-600">Vehicle Estimated Price</th><th class="bg-gray-100 dark:bg-gray-700 text-left p-2 border border-gray-200 dark:border-gray-600">Phone Number</th></tr></thead><tbody><tr><td class="p-2 border border-gray-200 dark:border-gray-600">' + (fd.reg || '—') + '</td><td class="p-2 border border-gray-200 dark:border-gray-600">' + fmt(value) + '</td><td class="p-2 border border-gray-200 dark:border-gray-600">' + (fd.phone || '—') + '</td></tr></tbody></table>' +
      '<table class="w-full text-[13px] border-collapse mb-4"><thead><tr><th colspan="2" class="text-left p-2 border text-white" style="background:' + company.color + '">COVER TYPE</th></tr></thead><tbody><tr><td class="p-2 border border-gray-200 dark:border-gray-600">Scope of Cover</td><td class="p-2 border border-gray-200 dark:border-gray-600">Comprehensive</td></tr><tr><td class="p-2 border border-gray-200 dark:border-gray-600">Frequency</td><td class="p-2 border border-gray-200 dark:border-gray-600">Annually</td></tr></tbody></table>' +
      '<table class="w-full text-[13px] border-collapse mb-4"><thead><tr><th colspan="2" class="text-left p-2 border text-white" style="background:' + company.color + '">SCHEDULE OF INSURANCE</th></tr></thead><tbody>' +
        '<tr><td class="p-2 border border-gray-200 dark:border-gray-600">Basic Annual Premium</td><td class="p-2 border border-gray-200 dark:border-gray-600">' + fmt(prem.basic) + '</td></tr>' +
        '<tr><td class="p-2 border border-gray-200 dark:border-gray-600">Annual Premium + Taxes</td><td class="p-2 border border-gray-200 dark:border-gray-600">' + fmt(prem.total) + '</td></tr>' +
        '<tr><td class="p-2 border border-gray-200 dark:border-gray-600">Windscreen Annual Premium</td><td class="p-2 border border-gray-200 dark:border-gray-600">KES 0</td></tr>' +
        '<tr><td class="p-2 border border-gray-200 dark:border-gray-600">Radio Cassette Premium</td><td class="p-2 border border-gray-200 dark:border-gray-600">KES 0</td></tr>' +
      '</tbody></table>' +
    '</div>' +
    '<div class="px-5 py-4 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 text-[11px] leading-7 text-gray-400">KENYA | UGANDA | TANZANIA | Rate subject to satisfactory valuation report</div>';

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
        vehicle_type: data.vehicleType || window.quotationVehicleType || 'psv',
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

  var np = fd.numPassengers || window.quotationNumPassengers || 1;
  var prems = calcPremiums(value, company, np);
  var qNum = fd.quoteNum || genQuoteNum(company);

  // Keep Alpine hero label in sync with the selected company.
  var alpineEl = document.querySelector('[x-data]');
  if (alpineEl && alpineEl.__x) alpineEl.__x.$data.selectedName = company.name;

  renderCompanyLogo(slug);

  document.getElementById('hero-quote-num').textContent = qNum;

  document.getElementById('s-name').textContent = [fd.salutation, fd.firstName, fd.lastName].filter(Boolean).join(' ') || '—';
  document.getElementById('s-phone').textContent = fd.phone || '—';
  document.getElementById('s-email').textContent = fd.email || '—';
  document.getElementById('s-company').textContent = company.name;
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
    ['PVT Premium', '@ ' + (company.pvtRate * 100).toFixed(2) + '%', fmt(prems.pvt)],
    ['PLL Premium', '@ KES ' + (company.pllPerPerson || 0) + ' per person × ' + (window.quotationNumPassengers || 1) + ' pax', fmt(prems.pll)],
    ['Levis (0.45%)', 'On Basic + PVT + PLL', fmt(prems.levis)],
    ['Stamp Duty', 'Fixed', fmt(prems.stampDuty)],
  ];
  var tbody = document.getElementById('breakdown-rows');
  tbody.innerHTML = rows.map(function(r) {
    return '<tr><td class="py-2 text-gray-800 dark:text-white">' + r[0] + '</td><td class="py-2 text-gray-400 text-[12.5px]">' + r[1] + '</td><td class="py-2 text-right font-semibold text-gray-800 dark:text-white">' + r[2] + '</td></tr>';
  }).join('');
  document.getElementById('breakdown-total').textContent = fmt(prems.total);

  renderComparisonGrid(companies, value, slug);
  renderCompareTable(companies, value);
  renderDownloadCards(company, value, prems, fd, qNum);
  buildPDFPreview(company, value, prems, fd, qNum);

  // Compact collapsed row
  var compactVehicle = document.getElementById('compact-vehicle');
  var compactCompany = document.getElementById('compact-company');
  if (compactVehicle) compactVehicle.textContent = ((fd.make || '') + ' ' + (fd.model || '')).trim() || '—';
  if (compactCompany) compactCompany.textContent = company.name;

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
    btn.classList.add('active', 'bg-blue-600', 'text-white');
    btn.classList.remove('text-blue-600', 'border-blue-600', 'hover:bg-blue-50', 'dark:hover:bg-blue-900/20');
    wrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
  } else {
    wrap.style.display = 'none';
    btn.classList.remove('active', 'bg-blue-600', 'text-white');
    btn.classList.add('text-blue-600', 'border-blue-600', 'hover:bg-blue-50', 'dark:hover:bg-blue-900/20');
  }
}

function downloadQuote(slug) {
  var company = getCompanyBySlug(slug);
  if (!company) return;
  var fd = getFormData();
  var value = Number(fd.value) || 800000;
  var np = fd.numPassengers || window.quotationNumPassengers || 1;
  var prems = calcPremiums(value, company, np);
  var qNum = fd.quoteNum || genQuoteNum(company);
  buildPDFPreview(company, value, prems, fd, qNum);
  document.getElementById('btn-preview').classList.remove('active', 'bg-blue-600', 'text-white');
  document.getElementById('btn-preview').classList.add('text-blue-600', 'border-blue-600');
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
