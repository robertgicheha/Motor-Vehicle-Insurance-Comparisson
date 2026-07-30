 // ── COMPANIES DATA (dynamic from backend) ──
const COMPANIES = {};
let quoteCounters = {};

function refreshCompanies() {
  var companies = window.MIC_COMPANIES || [];
  Object.keys(COMPANIES).forEach(function(k) { delete COMPANIES[k]; });
  if (Array.isArray(companies)) {
    companies.forEach(function(c) {
      COMPANIES[c.slug] = c;
      COMPANIES[c.slug.toUpperCase()] = c;
    });
  }
}
refreshCompanies();

// ── QUOTE STORE (in-memory, session) ──
const quoteStore = {};
let currentIndCompany = null;
let currentIndQuoteNum = null;

// ── POPULATE YEAR SELECT ──
(function(){
  function populateYear(id) {
    var sel = document.getElementById(id);
    if (!sel) return;
    for(var y=2026;y>=1990;y--) {
      var o=document.createElement('option');
      o.value=y;o.textContent=y;sel.appendChild(o);
    }
  }
  populateYear('f-year');
  populateYear('f-year-private');
  populateYear('f-year-psv');
  populateYear('f-year-vt');
})();

// ── SEARCH ──
var currentSearchMode = 'registration'; // 'registration' | 'quotation' | 'vehicle'
var suggestTimer = null;
var activeSuggestIndex = -1;

// Kenyan reg format helper: KDX 291H (3 letters, space, 3 digits, 1 letter)
function formatRegistrationInput(raw){
  var clean = raw.toUpperCase().replace(/[^A-Z0-9]/g,'');
  if (clean.length <= 3) return clean;
  return clean.slice(0,3) + ' ' + clean.slice(3,7);
}

function setChip(el,mode){
  document.querySelectorAll('.chip').forEach(c=>c.classList.remove('active'));
  el.classList.add('active');
  currentSearchMode = mode;

  var input = document.getElementById('searchInput');
  input.value = '';        // ← clears the field, nothing gets appended
  hideSuggestions();

  if (mode === 'registration') {
    input.placeholder = 'e.g. KDX 291H';
  } else if (mode === 'quotation') {
    input.placeholder = 'Enter quotation number...';
  } else {
    input.placeholder = 'Enter vehicle number...';
  }
  input.focus();
}

function handleSearchInput(){
  var input = document.getElementById('searchInput');
  var val = input.value;

  // auto-format registration number as the user types
  if (currentSearchMode === 'registration') {
    var formatted = formatRegistrationInput(val);
    if (formatted !== val) {
      input.value = formatted;
      val = formatted;
    }
  }

  clearTimeout(suggestTimer);
  var q = val.trim();
  if (q.length < 2) { hideSuggestions(); return; }

  suggestTimer = setTimeout(function(){ fetchSuggestions(q); }, 250);
}

function fetchSuggestions(q){
  var xhr = new XMLHttpRequest();
  xhr.open('GET', '/motor-quote/suggestions?q=' + encodeURIComponent(q) + '&type=' + encodeURIComponent(currentSearchMode), true);
  xhr.onload = function(){
    if (xhr.status === 200) {
      try {
        var data = JSON.parse(xhr.responseText);
        renderSuggestions(data.suggestions || []);
      } catch(e) { hideSuggestions(); }
    } else {
      hideSuggestions();
    }
  };
  xhr.onerror = function(){ hideSuggestions(); };
  xhr.send();
}

function renderSuggestions(list){
  var box = document.getElementById('searchSuggestions');
  activeSuggestIndex = -1;

  if (!list || list.length === 0) {
    box.innerHTML = '<div class="sg-empty">No matches found.</div>';
    box.style.display = 'block';
    return;
  }

  box.innerHTML = list.map(function(item){
    var label = item.label || item.value || '';
    var meta = item.meta || '';
    return '<div class="sg-item" onclick="selectSuggestion(\'' + label.replace(/'/g,"\\'") + '\')">'
         + '<span>' + label + '</span>'
         + (meta ? '<span class="sg-meta">' + meta + '</span>' : '')
         + '</div>';
  }).join('');

  box.style.display = 'block';
}

function selectSuggestion(value){
  document.getElementById('searchInput').value = value;
  hideSuggestions();
}

function hideSuggestions(){
  var box = document.getElementById('searchSuggestions');
  if (box) { box.style.display = 'none'; box.innerHTML = ''; }
}

// close dropdown when clicking outside the search bar
document.addEventListener('click', function(e){
  var bar = document.querySelector('.search-bar');
  if (bar && !bar.contains(e.target)) hideSuggestions();
});

// keyboard navigation (optional but expected UX for suggestion lists)
document.addEventListener('keydown', function(e){
  var box = document.getElementById('searchSuggestions');
  if (!box || box.style.display === 'none') return;
  var items = box.querySelectorAll('.sg-item');
  if (!items.length) return;

  if (e.key === 'ArrowDown') {
    e.preventDefault();
    activeSuggestIndex = Math.min(activeSuggestIndex + 1, items.length - 1);
    updateActiveSuggestion(items);
  } else if (e.key === 'ArrowUp') {
    e.preventDefault();
    activeSuggestIndex = Math.max(activeSuggestIndex - 1, 0);
    updateActiveSuggestion(items);
  } else if (e.key === 'Enter') {
    if (activeSuggestIndex > -1 && items[activeSuggestIndex]) {
      e.preventDefault();
      items[activeSuggestIndex].click();
    }
  } else if (e.key === 'Escape') {
    hideSuggestions();
  }
});

function updateActiveSuggestion(items){
  items.forEach(function(it,i){
    it.classList.toggle('sg-active', i === activeSuggestIndex);
  });
}

function doSearch(){
  var q = (document.getElementById('searchInput').value||'').trim();
  if (!q) { showToast('Please enter a search term.'); return; }
  hideSuggestions();
  var xhr = new XMLHttpRequest();
  xhr.open('GET', '/motor-quote/search?q=' + encodeURIComponent(q) + '&type=' + encodeURIComponent(currentSearchMode), true);
  xhr.onload = function(){
    if (xhr.status === 200) {
      var data = JSON.parse(xhr.responseText);
      renderSearchResults(data);
    } else {
      showToast('Search failed. Please try again.');
    }
  };
  xhr.send();
}

function renderSearchResults(data){
  var section = document.getElementById('searchResults');
  var grid = document.getElementById('searchResultsGrid');
  if (!section || !grid) return;
  grid.innerHTML = '';
  var all = (data.quotations||[]).concat(data.stickers||[]);
  if (all.length === 0) {
    grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:32px 0;color:var(--text-muted);font-size:13px">No results found.</div>';
    section.style.display = 'block';
    return;
  }
  all.forEach(function(item){
    var isSticker = !!item.sticker_number;
    var num = isSticker ? item.sticker_number : item.quotation_number;
    var vehicle = (item.car_make||'') + ' ' + (item.car_model||'');
    var reg = item.car_registration || item.vehicle_registration || '';
    var name = item.customer_name || '';
    var hashedNum = num ? num.slice(0,3) + '••••' + num.slice(-4) : '••••••';
    var hashedReg = reg ? reg.slice(0,3) + '••••' : '••••••';
    var hashedName = name ? name.slice(0,1) + '••••' : '••••••';
    var card = document.createElement('div');
    card.className = 'search-result-card';
    card.innerHTML = `
      <h4>${isSticker ? 'Certificate' : 'Quotation'}</h4>
      <div class="sr-detail"><span>Number</span><span class="hashed">${hashedNum}</span></div>
      ${vehicle ? '<div class="sr-detail"><span>Vehicle</span><span class="blurred">' + vehicle + '</span></div>' : ''}
      ${reg ? '<div class="sr-detail"><span>Reg No.</span><span class="hashed">' + hashedReg + '</span></div>' : ''}
      ${name ? '<div class="sr-detail"><span>Name</span><span class="hashed">' + hashedName + '</span></div>' : ''}
      <div class="sr-actions">
        <button class="sr-btn-quote" onclick="handleSearchQuote('${num}')">Generate Quote</button>
        <button class="sr-btn-sticker" onclick="handleSearchSticker('${num}')">Generate Sticker</button>
      </div>
    `;
    grid.appendChild(card);
  });
  section.style.display = 'block';
  section.scrollIntoView({behavior:'smooth'});
}

function closeSearchResults(){
  document.getElementById('searchResults').style.display = 'none';
}

function handleSearchQuote(num){
  if (window.MIC_GUEST) {
    sessionStorage.setItem('search_redirect', '/dashboard');
    openAuthModal();
    return;
  }
  window.location.href = '/quotations?search=' + encodeURIComponent(num);
}

function handleSearchSticker(num){
  if (window.MIC_GUEST) {
    sessionStorage.setItem('search_redirect', '/dashboard');
    openAuthModal();
    return;
  }
  window.location.href = '/stickers/user-lookup?quotation=' + encodeURIComponent(num);
}

// ── FORM STEPS ──
// function goStep(n){
//   document.querySelectorAll('.form-panel').forEach(p=>p.classList.remove('active'));
//   document.querySelectorAll('.form-step').forEach(t=>t.classList.remove('active','done'));
//   document.getElementById('panel-'+n).classList.add('active');
//   document.getElementById('step-tab-'+n).classList.add('active');
//   for(let i=1;i<n;i++) document.getElementById('step-tab-'+i).classList.add('done');
// }

function goStep(type, stepNum){
  if (stepNum === undefined) { stepNum = type; type = ''; }
  for (var i = 1; i <= 3; i++){
    var suffix = type ? '-' + type : '';
    var tab = document.getElementById('step-tab-' + i + suffix);
    var panel = document.getElementById('panel-' + i + suffix);
    if (tab) tab.classList.toggle('active', i === stepNum);
    if (panel) panel.classList.toggle('active', i === stepNum);
  }
}

function guestGuard(callback) {
  if (typeof callback === 'function') {
    var match = callback.toString().match(/openQuotePage\('([^']+)'(?:,\s*'([^']*)')?\)/);
    if (match && match[1] !== 'ALL') {
      try {
        var type = match[2] !== undefined ? match[2] : (document.getElementById('f-reg-vt') ? 'vt' : (document.getElementById('f-reg-private') ? 'private' : (document.getElementById('f-reg-psv') ? 'psv' : '')));
        var d = typeof getFormData === 'function' ? getFormData(type) : {};
        sessionStorage.setItem('pending_quote_company', match[1]);
        sessionStorage.setItem('micQuoteData', JSON.stringify({
          ...d,
          firstName: d.firstName || d.first || '',
          lastName: d.lastName || d.last || '',
          mode: match[1]
        }));
      } catch(e) {}
    }
    if (window.MIC_GUEST) {
      if (match && match[1] !== 'ALL') {
        sessionStorage.setItem('auth_redirect', '/dashboard?pending_quote=1');
        var alpineEl = document.querySelector('#authModal');
        if (alpineEl && alpineEl.__x) { alpineEl.__x.$data.redirectTo = '/dashboard?pending_quote=1'; }
        openAuthModal();
        return;
      }
      callback();
      return;
    }
    // Logged-in user
    if (match && match[1] !== 'ALL') {
      window.location.href = '/dashboard?pending_quote=1';
      return;
    }
  }
  if (typeof callback === 'function') callback();
}

// ── PREMIUM PREVIEW ──
function updatePremiumPreview(type){
  type = type || '';
  var s = type ? '-' + type : '';
  var raw = (document.getElementById('f-value' + s).value || '').replace(/,/g, '');
  var v = parseFloat(raw) || 0;
  var prev = document.getElementById('premium-preview' + s);
  var prevEmpty = document.getElementById('premium-preview-empty' + s);
  var grid = document.getElementById('premium-cards-grid' + s);
  if (!v) { if (prev) prev.style.display = 'none'; if (prevEmpty) prevEmpty.style.display = 'block'; return; }
  if (prevEmpty) prevEmpty.style.display = 'none';
  if (prev) prev.style.display = 'block';

  if (!grid) return;

  var minHint = document.getElementById('f-min-sum-hint' + s);
  if (minHint) {
    var vt = window.MIC_VEHICLE_TYPE || 'private';
    minHint.textContent = 'Minimum sum insured: ' + (vt === 'psv' ? 'KES 500,000' : 'KES 500,000');
  }

  var companies = type === 'psv' ? (window.MIC_COMPANIES_PSV || []) : type === 'private' ? (window.MIC_COMPANIES_PRIVATE || []) : (window.MIC_COMPANIES || []);
  if (!companies.length) return;
  
  var paxEl = document.getElementById('f-pax' + s);
  var numPassengers = paxEl ? parseInt(paxEl.value) || 1 : 1;

  // Build cards dynamically
  var html = '';
  companies.forEach(function(cd){
    var calc = calcPremium(v, cd, numPassengers);
    html += '<div class="preview-card" onclick="guestGuard(function(){ openQuotePage(\'' + cd.slug + '\',\'' + type + '\'); })" style="background:white;border:1.5px solid var(--border);border-radius:var(--radius);padding:16px 14px;text-align:center;cursor:pointer;transition:box-shadow .2s,transform .15s">';
    html += '<div style="height:36px;display:flex;align-items:center;justify-content:center;margin-bottom:8px">';
    if (cd.logo) {
      html += '<img src="' + cd.logo + '" alt="' + cd.name + '" style="max-height:32px;max-width:90px;object-fit:contain" onerror="fallbackLogo(this,\'' + cd.slug + '\')">';
    }
    html += '<span style="' + (cd.logo ? 'display:none' : '') + ';font-size:11px;font-weight:800;color:' + cd.color + '">' + cd.name.toUpperCase() + '</span>';
    html += '</div>';
    html += '<div style="font-size:11px;color:' + cd.color + ';font-weight:700;margin-bottom:6px">' + cd.name.toUpperCase() + '</div>';
    html += '<div style="font-size:1.1rem;font-weight:700;color:' + cd.color + '" id="prev-' + cd.slug + '">KES ' + Number(calc.grandTotal).toLocaleString('en-KE', {minimumFractionDigits:2, maximumFractionDigits:2}) + '</div>';
    html += '<div style="font-size:11px;color:var(--text-muted);margin-bottom:10px">@ ' + (cd.rate * 100) + '%</div>';
    html += '<div style="font-size:11px;font-weight:700;color:white;background:' + cd.color + ';border-radius:5px;padding:5px 0">View Quote ↗</div>';
    html += '</div>';
  });

  // Compare All card
  var compareColor = '#D0021B';
  html += '<div class="preview-card" onclick="guestGuard(function(){ openQuotePage(\'ALL\',\'' + type + '\'); })" style="background:white;border:1.5px solid ' + compareColor + ';border-radius:var(--radius);padding:16px 14px;text-align:center;cursor:pointer;transition:box-shadow .2s,transform .15s">';
  html += '<div style="height:36px;display:flex;align-items:center;justify-content:center;margin-bottom:8px">';
  html += '<span style="font-size:18px">📊</span></div>';
  html += '<div style="font-size:11px;color:' + compareColor + ';font-weight:700;margin-bottom:6px">COMPARE ALL</div>';
  html += '<div style="font-size:1.1rem;font-weight:700;color:' + compareColor + '">' + companies.length + ' Quotes</div>';
  html += '<div style="font-size:11px;color:var(--text-muted);margin-bottom:10px">All insurers</div>';
  html += '<div style="font-size:11px;font-weight:700;color:white;background:' + compareColor + ';border-radius:5px;padding:5px 0">Compare ↗</div>';
  html += '</div>';

  grid.innerHTML = html;
}

// ── COLLECT FORM DATA ──
function getFormData(type){
  type = type || '';
  var s = type ? '-' + type : '';
  var makeHidden = (document.getElementById('f-make-value' + s) || {}).value || '';
  var makeEl = document.getElementById('f-make' + s);
  var make = makeHidden || (makeEl ? makeEl.value : '');
  var modelHidden = (document.getElementById('f-model-value' + s) || {}).value || '';
  var modelEl = document.getElementById('f-model' + s);
  var model = modelHidden || (modelEl ? modelEl.value : '');
  var valueEl = document.getElementById('f-value' + s);
  var rawValue = (valueEl ? valueEl.value : '').replace(/,/g, '');
  return {
    salutation: (document.getElementById('f-salutation' + s) || {}).value || '',
    first: (document.getElementById('f-first' + s) || {}).value || '',
    middle: (document.getElementById('f-middle' + s) || {}).value || '',
    last: (document.getElementById('f-last' + s) || {}).value || '',
    phone: (document.getElementById('f-phone' + s) || {}).value || '',
    email: (document.getElementById('f-email' + s) || {}).value || '',
    reg: (document.getElementById('f-reg' + s) || {}).value || '',
    chassis: (document.getElementById('f-chassis' + s) || {}).value || '',
    make: make,
    model: model,
    body: (document.getElementById('f-body' + s) || {}).value || '',
    year: (document.getElementById('f-year' + s) || {}).value || '',
    color: (document.getElementById('f-color' + s) || {}).value || '',
    value: parseFloat(rawValue) || 0,
    vehicleType: (document.getElementById('f-vehicle-type' + s) || {}).value || 'psv',
    numPassengers: parseInt((document.getElementById('f-pax' + s) || {}).value) || 1,
    cover: (document.getElementById('f-cover' + s) || {}).value || ''
  };
}

function fullName(d){ return [d.salutation,d.first,d.middle,d.last].filter(Boolean).join(' '); }

// ── GENERATE QUOTE NUMBER ──
function genQuoteNum(prefix){
  const year = new Date().getFullYear();
  quoteCounters[prefix] = (quoteCounters[prefix]||0)+1;
  const num = String(quoteCounters[prefix]).padStart(3,'0');
  return prefix+'-'+year+'/'+num;
}

// ── OPEN MAIN QUOTE PAGE ──
// function openQuotePage(){
//   const d = getFormData();
//   if(!d.first||!d.last||!d.value){
//     showToast('Please complete all required fields first.');return;
//   }
//   const fmt = n=>n.toLocaleString('en-KE',{minimumFractionDigits:0});
//   document.getElementById('sq-name').textContent=fullName(d);
//   document.getElementById('sq-phone').textContent=d.phone||'—';
//   document.getElementById('sq-email').textContent=d.email||'—';
//   document.getElementById('sq-reg').textContent=d.reg||'—';
//   document.getElementById('sq-make').textContent=(d.make+' '+d.model).trim()||'—';
//   document.getElementById('sq-year').textContent=(d.year+(d.color?' / '+d.color:''))||'—';
//   document.getElementById('sq-value').textContent='KES '+fmt(d.value);

//   // A combined quote number for the summary page
//   const num = 'ALL-'+new Date().getFullYear()+'/'+String(Math.floor(Math.random()*900)+100);
//   document.getElementById('quoteNumDisplay').textContent=num;

//   // Build company cards on the quote page
//   const cards = document.getElementById('qcCards');
//   cards.innerHTML='';

//   companies.forEach(key=>{
//     const co = COMPANIES[key];
//     const premium = d.value*co.rate;
//     const tax = premium*co.taxRate;
//     const total = premium+tax;
//     const qn = genQuoteNum(co.prefix);
//     quoteStore[qn]={data:d,company:key,quoteNum:qn};
//     const card = document.createElement('div');
//     card.className='qc-card';
//     card.innerHTML=`
//       <div style="font-size:11px;font-weight:800;color:${co.color};letter-spacing:.05em;margin-bottom:8px">${co.name.toUpperCase()}</div>
//       <div class="premium-amount" style="color:${co.color}">KES ${total.toLocaleString('en-KE',{maximumFractionDigits:0})}</div>
//       <div class="premium-total">Total incl. taxes</div>
//       <div style="font-size:11.5px;color:var(--text-muted);margin:8px 0 14px">Rate: ${(co.rate*100).toFixed(1)}% + 0.5% tax</div>
//       <button class="btn btn-primary" style="width:100%;justify-content:center;font-size:13px" onclick="openIndividualQuote('${key}','${qn}')" >Download Quote ↗</button>
//     `;
//     cards.appendChild(card);
//   });

//   // Compare All card
//   const allCard=document.createElement('div');
//   allCard.className='qc-card compare-all';
//   allCard.innerHTML=`
//     <div style="font-size:11px;font-weight:800;letter-spacing:.05em;margin-bottom:8px;opacity:.8">COMPARE ALL</div>
//     <div style="font-size:1.1rem;font-weight:700;margin:8px 0 4px">All 3 Insurers</div>
//     <div style="font-size:12px;opacity:.75;margin-bottom:16px">Side-by-side comparison</div>
//     <button class="btn" style="width:100%;justify-content:center;font-size:13px;background:rgba(255,255,255,.2);color:white;border:1.5px solid rgba(255,255,255,.5)" onclick="openCompareAll()">Download All Quotes ↗</button>
//   `;
//   cards.appendChild(allCard);

//   document.getElementById('quotePage').classList.add('open');
//   window.scrollTo(0,0);
// }

function openQuotePage(company, type) {
  if (!type) {
    type = document.getElementById('f-reg-vt') ? 'vt' : (document.getElementById('f-reg-private') ? 'private' : (document.getElementById('f-reg-psv') ? 'psv' : ''));
  }
  var s = type ? '-' + type : '';
  var salutation = (document.getElementById('f-salutation' + s) || {}).value || '';
  var firstName  = (document.getElementById('f-first' + s) || {}).value     || '';
  var lastName   = (document.getElementById('f-last' + s) || {}).value      || '';
  var phone      = (document.getElementById('f-phone' + s) || {}).value     || '';
  var email      = (document.getElementById('f-email' + s) || {}).value     || '';
  var reg        = (document.getElementById('f-reg' + s) || {}).value       || '';
  var chassis    = (document.getElementById('f-chassis' + s) || {}).value   || '';
  var makeHidden = (document.getElementById('f-make-value' + s) || {}).value || '';
  var makeOther  = (document.getElementById('f-make-other' + s) || {}).value || '';
  var makeInput  = (document.getElementById('f-make' + s) || {}).value      || '';
  var make       = makeHidden === 'Other' ? makeOther : (makeHidden || makeInput);
  var modelHidden = (document.getElementById('f-model-value' + s) || {}).value || '';
  var modelOther  = (document.getElementById('f-model-other' + s) || {}).value || '';
  var modelInput  = (document.getElementById('f-model' + s) || {}).value      || '';
  var model       = modelHidden === 'Other' ? modelOther : (modelHidden || modelInput);
  var body       = (document.getElementById('f-body' + s) || {}).value      || '';
  var year       = (document.getElementById('f-year' + s) || {}).value      || '';
  var colorRaw   = (document.getElementById('f-color' + s) || {}).value     || '';
  var colorOther = (document.getElementById('f-color-other' + s) || {}).value|| '';
  var color      = colorRaw === 'Other' ? colorOther : colorRaw;
  var valueEl    = document.getElementById('f-value' + s);
  var value      = parseFloat((valueEl ? valueEl.value : '').replace(/,/g, '')) || 0;
  var paxEl      = document.getElementById('f-pax' + s);
  var numPassengers = paxEl ? parseInt(paxEl.value) || 1 : 1;

  var prefix = company === 'ALL' ? 'MIC' : company;
  var qNum   = prefix + '-' + new Date().getFullYear() + '/' + String(Math.floor(Math.random()*9000)+1000);

  var vt = window.MIC_VEHICLE_TYPE || 'psv';

  var data = {
    salutation: salutation, firstName: firstName, lastName: lastName,
    phone: phone, email: email, reg: reg, chassis: chassis,
    make: make, model: model, body: body, year: year, color: color,
    value: value, quoteNum: qNum, mode: company, vehicleType: vt,
    numPassengers: numPassengers
  };

  try { sessionStorage.setItem('micQuoteData', JSON.stringify(data)); } catch(e) {}

  var url = '/quotation/' + company + '?vehicle_type=' + vt + '&num_passengers=' + numPassengers;
  window.location.href = url;
}

function closeQuotePage(){
  document.getElementById('quotePage').classList.remove('open');
}

// ── OPEN INDIVIDUAL QUOTE ──
function openIndividualQuote(key, qn){
  guestGuard(function(){
    var d = getFormData();
    if(!d.first||!d.last||!d.value){
      showToast('Please complete the form first.');return;
    }
    var co = COMPANIES[key];
    currentIndCompany=key;
    if(qn){
      currentIndQuoteNum=qn;
    } else {
      currentIndQuoteNum = genQuoteNum(co.prefix);
      quoteStore[currentIndQuoteNum]={data:d,company:key,quoteNum:currentIndQuoteNum};
    }
    document.getElementById('indQuoteNum').textContent=currentIndQuoteNum;
    renderIndividualQuote(key,d,co,currentIndQuoteNum);
    document.getElementById('indQuotePage').classList.add('open');
    document.getElementById('indBackBtn').onclick=function(){ closeIndividual(); };
    window.scrollTo(0,0);
  });
}

function openCompareAll(){
  guestGuard(function(){
    var d = getFormData();
    if(!d.first||!d.last||!d.value){showToast('Please complete the form first.');return;}
    currentIndCompany='ALL';
    currentIndQuoteNum='COMPARE-'+new Date().getFullYear()+'/'+String(Math.floor(Math.random()*900)+100);
    document.getElementById('indQuoteNum').textContent=currentIndQuoteNum;
    renderCompareAll(d);
    document.getElementById('indQuotePage').classList.add('open');
    document.getElementById('indBackBtn').onclick=function(){ closeIndividual(); };
    window.scrollTo(0,0);
  });
}

function closeIndividual(){
  document.getElementById('indQuotePage').classList.remove('open');
}

// ── PREMIUM CALCULATION ENGINE ──
function calcPremium(valuation, co, numPassengers) {
  var minP = co.minPremium || 0;
  var premium = Math.max(valuation * co.rate, minP);
  var pvt = 0;
  if (!co.isPvtInclusive) {
    pvt = Math.max(valuation * (co.pvtRate || 0), co.pvtMinValue || 0);
  }
  var pll = (co.pllPerPerson || 0) * (numPassengers || 1);
  var totalBeforeLevy = premium + pvt + pll;
  var iraLevy = totalBeforeLevy * 0.0045;
  var stampDuty = 40;
  var grandTotal = totalBeforeLevy + iraLevy + stampDuty;
  return { premium: premium, pvt: pvt, pll: pll, totalBeforeLevy: totalBeforeLevy, iraLevy: iraLevy, stampDuty: stampDuty, grandTotal: grandTotal };
}

// ── RENDER INDIVIDUAL QUOTATION DOC ──
function renderIndividualQuote(key,d,co,qn){
  const fmt=n=>n.toLocaleString('en-KE',{minimumFractionDigits:0,maximumFractionDigits:0});
  var calc = calcPremium(d.value, co, d.numPassengers);
  var premium = calc.premium, pvt = calc.pvt, pll = calc.pll;
  var levis = calc.iraLevy, stampDuty = calc.stampDuty, total = calc.grandTotal;
  const today=new Date().toLocaleDateString('en-GB',{day:'2-digit',month:'long',year:'numeric'});
  document.getElementById('indQuoteContent').innerHTML=`
    <div class="quotation-doc">
      <div class="doc-header">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px">
          <div>
            <div style="font-size:11px;opacity:.7;text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px">Motor Insurance Quotation</div>
            <h2>${co.name}</h2>
            <p>Comprehensive Motor Insurance · Kenya</p>
          </div>
          <div style="text-align:right">
            <div style="font-size:10px;opacity:.6">QUOTATION No.</div>
            <div style="font-size:1.1rem;font-weight:800;letter-spacing:.03em">${qn}</div>
          </div>
        </div>
        <div class="doc-meta">
          <div class="doc-meta-item"><label>Date Issued</label><span>${today}</span></div>
          <div class="doc-meta-item"><label>Valid Until</label><span>15 days</span></div>
          <div class="doc-meta-item"><label>Cover Type</label><span>${d.cover}</span></div>
          <div class="doc-meta-item"><label>Region</label><span>Kenya/Uganda/TZ</span></div>
        </div>
      </div>
      <div class="doc-body">
        <div class="doc-section">
          <h3>Insured Details</h3>
          <div class="doc-row"><span>Full Name</span><span>${fullName(d)}</span></div>
          <div class="doc-row"><span>Phone Number</span><span>${d.phone||'—'}</span></div>
          <div class="doc-row"><span>Email Address</span><span>${d.email||'—'}</span></div>
        </div>
        <div class="doc-section">
          <h3>Vehicle Details</h3>
          <div class="doc-row"><span>Registration Number</span><span>${d.reg||'—'}</span></div>
          <div class="doc-row"><span>Chassis / Frame No.</span><span>${d.chassis||'—'}</span></div>
          <div class="doc-row"><span>Make</span><span>${d.make||'—'}</span></div>
          <div class="doc-row"><span>Model</span><span>${d.model||'—'}</span></div>
          <div class="doc-row"><span>Body Type</span><span>${d.body||'—'}</span></div>
          <div class="doc-row"><span>Year of Manufacture</span><span>${d.year||'—'}</span></div>
          <div class="doc-row"><span>Color</span><span>${d.color||'—'}</span></div>
          <div class="doc-row"><span>Estimated Value</span><span>KES ${fmt(d.value)}</span></div>
        </div>
        <div class="doc-section">
          <h3>Premium Schedule</h3>
          <div class="doc-row"><span>Basic Annual Premium (${(co.rate*100).toFixed(1)}% of KES ${fmt(d.value)})</span><span>KES ${fmt(premium)}</span></div>
          <div class="doc-row"><span>PVT Premium (${((co.pvtRate||0)*100).toFixed(2)}%)</span><span>KES ${fmt(pvt)}</span></div>
          <div class="doc-row"><span>PLL Premium (KES ${pllPerPerson} × ${d.numPassengers || 1} pax)</span><span>KES ${fmt(pll)}</span></div>
          <div class="doc-row"><span>Levis (0.45% of Basic + PVT + PLL)</span><span>KES ${fmt(levis)}</span></div>
          <div class="doc-row"><span>Stamp Duty</span><span>KES ${fmt(stampDuty)}</span></div>
          <div class="doc-row" style="font-weight:600"><span>TOTAL ANNUAL PREMIUM</span><span style="color:${co.color}">KES ${fmt(total)}</span></div>
        </div>
        <div class="doc-section">
          <h3>Excess / Deductibles</h3>
          <div class="doc-row"><span>Accidental Damage</span><span>2.5% of value, min. KES 30,000</span></div>
          <div class="doc-row"><span>Theft (with tracker)</span><span>5%, min. KES 30,000</span></div>
          <div class="doc-row"><span>Third Party Property Damage</span><span>KES 10,000</span></div>
          <div class="doc-row"><span>Medical Limit</span><span>KES 30,000</span></div>
          <div class="doc-row"><span>T.P. Property Damage Limit</span><span>KES 5,000,000</span></div>
        </div>
        <div style="font-size:12px;color:var(--text-muted);border-top:1px solid var(--border);padding-top:14px">
          This quotation is an invitation to treat. Subject to satisfactory vehicle valuation, sanction limitation and exclusion clauses, and standard policy terms and conditions. Political violence and terrorism covered upon request with additional premium.
        </div>
      </div>
      <div class="doc-total">
        <div><div class="label">Total Annual Premium (incl. taxes)</div><div style="font-size:12px;opacity:.7;margin-top:2px">${co.name} · ${qn}</div></div>
        <div class="amount">KES ${fmt(total)}</div>
      </div>
    </div>
  `;
}

// ── RENDER COMPARE ALL ──
function renderCompareAll(d){
  const fmt=n=>n.toLocaleString('en-KE',{minimumFractionDigits:0,maximumFractionDigits:0});
  const today=new Date().toLocaleDateString('en-GB',{day:'2-digit',month:'long',year:'numeric'});
  let html=`
    <div style="background:var(--white);border:1.5px solid var(--border);border-radius:var(--radius-xl);overflow:hidden;margin-bottom:28px">
      <div style="background:linear-gradient(135deg,#003D7A,#0066B3);padding:24px 28px;color:white">
        <div style="font-size:11px;opacity:.7;text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px">Motor Insurance — Comparative Quotation</div>
        <div style="font-size:1.3rem;font-weight:700">All Three Insurers · Side-by-Side</div>
        <div style="opacity:.75;font-size:13px;margin-top:4px">Prepared for ${fullName(d)} · ${today}</div>
      </div>
    </div>
  `;

  const companies = window.MIC_COMPANIES || [];
  const totalCos = companies.length;

  // Three individual quotes stacked
  companies.forEach(function(co, i){
    const key = co.slug;
    const qn=genQuoteNum(co.prefix);
    quoteStore[qn]={data:d,company:key,quoteNum:qn};
    var calc = calcPremium(d.value, co, d.numPassengers);
    var premium = calc.premium, pvt = calc.pvt, pll = calc.pll;
    var levis = calc.iraLevy, stampDuty = calc.stampDuty, total = calc.grandTotal;
    html+=`
      <div class="quotation-doc" style="margin-bottom:24px">
        <div class="doc-header">
          <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px">
            <div>
              <div style="font-size:10px;opacity:.6;text-transform:uppercase;letter-spacing:.08em">${i+1} of ${totalCos} · Motor Insurance Quotation</div>
              <h2 style="font-size:1.2rem">${co.name}</h2>
            </div>
            <div style="text-align:right">
              <div style="font-size:10px;opacity:.6">QUOTATION No.</div>
              <div style="font-size:1rem;font-weight:800">${qn}</div>
            </div>
          </div>
          <div class="doc-meta">
            <div class="doc-meta-item"><label>Premium Rate</label><span>${(co.rate*100).toFixed(1)}%</span></div>
            <div class="doc-meta-item"><label>Basic Premium</label><span>KES ${fmt(premium)}</span></div>
            <div class="doc-meta-item"><label>PVT Premium</label><span>KES ${fmt(pvt)}</span></div>
            <div class="doc-meta-item"><label>TOTAL</label><span>KES ${fmt(total)}</span></div>
          </div>
        </div>
      </div>
    `;
  });

  // Side-by-side comparison table
  let headerCols = '<div>Details</div>';
  companies.forEach(function(co){ headerCols += '<div>'+co.name+'</div>'; });
  html+=`
    <div class="compare-view">
      <div class="compare-header-strip">
        ${headerCols}
      </div>
  `;
  const rows=[];
  // Basic rows
  let rateRow = ['Premium Rate'];
  let basicRow = ['Basic Premium'];
  let pvtRow = ['PVT Premium'];
  let pllRow = ['PLL Premium'];
  let levisRow = ['Levis (0.45%)'];
  let stampRow = ['Stamp Duty'];
  let totalRow = ['TOTAL'];
  let coverRow = ['Cover Type'];
  let regionRow = ['Region'];
  companies.forEach(function(co){
    var calc = calcPremium(d.value, co, d.numPassengers);
    rateRow.push((co.rate*100).toFixed(1)+'%');
    basicRow.push('KES '+fmt(calc.premium));
    pvtRow.push('KES '+fmt(calc.pvt));
    pllRow.push('KES '+fmt(calc.pll));
    levisRow.push('KES '+fmt(calc.iraLevy));
    stampRow.push('KES '+fmt(calc.stampDuty));
    totalRow.push('KES '+fmt(calc.grandTotal));
    coverRow.push(d.cover);
    regionRow.push('Kenya/Uganda/TZ');
  });
  rows.push(rateRow, basicRow, pvtRow, pllRow, levisRow, stampRow, totalRow, coverRow, regionRow);
  rows.forEach(function(r, i){
    const hi=i===3?' highlight':'';
    let rowHtml = '<div class="compare-row'+hi+'">';
    r.forEach(function(cell){ rowHtml += '<div>'+cell+'</div>'; });
    rowHtml += '</div>';
    html+=rowHtml;
  });
  html+=`</div>`;
  document.getElementById('indQuoteContent').innerHTML=html;
}

// ── CONFIRM & DOWNLOAD ──
function confirmAndDownload(){
  guestGuard(function(){
    var d=getFormData();
    var companiesArr = window.MIC_COMPANIES || [];
    var co=currentIndCompany==='ALL'||currentIndCompany==='COMPARE'?(companiesArr[0]||{}):COMPANIES[currentIndCompany];
    if(!co||!co.rate){ showToast('No company selected.'); return; }
    var calc = calcPremium(d.value, co, d.numPassengers);
    var premium = calc.premium, pvt = calc.pvt, pll = calc.pll;
    var levis = calc.iraLevy, stampDuty = calc.stampDuty, total = calc.grandTotal;
  const fmt=n=>n.toLocaleString('en-KE',{minimumFractionDigits:0,maximumFractionDigits:0});
  document.getElementById('m-name').textContent=fullName(d);
  document.getElementById('m-phone').textContent=d.phone||'—';
  document.getElementById('m-email').textContent=d.email||'—';
  document.getElementById('m-reg').textContent=d.reg||'—';
  document.getElementById('m-make').textContent=(d.make+' '+d.model).trim()||'—';
  document.getElementById('m-year').textContent=(d.year+(d.color?' / '+d.color:''))||'—';
  document.getElementById('m-value').textContent='KES '+fmt(d.value);
  document.getElementById('m-body').textContent=d.body||'—';
  document.getElementById('m-chassis').textContent=d.chassis||'—';
  document.getElementById('m-insurer').textContent=co.name;
  document.getElementById('m-rate').textContent=(co.rate*100).toFixed(1)+'%';
  document.getElementById('m-premium').textContent='KES '+fmt(premium);
  document.getElementById('m-pvt').textContent='KES '+fmt(pvt);
  document.getElementById('m-pll').textContent='KES '+fmt(pll);
  document.getElementById('m-levis').textContent='KES '+fmt(levis);
  document.getElementById('m-stamp').textContent='KES '+fmt(stampDuty);
  document.getElementById('m-total').textContent='KES '+fmt(total);
  document.getElementById('confirmModal').classList.add('open');
  });
}

function closeModal(){
  document.getElementById('confirmModal').classList.remove('open');
}

// ── AUTH MODAL ──
function openAuthModal(tab){
  if (!sessionStorage.getItem('auth_redirect')) {
    sessionStorage.setItem('auth_redirect', window.location.href);
  }
  document.getElementById('authModal').style.display = 'flex';
  document.body.style.overflow = 'hidden';
  if(tab && window.Alpine){
    const alps = document.querySelector('[x-data]');
    if(alps) alps.__x.$data.tab = tab;
  }
}

function closeAuthModal(){
  document.getElementById('authModal').style.display = 'none';
  document.body.style.overflow = '';
}

function proceedDownload(){
  guestGuard(function(){
    closeModal();
    showToast('Preparing your quotation for download...');
    setTimeout(function(){ window.print(); },800);
  });
}

// ── COPY QUOTE NUM ──
function copyQuoteNum(){
  const n=document.getElementById('quoteNumDisplay').textContent;
  navigator.clipboard.writeText(n).catch(()=>{
    const el=document.createElement('textarea');el.value=n;document.body.appendChild(el);el.select();document.execCommand('copy');document.body.removeChild(el);
  });
  showToast('Quotation number copied: '+n);
}

function copyIndQuoteNum(){
  const n=document.getElementById('indQuoteNum').textContent;
  navigator.clipboard.writeText(n).catch(()=>{
    const el=document.createElement('textarea');el.value=n;document.body.appendChild(el);el.select();document.execCommand('copy');document.body.removeChild(el);
  });
  showToast('Quotation number copied: '+n);
}

// ── REGENERATE QUOTE ──
function regenQuote(){
  const val=document.getElementById('regenInput').value.trim();
  if(!val){showToast('Please enter a quotation number.');return;}
  if(quoteStore[val]){
    const q=quoteStore[val];
    // pre-fill form
    const d=q.data;
    document.getElementById('f-salutation').value=d.salutation||'';
    document.getElementById('f-first').value=d.first||'';
    document.getElementById('f-middle').value=d.middle||'';
    document.getElementById('f-last').value=d.last||'';
    document.getElementById('f-phone').value=d.phone||'';
    document.getElementById('f-email').value=d.email||'';
    document.getElementById('f-reg').value=d.reg||'';
    document.getElementById('f-chassis').value=d.chassis||'';
    document.getElementById('f-make').value=d.make||'';
    document.getElementById('f-model').value=d.model||'';
    document.getElementById('f-body').value=d.body||'';
    document.getElementById('f-year').value=d.year||'';
    document.getElementById('f-color').value=d.color||'';
    document.getElementById('f-value').value=d.value||'';
    document.getElementById('regen-result').textContent='✓ Quote found! Form has been pre-filled. Scroll up to review and re-generate.';
    document.getElementById('regen-result').style.color='green';
    openIndividualQuote(q.company,val);
  } else {
    document.getElementById('regen-result').textContent='Quote number not found. Please check and try again.';
    document.getElementById('regen-result').style.color='var(--red)';
  }
}

// ── TOAST ──
function showToast(msg){
  const t=document.getElementById('toast');
  t.textContent=msg;t.classList.add('show');
  setTimeout(()=>t.classList.remove('show'),3200);
}

// ═══════════════════════════════════════════════════════════
//  Add these functions inside your existing <script> block
//  (or paste just before </script>)
// ═══════════════════════════════════════════════════════════

// ── HERO CAROUSEL ──────────────────────────────────────────
(function () {
  const INTERVAL = 5000;          // ms between auto-advances
  const slides   = document.querySelectorAll('.hero-slide');
  const dots     = document.querySelectorAll('.hero-dot');
  let   current  = 0;
  let   timer    = null;

  function show(index) {
    slides[current].classList.remove('active');
    dots[current].classList.remove('active');
    current = (index + slides.length) % slides.length;
    slides[current].classList.add('active');
    dots[current].classList.add('active');
  }

  // expose for inline onclick handlers
  window.heroGoTo = function (i) { show(i); resetTimer(); };
  window.heroStep = function (d) { show(current + d); resetTimer(); };

  function resetTimer() {
    clearInterval(timer);
    timer = setInterval(() => show(current + 1), INTERVAL);
  }

  // initialise first slide and start loop
  slides[0].classList.add('active');
  resetTimer();

  // pause on hover
  const hero = document.querySelector('.hero');
  if (hero) {
    hero.addEventListener('mouseenter', () => clearInterval(timer));
    hero.addEventListener('mouseleave', resetTimer);
  }
})();


// ── "OTHER" MAKE OF VEHICLE ────────────────────────────────
window.toggleOtherMake = function (sel) {
  const box = document.getElementById('f-make-other');
  if (sel.value === 'Other') {
    box.style.display = 'block';
    box.focus();
  } else {
    box.style.display = 'none';
    box.value = '';
  }
};


// ── "OTHER" COLOR ──────────────────────────────────────────
window.toggleOtherColor = function (type, sel) {
  var s = type ? '-' + type : '';
  const box = document.getElementById('f-color-other' + s);
  if (!box) return;
  if (sel.value === 'Other') {
    box.style.display = 'block';
    box.focus();
  } else {
    box.style.display = 'none';
    box.value = '';
  }
};

// ── PATCH getFormData() to read the "other" inputs ─────────
const _origGetFormData = window.getFormData || (function(){
  return function(){ return {}; };
})();

window.getFormData = function (type) {
  var d = _origGetFormData(type);
  var s = type ? '-' + type : '';

  if (d.make === 'Other') {
    var ov = (document.getElementById('f-make-other' + s) || {}).value || '';
    d.make = ov.trim() || 'Other';
  }

  if (d.model === 'Other') {
    var ov = (document.getElementById('f-model-other' + s) || {}).value || '';
    d.model = ov.trim() || 'Other';
  }

  if (d.color === 'Other') {
    var ov = (document.getElementById('f-color-other' + s) || {}).value || '';
    d.color = ov.trim() || 'Other';
  }

  return d;
};


// ═══════════════════════════════════════════════════════════
//  VEHICLE SEARCH / AUTOCOMPLETE
// ═══════════════════════════════════════════════════════════

(function() {
  var makesCache = [];
  var modelsCache = {};

  function fetchMakes() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/api/vehicle-makes', true);
    xhr.onload = function() {
      if (xhr.status === 200) {
        makesCache = JSON.parse(xhr.responseText);
      }
    };
    xhr.send();
  }

  function fetchModels(make, callback) {
    if (modelsCache[make]) {
      callback(modelsCache[make]);
      return;
    }
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/api/vehicle-models/' + encodeURIComponent(make), true);
    xhr.onload = function() {
      if (xhr.status === 200) {
        modelsCache[make] = JSON.parse(xhr.responseText);
        callback(modelsCache[make]);
      }
    };
    xhr.send();
  }

  function setupSearch(inputId, dropdownId, hiddenId, otherId, options) {
    var input = document.getElementById(inputId);
    var dropdown = document.getElementById(dropdownId);
    var hidden = document.getElementById(hiddenId);
    var other = document.getElementById(otherId);
    if (!input || !dropdown) return;

    var items = options.items || [];
    var onSelect = options.onSelect || function() {};
    var onOther = options.onOther || function() {};

    input.addEventListener('input', function() {
      var val = this.value.toLowerCase().trim();
      if (!val) { dropdown.innerHTML = ''; dropdown.classList.remove('open'); return; }

      var filtered = items.filter(function(item) {
        return item.toLowerCase().indexOf(val) > -1;
      });

      var html = '';
      filtered.forEach(function(item) {
        html += '<div class="vehicle-option" data-value="' + item.replace(/'/g, '&#39;') + '">' + item + '</div>';
      });
      html += '<div class="vehicle-option vehicle-option-other" data-value="__other__">+ Other</div>';
      dropdown.innerHTML = html;
      dropdown.classList.add('open');
    });

    dropdown.addEventListener('click', function(e) {
      var opt = e.target.closest('.vehicle-option');
      if (!opt) return;
      var val = opt.dataset.value;
      if (val === '__other__') {
        input.value = '';
        hidden.value = 'Other';
        if (other) { other.style.display = 'block'; other.focus(); }
        onOther();
      } else {
        input.value = val;
        hidden.value = val;
        if (other) { other.style.display = 'none'; other.value = ''; }
        onSelect(val);
      }
      dropdown.classList.remove('open');
    });

    input.addEventListener('blur', function() {
      setTimeout(function() { dropdown.classList.remove('open'); }, 200);
    });

    input.addEventListener('focus', function() {
      if (this.value.trim()) {
        this.dispatchEvent(new Event('input'));
      }
    });

    return {
      setItems: function(newItems) { items = newItems; },
      getItems: function() { return items; }
    };
  }

  // Initialize make search
  var makeSearch = null;
  var modelSearch = null;

  function initMakeSearch() {
    if (makeSearch) return;
    fetchMakes();
    makeSearch = setupSearch('f-make', 'f-make-dropdown', 'f-make-value', 'f-make-other', {
      items: makesCache,
      onSelect: function(val) {
        var modelInput = document.getElementById('f-model');
        var modelOther = document.getElementById('f-model-other');
        if (modelInput) {
          modelInput.disabled = false;
          modelInput.placeholder = 'Search model...';
          modelInput.value = '';
        }
        if (modelOther) { modelOther.style.display = 'none'; modelOther.value = ''; }
        var modelHidden = document.getElementById('f-model-value');
        if (modelHidden) modelHidden.value = '';
        fetchModels(val, function(models) {
          if (modelSearch) {
            modelSearch.setItems(models);
          }
        });
      },
      onOther: function() {
        var modelInput = document.getElementById('f-model');
        if (modelInput) {
          modelInput.disabled = false;
          modelInput.placeholder = 'Type your vehicle model...';
          modelInput.value = '';
        }
        var modelHidden = document.getElementById('f-model-value');
        if (modelHidden) modelHidden.value = '';
        var modelDropdown = document.getElementById('f-model-dropdown');
        if (modelDropdown) { modelDropdown.innerHTML = ''; modelDropdown.classList.remove('open'); }
        if (modelSearch) modelSearch.setItems([]);
      }
    });

    // Re-fetch makes once loaded and update items
    var checkMakes = setInterval(function() {
      if (makesCache.length > 0) {
        makeSearch.setItems(makesCache);
        clearInterval(checkMakes);
      }
    }, 100);
    setTimeout(function() { clearInterval(checkMakes); }, 5000);
  }

  function initModelSearch() {
    modelSearch = setupSearch('f-model', 'f-model-dropdown', 'f-model-value', 'f-model-other', {
      items: [],
      onSelect: function() {},
      onOther: function() {}
    });
  }

  if (document.getElementById('f-make')) {
    initMakeSearch();
    initModelSearch();
  }

  // ── Initialize make/model search for suffixed forms (-private, -psv, -vt) ──
  var suffixedTypes = ['private', 'psv', 'vt'];
  suffixedTypes.forEach(function(type) {
    var makeInput = document.getElementById('f-make-' + type);
    if (!makeInput) return;

    var ms = setupSearch('f-make-' + type, 'f-make-dropdown-' + type, 'f-make-value-' + type, 'f-make-other-' + type, {
      items: makesCache,
      onSelect: function(val) {
        var modelInput = document.getElementById('f-model-' + type);
        var modelOther = document.getElementById('f-model-other-' + type);
        if (modelInput) {
          modelInput.disabled = false;
          modelInput.placeholder = 'Search model...';
          modelInput.value = '';
        }
        if (modelOther) { modelOther.style.display = 'none'; modelOther.value = ''; }
        var modelHidden = document.getElementById('f-model-value-' + type);
        if (modelHidden) modelHidden.value = '';
        fetchModels(val, function(models) {
          if (msModel) msModel.setItems(models);
        });
      },
      onOther: function() {
        var modelInput = document.getElementById('f-model-' + type);
        if (modelInput) {
          modelInput.disabled = false;
          modelInput.placeholder = 'Type your vehicle model...';
          modelInput.value = '';
        }
        var modelHidden = document.getElementById('f-model-value-' + type);
        if (modelHidden) modelHidden.value = '';
        var modelDropdown = document.getElementById('f-model-dropdown-' + type);
        if (modelDropdown) { modelDropdown.innerHTML = ''; modelDropdown.classList.remove('open'); }
        if (msModel) msModel.setItems([]);
      }
    });

    var msModel = setupSearch('f-model-' + type, 'f-model-dropdown-' + type, 'f-model-value-' + type, 'f-model-other-' + type, {
      items: [],
      onSelect: function() {},
      onOther: function() {}
    });

    var checkMakes = setInterval(function() {
      if (makesCache.length > 0 && ms) {
        ms.setItems(makesCache);
        clearInterval(checkMakes);
      }
    }, 100);
    setTimeout(function() { clearInterval(checkMakes); }, 5000);
  });


  // ═══════════════════════════════════════════════════════════
  //  VEHICLE VALUE FORMATTING & VALIDATION
  // ═══════════════════════════════════════════════════════════

  var valueInput = document.getElementById('f-value');
  if (valueInput) {
    valueInput.addEventListener('input', function() {
      var cursor = this.selectionStart;
      var raw = this.value.replace(/[^0-9]/g, '');
      if (raw === '') { this.value = ''; return; }
      var formatted = parseInt(raw, 10).toLocaleString('en-KE');
      var diff = formatted.length - this.value.length;
      this.value = formatted;
      this.setSelectionRange(cursor + diff, cursor + diff);

      // Update premium preview
      updatePremiumPreview();
    });

    function getValueRange() {
      var vt = window.MIC_VEHICLE_TYPE || 'private';
      if (vt === 'psv') {
        return { min: 500000, max: 10000000, label: 'KES 500,000' };
      }
      return { min: 500000, max: 5000000, label: 'KES 500,000' };
    }

    valueInput.addEventListener('blur', function() {
      var raw = this.value.replace(/,/g, '');
      var val = parseInt(raw, 10) || 0;
      var hint = document.getElementById('f-value-hint');
      var err = document.getElementById('f-value-error');
      var minHint = document.getElementById('f-min-sum-hint');
      if (!err) return;

      var range = getValueRange();
      if (minHint) {
        minHint.textContent = 'Minimum sum insured: ' + range.label;
      }

      if (val > 0 && val < range.min) {
        err.textContent = 'Minimum vehicle value is ' + range.label;
        err.style.display = 'block';
        this.style.borderColor = '#CC0000';
        if (hint) hint.style.display = 'none';
      } else if (val > range.max) {
        err.textContent = 'Maximum vehicle value is KES ' + Number(range.max).toLocaleString('en-KE');
        err.style.display = 'block';
        this.style.borderColor = '#CC0000';
        if (hint) hint.style.display = 'none';
      } else {
        err.style.display = 'none';
        this.style.borderColor = '';
        if (hint) hint.style.display = '';
      }
    });
  }

  // ── Update toggleOtherMake for new make input ──
  var oldToggleMake = window.toggleOtherMake;
  window.toggleOtherMake = function(sel) {
    var box = document.getElementById('f-make-other');
    if (!box) { if (oldToggleMake) oldToggleMake(sel); return; }
    if (sel && sel.value === 'Other') {
      box.style.display = 'block';
      box.focus();
    } else {
      box.style.display = 'none';
      box.value = '';
    }
  };
})();

// ── INIT VALUE INPUT FOR SUFFIXED FORMS (private/psv) ──
function initValueInput(suffix) {
  var s = suffix ? '-' + suffix : '';
  var valueInput = document.getElementById('f-value' + s);
  if (!valueInput) return;

  valueInput.addEventListener('input', function() {
    var cursor = this.selectionStart;
    var raw = this.value.replace(/[^0-9]/g, '');
    if (raw === '') { this.value = ''; return; }
    var formatted = parseInt(raw, 10).toLocaleString('en-KE');
    var diff = formatted.length - this.value.length;
    this.value = formatted;
    this.setSelectionRange(cursor + diff, cursor + diff);
    updatePremiumPreview(suffix);
  });

  valueInput.addEventListener('blur', function() {
    var raw = this.value.replace(/,/g, '');
    var val = parseInt(raw, 10) || 0;
    var hint = document.getElementById('f-value-hint' + s);
    var err = document.getElementById('f-value-error' + s);
    var minHint = document.getElementById('f-min-sum-hint' + s);
    if (!err) return;

    var vt = window.MIC_VEHICLE_TYPE || 'private';
    var range = vt === 'psv' ? { min: 500000, max: 10000000 } : { min: 500000, max: 5000000 };
    var label = 'KES ' + Number(range.min).toLocaleString('en-KE');
    if (minHint) minHint.textContent = 'Minimum sum insured: ' + label;

    if (val > 0 && val < range.min) {
      err.textContent = 'Minimum vehicle value is ' + label;
      err.style.display = 'block';
      this.style.borderColor = '#CC0000';
      if (hint) hint.style.display = 'none';
    } else if (val > range.max) {
      err.textContent = 'Maximum vehicle value is KES ' + Number(range.max).toLocaleString('en-KE');
      err.style.display = 'block';
      this.style.borderColor = '#CC0000';
      if (hint) hint.style.display = 'none';
    } else {
      err.style.display = 'none';
      this.style.borderColor = '';
      if (hint) hint.style.display = '';
    }
  });
}

// ── AUTO SEARCH (fires as user types; instant on complete reg/vehicle/quotation no.) ──
(function() {
  var input = document.getElementById('searchInput');
  if (!input) return;

  // Kenyan number plate shape: 3 letters, optional space, 3 digits, 1 letter — e.g. "KDU 291H"
  // Covers Registration No and Vehicle No (same format, per your note).
  var PLATE_REGEX = /^[A-Za-z]{3}\s?\d{3}[A-Za-z]$/;

  // ASSUMPTION: quotation numbers look like "GA-2026/001" (prefix-year/seq),
  // matching the placeholder already in your lookup form. Adjust if the real format differs.
  var QUOTATION_REGEX = /^[A-Za-z]{1,4}-\d{4}\/\d+$/;

  var debounceTimer = null;
  var lastAutoSearched = '';

  function isCompletePattern(val) {
    return PLATE_REGEX.test(val) || QUOTATION_REGEX.test(val);
  }

  input.addEventListener('input', function() {
    var val = this.value.trim();
    clearTimeout(debounceTimer);

    if (!val) return;

    // Complete plate or quotation number → search immediately, no debounce.
    if (isCompletePattern(val)) {
      if (val !== lastAutoSearched) {
        lastAutoSearched = val;
        doSearch();
      }
      return;
    }

    // Anything else (partial plate, first/last name, chassis no, etc.) → debounce
    // so we're not firing a request on every single keystroke.
    if (val.length >= 3) {
      debounceTimer = setTimeout(function() {
        if (input.value.trim() === val) {
          lastAutoSearched = val;
          doSearch();
        }
      }, 450);
    }
  });
})();

// ── RETRIEVE QUOTE ──
// ASSUMPTION: current logged-in state, passed from blade once, near top of page:
// const IS_GUEST = @json(auth()->guest());
// If IS_GUEST isn't already defined globally, add that line in a <script> block in the blade.

let __lastLookupResult = null;

function lookupQuotation(event) {
  event.preventDefault();
  const val = document.getElementById('regenInput').value.trim();
  const resultEl = document.getElementById('regen-result');
  const wrap = document.getElementById('regen-results-wrap');
  const body = document.getElementById('regen-results-body');
  const loginCta = document.getElementById('regen-login-cta');

  if (!val) { showToast('Please enter a quotation number.'); return; }

  resultEl.textContent = 'Searching…';
  resultEl.style.color = 'var(--text-muted)';
  wrap.style.display = 'none';

  // ASSUMPTION: motor.lookup.quotation now returns JSON instead of a redirect/view.
  // Expected shape:
  // { found: true, quotation_number, make, model, year, chassis_no, valuation, premium, is_owner }
  // is_owner = true only if the authenticated user actually owns this quotation.
  fetch(document.getElementById('regenForm').action || '{{ route("motor.lookup.quotation") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
    },
    body: JSON.stringify({ number: val }),
  })
    .then(r => r.json())
    .then(data => {
      if (!data.found) {
        resultEl.textContent = 'Quote number not found. Please check and try again.';
        resultEl.style.color = 'var(--red)';
        return;
      }

      __lastLookupResult = data;
      resultEl.textContent = '';

      const showFull = typeof IS_GUEST !== 'undefined' ? (!IS_GUEST && data.is_owner) : !!data.is_owner;
      const cls = showFull ? '' : ' class="ql-blurred"';

      body.innerHTML = `
        <tr>
          <td>${data.quotation_number}</td>
          <td${cls}>${data.make ?? ''}</td>
          <td${cls}>${data.model ?? ''}</td>
          <td${cls}>${data.year ?? ''}</td>
          <td${cls}>${data.chassis_no ?? ''}</td>
          <td${cls}>${data.valuation ?? ''}</td>
          <td${cls}>${data.premium ?? ''}</td>
        </tr>`;

      wrap.style.display = '';
      loginCta.style.display = showFull ? 'none' : 'flex';

      if (showFull) {
        // Logged-in owner — keep existing pre-fill/regenerate behaviour.
        prefillFromLookup(data);
      }
    })
    .catch(() => {
      resultEl.textContent = 'Something went wrong. Please try again.';
      resultEl.style.color = 'var(--red)';
    });
}

function prefillFromLookup(d) {
  const setVal = (id, val) => { const el = document.getElementById(id); if (el && val != null) el.value = val; };
  setVal('f-salutation', d.salutation);
  setVal('f-first', d.first_name);
  setVal('f-middle', d.middle_name);
  setVal('f-last', d.last_name);
  setVal('f-phone', d.phone);
  setVal('f-email', d.email);
  setVal('f-reg', d.registration_number);
  setVal('f-chassis', d.chassis_no);
  setVal('f-make', d.make);
  setVal('f-model', d.model);
  setVal('f-body', d.body_type);
  setVal('f-year', d.year);
  setVal('f-color', d.color);
  setVal('f-value', d.valuation);

  document.getElementById('regen-result').textContent = '✓ Quote found! Form has been pre-filled. Scroll up to review and re-generate.';
  document.getElementById('regen-result').style.color = 'green';
}

function loginToViewQuote() {
  if (!__lastLookupResult) return;
  // ASSUMPTION: replace with your real auth modal trigger + its actual options shape.
  // ASSUMPTION: after login, user should land on the dashboard with the sidebar's
  // "View All" (nested under Quotations) nav item auto-opened. I don't have that
  // sidebar's markup, so this passes a redirect target with query params for you
  // to read on the dashboard and trigger the right nav click / active state.
  const redirectUrl = `{{ route('dashboard') }}?tab=quotations&view=all&quotation=${encodeURIComponent(__lastLookupResult.quotation_number)}#view-all`;
  window.openAuthModal({ redirect: redirectUrl });
}

// ── VALIDATION ──
function validateReg(type, input) {
  var errorEl = document.getElementById('f-reg-error-' + type);
  if (!errorEl) return;
  var val = input.value.trim().toUpperCase();
  input.value = val;
  var pattern = /^[A-Z]{3}\s?\d{3}[A-Z]$/;
  if (val && !pattern.test(val)) {
    errorEl.style.display = 'block';
  } else {
    errorEl.style.display = 'none';
  }
}

function validateStep2AndProceed(type) {
  type = type || '';
  var s = type ? '-' + type : '';
  var reg = (document.getElementById('f-reg' + s) || {}).value || '';
  var year = (document.getElementById('f-year' + s) || {}).value || '';
  var make = (document.getElementById('f-make-value' + s) || {}).value || (document.getElementById('f-make' + s) || {}).value || '';
  var model = (document.getElementById('f-model-value' + s) || {}).value || (document.getElementById('f-model' + s) || {}).value || '';
  var valueRaw = (document.getElementById('f-value' + s) || {}).value || '';
  var value = parseFloat(valueRaw.replace(/,/g, '')) || 0;
  var yearError = document.getElementById('f-year-error' + s);

  var regPattern = /^[A-Z]{3}\s?\d{3}[A-Z]$/;
  if (!regPattern.test(reg)) {
    var regError = document.getElementById('f-reg-error' + s);
    if (regError) regError.style.display = 'block';
    showToast('Please enter a valid Kenyan registration number (e.g. KDH 291H)');
    return;
  }

  if (!year) {
    if (yearError) yearError.textContent = 'Please select the year of manufacture.';
    if (yearError) yearError.style.display = 'block';
    showToast('Please select the year of manufacture.');
    return;
  }
  if (yearError) yearError.style.display = 'none';

  if (!make || !model) {
    showToast('Please enter the vehicle make and model.');
    return;
  }

  if (!value || value <= 0) {
    showToast('Please enter a valid vehicle value.');
    return;
  }

  updatePremiumPreview(type);
  goStep(type, 3);
}
// ── MAIN QUOTE FORM (un-suffixed IDs) ──
function goStepMain(n){
  for (var i = 1; i <= 3; i++){
    var tab = document.getElementById('step-tab-' + i);
    var panel = document.getElementById('panel-' + i);
    if (tab) tab.classList.toggle('active', i === n);
    if (panel) panel.classList.toggle('active', i === n);
  }
}

function validateRegMain(input){
  var errorEl = document.getElementById('f-reg-error');
  if (!errorEl) return;
  var val = input.value.trim().toUpperCase();
  input.value = val;
  var pattern = /^[A-Z]{3}\s?\d{3}[A-Z]$/;
  if (val && !pattern.test(val)) {
    errorEl.style.display = 'block';
  } else {
    errorEl.style.display = 'none';
  }
}

function validateStep2AndProceedMain(){
  var reg = (document.getElementById('f-reg') || {}).value || '';
  var year = (document.getElementById('f-year') || {}).value || '';
  var make = (document.getElementById('f-make-value') || {}).value || (document.getElementById('f-make') || {}).value || '';
  var model = (document.getElementById('f-model-value') || {}).value || (document.getElementById('f-model') || {}).value || '';
  var valueRaw = (document.getElementById('f-value') || {}).value || '';
  var value = parseFloat(valueRaw.replace(/,/g, '')) || 0;

  var regPattern = /^[A-Z]{3}\s?\d{3}[A-Z]$/;
  if (!regPattern.test(reg)) {
    var regError = document.getElementById('f-reg-error');
    if (regError) regError.style.display = 'block';
    showToast('Please enter a valid Kenyan registration number (e.g. KDH 291H)');
    return;
  }

  var yearError = document.getElementById('f-year-error');
  if (!year) {
    if (yearError) yearError.textContent = 'Please select the year of manufacture.';
    if (yearError) yearError.style.display = 'block';
    showToast('Please select the year of manufacture.');
    return;
  }
  if (yearError) yearError.style.display = 'none';

  if (!make || !model) {
    showToast('Please enter the vehicle make and model.');
    return;
  }

  if (!value || value <= 0) {
    showToast('Please enter a valid vehicle value.');
    return;
  }

  goStepMain(3);
}

// ── Quotation Flip Functionality ──
function openQuoteFlow(type){
  var source = document.getElementById(type === 'psv' ? 'psv-motor-section' : 'private-motor-section');
  var formSection = document.getElementById(type === 'psv' ? 'quote-form-section-psv' : 'quote-form-section-private');
  var stepsSection = document.getElementById(type === 'psv' ? 'psv-steps-section' : 'private-steps-section');

  // If the OTHER form is currently open, close it and restore its section first
  var otherType = type === 'psv' ? 'private' : 'psv';
  var otherSource = document.getElementById(otherType === 'psv' ? 'psv-motor-section' : 'private-motor-section');
  var otherForm = document.getElementById(otherType === 'psv' ? 'quote-form-section-psv' : 'quote-form-section-private');
  var otherSteps = document.getElementById(otherType === 'psv' ? 'psv-steps-section' : 'private-steps-section');
  if (otherForm && otherForm.style.display === 'block') {
    otherForm.style.display = 'none';
    if (otherSource) otherSource.style.display = 'block';
    if (otherSteps) otherSteps.style.display = 'block';
  }

  // reset that specific form back to step 1 each time it's opened
  if(typeof goStep === 'function') goStep(type, 1);

  source.classList.add('section-flip-out');
  if(stepsSection) stepsSection.style.display = 'none';

  setTimeout(function(){
    source.style.display = 'none';
    source.classList.remove('section-flip-out');

    formSection.style.display = 'block';
    formSection.classList.add('section-flip-in');
    formSection.scrollIntoView({behavior:'smooth', block:'start'});

    setTimeout(function(){
      formSection.classList.remove('section-flip-in');
    }, 500);
  }, 500);
}

function closeQuoteFlow(type){
  var source = document.getElementById(type === 'psv' ? 'psv-motor-section' : 'private-motor-section');
  var formSection = document.getElementById(type === 'psv' ? 'quote-form-section-psv' : 'quote-form-section-private');
  var stepsSection = document.getElementById(type === 'psv' ? 'psv-steps-section' : 'private-steps-section');

  formSection.classList.add('section-flip-out');

  setTimeout(function(){
    formSection.style.display = 'none';
    formSection.classList.remove('section-flip-out');

    source.style.display = 'block';
    if(stepsSection) stepsSection.style.display = 'block';
    source.classList.add('section-flip-in');
    source.scrollIntoView({behavior:'smooth', block:'start'});

    setTimeout(function(){
      source.classList.remove('section-flip-in');
    }, 500);
  }, 500);
}

function setVehicleType(type) {
    document.getElementById('f-vehicle-type').value = type;
    window.MIC_VEHICLE_TYPE = type;

    if (window.MIC_COMPANIES_PRIVATE && window.MIC_COMPANIES_PSV) {
      window.MIC_COMPANIES = type === 'psv' ? window.MIC_COMPANIES_PSV : window.MIC_COMPANIES_PRIVATE;
    }

    document.getElementById('toggle-private').classList.toggle('active', type === 'private');
    document.getElementById('toggle-private').setAttribute('aria-selected', type === 'private');
    document.getElementById('toggle-psv').classList.toggle('active', type === 'psv');
    document.getElementById('toggle-psv').setAttribute('aria-selected', type === 'psv');

    const paxGroup = document.getElementById('f-pax-group');
    if (paxGroup) paxGroup.style.display = (type === 'psv') ? '' : 'none';

    const title = document.getElementById('form-title');
    const subtitle = document.getElementById('form-subtitle');
    const desc = document.getElementById('form-desc');

    if (type === 'psv') {
      title.textContent = 'Get Your PSV Motor Vehicle Quote';
      subtitle.textContent = 'Complete the form below in three easy steps to receive your PSV quotation.';
      desc.textContent = 'Cover your public service vehicle with a policy that includes Passenger Legal Liability, then compare premiums from leading insurers.';
    } else {
      title.textContent = 'Get Your Motor Insurance Quote';
      subtitle.textContent = 'Complete the form below in three easy steps to receive your personalised quotation.';
      desc.textContent = 'Choose the cover type that matches your vehicle, then fill in your details to compare premiums from leading insurers.';
    }

    if (typeof updatePremiumPreview === 'function') updatePremiumPreview();
  }