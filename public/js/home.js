 // ── COMPANIES DATA ──
const COMPANIES = {
  GA:      { name:'GA Insurance Ltd', short:'GA', rate:0.035, taxRate:0.005, color:'#003087', prefix:'GA' },
  BRITAM:  { name:'Britam General Insurance', short:'BRITAM', rate:0.041, taxRate:0.005, color:'#0066B3', prefix:'BRITAM' },
  SANLAM:  { name:'Sanlam Allianz Insurance', short:'SA', rate:0.045, taxRate:0.005, color:'#00205B', prefix:'SA' }
};

// ── QUOTE STORE (in-memory, session) ──
const quoteStore = {};
let quoteCounters = {GA:0,BRITAM:0,SA:0};
let currentIndCompany = null;
let currentIndQuoteNum = null;

// ── POPULATE YEAR SELECT ──
(function(){
  const sel = document.getElementById('f-year');
  for(let y=2026;y>=1990;y--) {
    const o=document.createElement('option');
    o.value=y;o.textContent=y;sel.appendChild(o);
  }
})();

// ── SEARCH ──
function doSearch(){ showToast('Searching for: ' + (document.getElementById('searchInput').value||'all products')); }
function setChip(el,val){ document.querySelectorAll('.chip').forEach(c=>c.classList.remove('active'));el.classList.add('active');document.getElementById('searchInput').value=val; }

// ── FORM STEPS ──
function goStep(n){
  document.querySelectorAll('.form-panel').forEach(p=>p.classList.remove('active'));
  document.querySelectorAll('.form-step').forEach(t=>t.classList.remove('active','done'));
  document.getElementById('panel-'+n).classList.add('active');
  document.getElementById('step-tab-'+n).classList.add('active');
  for(let i=1;i<n;i++) document.getElementById('step-tab-'+i).classList.add('done');
}

// ── PREMIUM PREVIEW ──
function updatePremiumPreview(){
  const v = parseFloat(document.getElementById('f-value').value)||0;
  const prev = document.getElementById('premium-preview');
  if(!v){prev.style.display='none';return;}
  prev.style.display='block';
  const fmt = n=>n.toLocaleString('en-KE',{minimumFractionDigits:0,maximumFractionDigits:0});
  document.getElementById('prev-ga').textContent='KES '+fmt(v*0.035);
  document.getElementById('prev-britam').textContent='KES '+fmt(v*0.041);
  document.getElementById('prev-sanlam').textContent='KES '+fmt(v*0.045);
}

// ── COLLECT FORM DATA ──
function getFormData(){
  return {
    salutation: document.getElementById('f-salutation').value,
    first: document.getElementById('f-first').value,
    middle: document.getElementById('f-middle').value,
    last: document.getElementById('f-last').value,
    phone: document.getElementById('f-phone').value,
    email: document.getElementById('f-email').value,
    reg: document.getElementById('f-reg').value,
    chassis: document.getElementById('f-chassis').value,
    make: document.getElementById('f-make').value,
    model: document.getElementById('f-model').value,
    body: document.getElementById('f-body').value,
    year: document.getElementById('f-year').value,
    color: document.getElementById('f-color').value,
    value: parseFloat(document.getElementById('f-value').value)||0,
    cover: document.getElementById('f-cover').value
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
//   const companies = ['GA','BRITAM','SANLAM'];
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

function openQuotePage(company) {
  // Gather all form data
  const salutation = document.getElementById('f-salutation')?.value || '';
  const firstName  = document.getElementById('f-first')?.value     || '';
  const lastName   = document.getElementById('f-last')?.value      || '';
  const phone      = document.getElementById('f-phone')?.value     || '';
  const email      = document.getElementById('f-email')?.value     || '';
  const reg        = document.getElementById('f-reg')?.value       || '';
  const chassis    = document.getElementById('f-chassis')?.value   || '';
  const makeRaw    = document.getElementById('f-make')?.value      || '';
  const makeOther  = document.getElementById('f-make-other')?.value|| '';
  const make       = makeRaw === 'Other' ? makeOther : makeRaw;
  const model      = document.getElementById('f-model')?.value     || '';
  const body       = document.getElementById('f-body')?.value      || '';
  const year       = document.getElementById('f-year')?.value      || '';
  const colorRaw   = document.getElementById('f-color')?.value     || '';
  const colorOther = document.getElementById('f-color-other')?.value|| '';
  const color      = colorRaw === 'Other' ? colorOther : colorRaw;
  // value field may exist in panel-2 or panel-3
  const valueEl    = document.getElementById('f-value');
  const value      = parseFloat(valueEl?.value) || 0;
 
  const prefix = company === 'ALL' ? 'MIC' : company;
  const qNum   = `${prefix}-${new Date().getFullYear()}/${String(Math.floor(Math.random()*9000)+1000)}`;
 
  const data = {
    salutation, firstName, lastName, phone, email,
    reg, chassis, make, model, body, year, color, value,
    quoteNum: qNum,
    mode: company
  };
 
  try { sessionStorage.setItem('micQuoteData', JSON.stringify(data)); } catch(e) {}
 
  // Navigate to quotation page
  window.location.href = `quotation.html?company=${company}`;
}

function closeQuotePage(){
  document.getElementById('quotePage').classList.remove('open');
}

// ── OPEN INDIVIDUAL QUOTE ──
function openIndividualQuote(key, qn){
  const d = getFormData();
  if(!d.first||!d.last||!d.value){
    showToast('Please complete the form first.');return;
  }
  const co = COMPANIES[key];
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
  document.getElementById('indBackBtn').onclick=()=>closeIndividual();
  window.scrollTo(0,0);
}

function openCompareAll(){
  const d = getFormData();
  if(!d.first||!d.last||!d.value){showToast('Please complete the form first.');return;}
  currentIndCompany='ALL';
  currentIndQuoteNum='COMPARE-'+new Date().getFullYear()+'/'+String(Math.floor(Math.random()*900)+100);
  document.getElementById('indQuoteNum').textContent=currentIndQuoteNum;
  renderCompareAll(d);
  document.getElementById('indQuotePage').classList.add('open');
  document.getElementById('indBackBtn').onclick=()=>closeIndividual();
  window.scrollTo(0,0);
}

function closeIndividual(){
  document.getElementById('indQuotePage').classList.remove('open');
}

// ── RENDER INDIVIDUAL QUOTATION DOC ──
function renderIndividualQuote(key,d,co,qn){
  const fmt=n=>n.toLocaleString('en-KE',{minimumFractionDigits:0,maximumFractionDigits:0});
  const premium=d.value*co.rate;
  const tax=premium*co.taxRate;
  const total=premium+tax;
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
          <div class="doc-row"><span>Stamp Duty / Taxes (0.5%)</span><span>KES ${fmt(tax)}</span></div>
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

  // Three individual quotes stacked
  ['GA','BRITAM','SANLAM'].forEach((key,i)=>{
    const co=COMPANIES[key];
    const qn=genQuoteNum(co.prefix);
    quoteStore[qn]={data:d,company:key,quoteNum:qn};
    const premium=d.value*co.rate;
    const tax=premium*co.taxRate;
    const total=premium+tax;
    html+=`
      <div class="quotation-doc" style="margin-bottom:24px">
        <div class="doc-header">
          <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px">
            <div>
              <div style="font-size:10px;opacity:.6;text-transform:uppercase;letter-spacing:.08em">${i+1} of 3 · Motor Insurance Quotation</div>
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
            <div class="doc-meta-item"><label>Taxes</label><span>KES ${fmt(tax)}</span></div>
            <div class="doc-meta-item"><label>TOTAL</label><span>KES ${fmt(total)}</span></div>
          </div>
        </div>
      </div>
    `;
  });

  // Side-by-side comparison table
  html+=`
    <div class="compare-view">
      <div class="compare-header-strip">
        <div>Details</div>
        <div>GA Insurance</div>
        <div>Britam</div>
        <div>Sanlam Allianz</div>
      </div>
  `;
  const rows=[
    ['Premium Rate','3.5%','4.1%','4.5%'],
    ['Basic Premium','KES '+fmt(d.value*0.035),'KES '+fmt(d.value*0.041),'KES '+fmt(d.value*0.045)],
    ['Taxes (0.5%)','KES '+fmt(d.value*0.035*0.005),'KES '+fmt(d.value*0.041*0.005),'KES '+fmt(d.value*0.045*0.005)],
    ['TOTAL','KES '+fmt(d.value*0.035*1.005),'KES '+fmt(d.value*0.041*1.005),'KES '+fmt(d.value*0.045*1.005)],
    ['Cover Type',d.cover,d.cover,d.cover],
    ['Region','Kenya/Uganda/TZ','Kenya/Uganda/TZ','Kenya/Uganda/TZ']
  ];
  rows.forEach((r,i)=>{
    const hi=i===3?' highlight':'';
    html+=`<div class="compare-row${hi}"><div>${r[0]}</div><div>${r[1]}</div><div>${r[2]}</div><div>${r[3]}</div></div>`;
  });
  html+=`</div>`;
  document.getElementById('indQuoteContent').innerHTML=html;
}

// ── CONFIRM & DOWNLOAD ──
function confirmAndDownload(){
  const d=getFormData();
  const co=currentIndCompany==='ALL'?COMPANIES.BRITAM:COMPANIES[currentIndCompany];
  const premium=d.value*co.rate;
  const tax=premium*co.taxRate;
  const total=premium+tax;
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
  document.getElementById('m-tax').textContent='KES '+fmt(tax);
  document.getElementById('m-total').textContent='KES '+fmt(total);
  document.getElementById('confirmModal').classList.add('open');
}

function closeModal(){
  document.getElementById('confirmModal').classList.remove('open');
}

function proceedDownload(){
  closeModal();
  showToast('Preparing your quotation for download...');
  setTimeout(()=>window.print(),800);
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
window.toggleOtherColor = function (sel) {
  const box = document.getElementById('f-color-other');
  if (sel.value === 'Other') {
    box.style.display = 'block';
    box.focus();
  } else {
    box.style.display = 'none';
    box.value = '';
  }
};


// ── PATCH getFormData() to read the "other" inputs ─────────
//    Override the existing getFormData so "Other" values
//    are substituted before anything else uses them.
//    Place this AFTER your original getFormData definition.
const _origGetFormData = window.getFormData || (function(){
  // safety shim in case script order changes
  return function(){ return {}; };
})();

window.getFormData = function () {
  const d = _origGetFormData();

  // make: if "Other" was chosen, use the free-text box
  if (d.make === 'Other') {
    const ov = (document.getElementById('f-make-other') || {}).value || '';
    d.make = ov.trim() || 'Other';
  }

  // color: same treatment
  if (d.color === 'Other') {
    const ov = (document.getElementById('f-color-other') || {}).value || '';
    d.color = ov.trim() || 'Other';
  }

  return d;
};