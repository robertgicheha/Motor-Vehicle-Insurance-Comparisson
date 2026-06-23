const TAX_RATE = window.__TAX_RATE || 0.005;
const _COMPANIES = window.__COMPANIES || [];

const RATES = {};
const COMPANY_META = {};

_COMPANIES.forEach(function (c) {
  var key = c.slug.toUpperCase().replace(/-/g, '_');
  var pRate = (c.rates || []).find(function (r) { return r.category === 'Premium Rate' && r.item_name === 'Premium Rate'; });
  var pct = pRate ? parseFloat(pRate.percentage) : 0;
  var colors = ['#005baa', '#0071bc', '#1d3c6e', '#2d6a4f', '#b9770e', '#6c3483', '#922b21', '#117a65'];
  var colorIdx = _COMPANIES.indexOf(c) % colors.length;

  RATES[key] = {
    id: c.id,
    rate: pct / 100,
    label: pct + '%',
    name: c.name,
    color: colors[colorIdx],
    imgSrc: c.logo ? ('storage/' + c.logo) : '',
    slug: c.slug
  };

  COMPANY_META[key] = {
    address: '',
    website: ''
  };
});

function fmt(n) {
  return 'KES ' + Number(n).toLocaleString('en-KE', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
}

function calcPremiums(vehicleValue) {
  var result = {};
  for (var key in RATES) {
    if (RATES.hasOwnProperty(key)) {
      var cfg = RATES[key];
      var basic = vehicleValue * cfg.rate;
      var tax = basic * TAX_RATE;
      var total = basic + tax;
      result[key] = { basic: basic, tax: tax, total: total };
    }
  }
  return result;
}

function getFormData() {
  try {
    var raw = sessionStorage.getItem('micQuoteData');
    if (raw) return JSON.parse(raw);
  } catch (e) {}
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
    mode: 'ALL'
  };
}

function getMode() {
  var p = new URLSearchParams(window.location.search);
  return p.get('company') || getFormData().mode || 'ALL';
}

function genQuoteNum(company) {
  var d = new Date();
  var y = d.getFullYear();
  var n = String(Math.floor(Math.random() * 9000) + 1000);
  return company + '-' + y + '/' + n;
}

function showActionBar() {
  document.getElementById('action-bar').classList.remove('hidden');
  document.getElementById('action-bar').classList.add('flex');
}

function init() {
  var fd = getFormData();
  var mode = getMode();
  var value = Number(fd.value) || 800000;
  var prems = calcPremiums(value);
  var qNum = fd.quoteNum || genQuoteNum(mode === 'ALL' ? 'MIC' : mode);

  document.getElementById('hero-quote-num').textContent = qNum;

  document.getElementById('s-name').textContent = (fd.salutation || '') + ' ' + fd.firstName + ' ' + fd.lastName;
  document.getElementById('s-phone').textContent = fd.phone || '\u2014';
  document.getElementById('s-email').textContent = fd.email || '\u2014';
  document.getElementById('s-reg').textContent = fd.reg || '\u2014';
  document.getElementById('s-make').textContent = (fd.make + ' ' + fd.model).trim() || '\u2014';
  document.getElementById('s-year').textContent = fd.year + '  \u2022  ' + (fd.body || '\u2014');
  document.getElementById('s-color').textContent = fd.color || '\u2014';
  document.getElementById('s-chassis').textContent = fd.chassis || '\u2014';
  document.getElementById('s-value').textContent = fmt(value);

  if (mode === 'ALL') {
    renderCompareAll(value, prems, fd, qNum);
    var firstKey = Object.keys(RATES)[0];
    if (firstKey) buildPDFPreview(firstKey, value, prems[firstKey], fd, qNum);
  } else {
    renderSingle(mode, value, prems, fd, qNum);
  }
}

function renderSingle(company, value, prems, fd, qNum) {
  var cfg = RATES[company];
  if (!cfg) return;
  var p = prems[company];

  document.getElementById('single-breakdown-wrap').style.display = '';
  document.getElementById('compare-all-wrap').classList.add('hidden');

  document.getElementById('breakdown-company-label').textContent = cfg.name;

  var rows = [
    ['Estimated Vehicle Value', '\u2014', fmt(value)],
    ['Basic Annual Premium', '@ ' + cfg.label + ' of vehicle value', fmt(p.basic)],
    ['Windscreen Premium', 'Included', 'KES 0'],
    ['Radio / Cassette Premium', 'Included', 'KES 0'],
    ['IRA Levy (Training Levy)', '@ 0.5% of basic premium', fmt(p.tax)]
  ];
  var tbody = document.getElementById('breakdown-rows');
  tbody.innerHTML = rows.map(function (r) {
    return '<tr><td class="px-5 py-3 text-[13.5px] border-b border-gray-200">' + r[0] + '</td><td class="px-5 py-3 text-[12.5px] text-gray-500 border-b border-gray-200">' + r[1] + '</td><td class="px-5 py-3 text-[13.5px] font-semibold text-right border-b border-gray-200">' + r[2] + '</td></tr>';
  }).join('');
  document.getElementById('breakdown-total').textContent = fmt(p.total);

  renderDownloadCards(company, value, prems, fd, qNum);
  buildPDFPreview(company, value, prems[company], fd, qNum);
}

function renderCompareAll(value, prems, fd, qNum) {
  document.getElementById('single-breakdown-wrap').style.display = 'none';
  document.getElementById('compare-all-wrap').classList.remove('hidden');
  document.getElementById('dl-cards-label').textContent = 'Download Individual Quote';

  var keys = Object.keys(RATES);

  var thead = document.getElementById('compare-all-wrap').querySelector('thead tr');
  thead.innerHTML = '<th class="px-[18px] py-3 text-[12px] font-bold uppercase tracking-wide text-left">Description</th>';
  keys.forEach(function (k) {
    thead.innerHTML += '<th class="px-[18px] py-3 text-[12px] font-bold uppercase tracking-wide text-right">' + RATES[k].name + '</th>';
  });

  var rows = [
    { label: 'Estimated Vehicle Value' },
    { label: 'Premium Rate' },
    { label: 'Basic Annual Premium' },
    { label: 'IRA Levy (0.5%)' }
  ];

  rows[0].vals = {};
  rows[1].vals = {};
  rows[2].vals = {};
  rows[3].vals = {};
  keys.forEach(function (k) {
    rows[0].vals[k] = fmt(value);
    rows[1].vals[k] = RATES[k].label;
    rows[2].vals[k] = fmt(prems[k].basic);
    rows[3].vals[k] = fmt(prems[k].tax);
  });

  var tbody = document.getElementById('compare-rows');
  tbody.innerHTML = rows.map(function (r) {
    var cells = '<td class="px-[18px] py-3 text-[13px] border-b border-gray-200">' + r.label + '</td>';
    keys.forEach(function (k) {
      cells += '<td class="px-[18px] py-3 text-[13px] font-semibold text-right border-b border-gray-200">' + (r.vals[k] || '\u2014') + '</td>';
    });
    return '<tr>' + cells + '</tr>';
  }).join('');

  var tfoot = document.getElementById('compare-all-wrap').querySelector('tfoot tr');
  tfoot.innerHTML = '<td class="px-[18px] py-3 text-[13px] font-bold border-t-2 border-gray-200">Total Premium (incl. taxes)</td>';
  keys.forEach(function (k) {
    tfoot.innerHTML += '<td class="px-[18px] py-3 text-[13px] font-bold border-t-2 border-gray-200 text-right text-[#0066B3]" id="cmp-total-' + k.toLowerCase() + '">' + fmt(prems[k].total) + '</td>';
  });

  renderDownloadCards('ALL', value, prems, fd, qNum);
}

function renderDownloadCards(mode, value, prems, fd, qNum) {
  var container = document.getElementById('dl-cards');
  var keys = Object.keys(RATES);

  var html = keys.map(function (k) {
    var cfg = RATES[k];
    var p = prems[k];
    var selected = (mode === k) ? 'border-[#0066B3] shadow-[0_0_0_2px_rgba(0,102,179,0.15)]' : '';
    var imgHtml = cfg.imgSrc ? '<img src="' + cfg.imgSrc + '" alt="' + cfg.name + '" class="max-h-[38px] max-w-[110px] object-contain" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'block\'">' : '';
    return '<div class="bg-white border border-gray-200 rounded-2xl p-5 pt-4 text-center cursor-pointer shadow-sm transition-all hover:shadow-md hover:-translate-y-1 hover:border-[#0066B3] ' + selected + '" onclick="showActionBar()">'
      + '<div class="h-11 flex items-center justify-center mx-auto mb-3">'
      + imgHtml
      + '<span class="text-[12px] font-extrabold tracking-wide text-[#0066B3]" style="display:none">' + k + '</span>'
      + '</div>'
      + '<div class="text-[12px] font-bold text-[#1a1a2e] mb-1">' + cfg.name + '</div>'
      + '<div class="text-[11px] text-gray-500 mb-3.5">Rate: <strong>' + cfg.label + '</strong> of vehicle value</div>'
      + '<div class="text-lg font-extrabold text-[#0066B3] mb-3.5 tabular-nums">' + fmt(p.total) + '<small class="text-[10px] font-semibold text-gray-500 block">Total incl. taxes</small></div>'
      + '<button class="block w-full py-2 rounded-lg text-[12px] font-bold tracking-wide cursor-pointer border-none transition-colors text-white" style="background:' + cfg.color + '">Download Quote \u2193</button>'
      + '</div>';
  }).join('');

  if (keys.length > 1) {
    html += '<div class="bg-white border-2 border-[#D0021B] rounded-2xl p-5 pt-4 text-center cursor-pointer shadow-sm transition-all hover:shadow-md hover:-translate-y-1" onclick="showActionBar()">'
      + '<div class="h-11 flex items-center justify-center mx-auto mb-3">'
      + '<span class="text-[12px] font-extrabold tracking-wide text-[#D0021B]">ALL</span>'
      + '</div>'
      + '<div class="text-[12px] font-bold text-[#1a1a2e] mb-1">Download All Quotes</div>'
      + '<div class="text-[11px] text-gray-500 mb-3.5">Full comparison of all insurers</div>'
      + '<div class="text-lg font-extrabold text-[#D0021B] mb-3.5">' + keys.length + ' Quotes<small class="text-[10px] font-semibold text-gray-500 block">' + keys.map(function (k) { return RATES[k].name.split(' ')[0]; }).join(' \u00b7 ') + '</small></div>'
      + '<button class="block w-full py-2 rounded-lg text-[12px] font-bold tracking-wide cursor-pointer border-none transition-colors bg-[#D0021B] text-white">Download All \u2193</button>'
      + '</div>';
  }

  container.innerHTML = html;
}

function buildLetterheadBanner(cfg, meta, now) {
  var imgHtml = cfg.imgSrc ? '<img src="' + cfg.imgSrc + '" alt="' + cfg.name + '" class="max-h-11 max-w-[160px] object-contain" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'block\'">' : '';
  return '<div class="flex justify-between items-stretch min-h-[90px]" style="background:' + cfg.color + '">'
    + '<div class="flex items-center px-7 min-w-[220px]">'
    + imgHtml
    + '<span class="text-[22px] font-extrabold text-white tracking-wide hidden">' + cfg.name + '</span>'
    + '</div>'
    + '<div class="flex-1 flex items-center justify-end px-7 text-right">'
    + '<p class="text-[12px] leading-relaxed text-white/92 m-0">' + (meta.address ? meta.address.replace(/\n/g, '<br>') : '') + '<br>' + (meta.website || '') + '</p>'
    + '</div>'
    + '</div>';
}

function buildPDFPreview(company, value, pData, fd, qNum) {
  var cfg = RATES[company];
  if (!cfg) return;
  var meta = COMPANY_META[company] || { address: '', website: '' };
  var now = new Date();
  var dateStr = now.toLocaleDateString('en-KE', { year: 'numeric', month: 'long', day: 'numeric' });

  var html = buildLetterheadBanner(cfg, meta, now);
  html += '<div class="px-9 py-7">';
  html += '<h2 class="text-[15px] font-bold uppercase tracking-wide mb-1.5">Insurance Quotation</h2>';
  html += '<div class="text-[12px] text-gray-500 mb-5">Date: ' + dateStr + ' | Ref: ' + qNum + '</div>';
  html += '<p class="text-[13px] leading-relaxed mb-4">Dear ' + (fd.salutation || '') + ' ' + fd.lastName + ',<br>Thank you for choosing ' + cfg.name + '. Please find below your comprehensive motor insurance quotation based on the vehicle details provided.</p>';

  html += '<table class="w-full border-collapse my-4 text-[13px]">';
  html += '<thead><tr><th class="bg-gray-100 px-3 py-2 text-left text-[11px] font-bold uppercase tracking-wide border border-gray-200">Item</th><th class="bg-gray-100 px-3 py-2 text-left text-[11px] font-bold uppercase tracking-wide border border-gray-200">Details</th><th class="bg-gray-100 px-3 py-2 text-right text-[11px] font-bold uppercase tracking-wide border border-gray-200">Amount (KES)</th></tr></thead>';
  html += '<tbody>';
  html += '<tr><td class="px-3 py-2 border border-gray-200">Estimated Vehicle Value</td><td class="px-3 py-2 border border-gray-200">' + fd.make + ' ' + fd.model + ' (' + fd.year + ')</td><td class="px-3 py-2 border border-gray-200 text-right font-semibold">' + fmt(value) + '</td></tr>';
  html += '<tr><td class="px-3 py-2 border border-gray-200">Basic Annual Premium</td><td class="px-3 py-2 border border-gray-200">@ ' + cfg.label + ' of vehicle value</td><td class="px-3 py-2 border border-gray-200 text-right font-semibold">' + fmt(pData.basic) + '</td></tr>';
  html += '<tr><td class="px-3 py-2 border border-gray-200">Windscreen Premium</td><td class="px-3 py-2 border border-gray-200">Included</td><td class="px-3 py-2 border border-gray-200 text-right font-semibold">KES 0</td></tr>';
  html += '<tr><td class="px-3 py-2 border border-gray-200">Radio / Cassette Premium</td><td class="px-3 py-2 border border-gray-200">Included</td><td class="px-3 py-2 border border-gray-200 text-right font-semibold">KES 0</td></tr>';
  html += '<tr><td class="px-3 py-2 border border-gray-200">IRA Levy (Training Levy)</td><td class="px-3 py-2 border border-gray-200">@ 0.5% of basic premium</td><td class="px-3 py-2 border border-gray-200 text-right font-semibold">' + fmt(pData.tax) + '</td></tr>';
  html += '<tr class="font-bold text-white" style="background:' + cfg.color + '"><td class="px-3 py-2 border border-gray-200" colspan="2">Total Premium (incl. taxes)</td><td class="px-3 py-2 border border-gray-200 text-right">' + fmt(pData.total) + '</td></tr>';
  html += '</tbody></table>';

  html += '<p class="text-[13px] leading-relaxed mb-4">This quotation is valid for 15 days from the date of issue. Comprehensive cover is subject to a satisfactory valuation report conducted within the first month of cover. Subject to sanction limitation and exclusion clause and standard policy terms and conditions.</p>';
  html += '<p class="text-[13px] leading-relaxed">Geographical limit: Kenya / Uganda / Tanzania (Yellow Card required for TPO outside Kenya).</p>';
  html += '</div>';
  html += '<div class="px-9 py-5 bg-gray-50 border-t border-gray-200 text-[11px] text-gray-500 leading-relaxed">';
  html += 'Generated by MIC Global Risks Insurance (Kenya) Limited | www.micglobalrisks.com | This is a computer-generated quotation and does not require a signature.';
  html += '</div>';

  document.getElementById('pdf-preview').innerHTML = html;
}

function downloadQuote(company) {
  showActionBar();
  var fd = getFormData();
  var value = Number(fd.value) || 800000;
  var prems = calcPremiums(value);
  var qNum = fd.quoteNum || genQuoteNum(company);
  buildPDFPreview(company, value, prems[company], fd, qNum);
}

function downloadAll() {
  showActionBar();
  var fd = getFormData();
  var value = Number(fd.value) || 800000;
  var prems = calcPremiums(value);
  var qNum = fd.quoteNum || genQuoteNum('ALL');
  var firstKey = Object.keys(RATES)[0];
  if (firstKey) buildPDFPreview(firstKey, value, prems[firstKey], fd, qNum);
}

function actionDownload() {
  window.print();
}

function actionPrint() {
  window.print();
}

function actionTogglePreview() {
  var el = document.getElementById('pdf-preview');
  var btn = document.getElementById('btn-preview');
  if (el.classList.contains('hidden')) {
    el.classList.remove('hidden');
    btn.classList.add('bg-[#0066B3]', 'text-white');
    btn.classList.remove('bg-white', 'text-[#0066B3]', 'border-[#0066B3]');
  } else {
    el.classList.add('hidden');
    btn.classList.remove('bg-[#0066B3]', 'text-white');
    btn.classList.add('bg-white', 'text-[#0066B3]', 'border-[#0066B3]');
  }
}

window.onload = init;
