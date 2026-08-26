/* Dashboard Quick Quote engine — mirrors public/js/home.js + quotation.js logic */
(function () {
  var dashVehicleType = 'private';
  var makesCache = [];
  var modelsCache = {};

  var COLORS = window.MIC_QUOTE_COLORS || ['#1756c8', '#7c3aed', '#0f7a65', '#d4860e', '#c0392b'];

  function $id(id) { return document.getElementById(id); }

  function parseNum(raw) { return parseFloat(String(raw || '').replace(/,/g, '')) || 0; }

  function fmt(n) { return n.toLocaleString('en-KE', { minimumFractionDigits: 0, maximumFractionDigits: 0 }); }

  function showDashToast(msg) {
    var t = document.getElementById('dash-quote-toast');
    if (!t) {
      t = document.createElement('div');
      t.id = 'dash-quote-toast';
      t.style.cssText = 'position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:#1e293b;color:#fff;padding:10px 18px;border-radius:10px;font-size:12px;z-index:9999;box-shadow:0 8px 24px rgba(0,0,0,.2);max-width:90vw;';
      document.body.appendChild(t);
    }
    t.textContent = msg;
    t.style.opacity = '1';
    t.style.display = 'block';
    clearTimeout(t._t);
    t._t = setTimeout(function () { t.style.opacity = '0'; setTimeout(function () { t.style.display = 'none'; }, 300); }, 3500);
  }

  function dashCompanies() {
    return dashVehicleType === 'psv'
      ? (window.MIC_QUOTE_COMPANY_DATA_PSV || [])
      : (window.MIC_QUOTE_COMPANY_DATA_PRIVATE || []);
  }

  function dashCalcPremium(valuation, co, numPassengers) {
    var minP = co.minPremium || 0;
    var premium = Math.max(valuation * (co.rate || 0), minP);
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

  function currentYear() { return new Date().getFullYear(); }

  function companyEligibility(co, value, year) {
    var reasons = [];
    var min = co.minSumInsured || 0;
    var max = (dashVehicleType === 'psv' ? 10000000 : 5000000);
    if (value < min) reasons.push('Below minimum sum insured (' + fmt(min) + ')');
    if (value > max) reasons.push('Exceeds maximum valuation (' + fmt(max) + ')');
    if (year) {
      var age = currentYear() - year;
      if (age > co.maxAge) reasons.push('Vehicle age ' + age + 'y exceeds max ' + co.maxAge + 'y');
    }
    return { eligible: reasons.length === 0, reasons: reasons };
  }

  function readDashForm() {
    var value = parseNum(($id('dash-f-value') || {}).value);
    var year = parseInt(($id('dash-f-year') || {}).value || '0', 10) || 0;
    var paxEl = $id('dash-f-pax');
    var numPassengers = paxEl && dashVehicleType === 'psv' ? (parseInt(paxEl.value, 10) || 1) : 1;
    return {
      salutation: '', firstName: (($id('dash-f-first') || {}).value || '').trim(),
      lastName: (($id('dash-f-last') || {}).value || '').trim(),
      phone: (($id('dash-f-phone') || {}).value || '').trim(),
      email: (($id('dash-f-email') || {}).value || '').trim(),
      reg: (($id('dash-f-reg') || {}).value || '').trim().toUpperCase(),
      make: (($id('dash-f-make') || {}).value || '').trim(),
      model: (($id('dash-f-model') || {}).value || '').trim(),
      year: year, value: value, numPassengers: numPassengers, vehicleType: dashVehicleType,
    };
  }

  function renderDashPreview() {
    var wrap = $id('dash-premium-preview');
    var grid = $id('dash-company-cards');
    if (!wrap || !grid) return;
    var d = readDashForm();
    var companies = dashCompanies();

    if (!d.value) {
      wrap.style.display = 'none';
      grid.innerHTML = '';
      return;
    }

    var html = '';
    companies.forEach(function (co) {
      var calc = dashCalcPremium(d.value, co, d.numPassengers);
      var elig = companyEligibility(co, d.value, d.year);
      var state = elig.eligible ? '' : 'opacity:.45;filter:grayscale(.4);';
      html += '<div class="dash-co-card" data-slug="' + co.slug + '" style="border:1px solid #e5e7eb;border-top:3px solid ' + co.color + ';border-radius:12px;padding:12px;background:#fff;display:flex;flex-direction:column;gap:8px;' + state + '">';
      html += '<div style="display:flex;align-items:center;gap:8px;min-width:0">';
      if (co.logo) html += '<img src="' + co.logo + '" alt="" style="width:26px;height:26px;object-fit:contain;border-radius:4px;">';
      html += '<span style="font-size:11px;font-weight:700;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + co.name + '</span>';
      html += '</div>';
      html += '<div style="font-size:16px;font-weight:800;color:' + co.color + ';">KES ' + fmt(calc.grandTotal) + '</div>';
      html += '<div style="font-size:10px;color:#64748b;">Total annual premium</div>';
      if (elig.eligible) {
        html += '<button type="button" onclick="dashOpenQuote(\'' + co.slug + '\')" style="margin-top:auto;width:100%;padding:7px 0;border:0;border-radius:8px;background:' + co.color + ';color:#fff;font-size:11px;font-weight:700;cursor:pointer;">View Quote</button>';
      } else {
        html += '<div style="font-size:9px;color:#dc2626;line-height:1.4;">' + elig.reasons.join(' • ') + '</div>';
      }
      html += '</div>';
    });

    grid.innerHTML = html;
    wrap.style.display = 'block';
  }

  function validateDashForm() {
    var d = readDashForm();
    if (!d.firstName || !d.lastName) { showDashToast('Please enter the customer first and last name.'); return null; }
    if (!/^[A-Z]{3}\s?\d{3}[A-Z]$/.test(d.reg)) { showDashToast('Please enter a valid Kenyan registration number (e.g. KDH 291H).'); return null; }
    if (!d.year) { showDashToast('Please select the year of manufacture.'); return null; }
    if (!d.make || !d.model) { showDashToast('Please enter the vehicle make and model.'); return null; }
    if (!d.value || d.value <= 0) { showDashToast('Please enter a valid vehicle value.'); return null; }
    if (dashVehicleType === 'psv' && (!d.numPassengers || d.numPassengers < 1)) { showDashToast('Please enter the number of passengers.'); return null; }
    return d;
  }

  // ── Public API used by dashboard blade templates ──
  window.setDashVehicleType = function (type) {
    dashVehicleType = (type === 'psv') ? 'psv' : 'private';
    var priv = $id('dash-toggle-private');
    var psv = $id('dash-toggle-psv');
    if (priv) priv.className = priv.className.replace(/bg-white[^"]*|shadow-sm/g, '').replace(/dark:bg-gray-600/g, '') + (dashVehicleType === 'private' ? ' bg-white dark:bg-gray-600 text-gray-800 dark:text-white shadow-sm' : ' text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200');
    if (psv) psv.className = psv.className.replace(/bg-white[^"]*|shadow-sm/g, '').replace(/dark:bg-gray-600/g, '') + (dashVehicleType === 'psv' ? ' bg-white dark:bg-gray-600 text-gray-800 dark:text-white shadow-sm' : ' text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200');
    var paxWrap = $id('dash-f-pax-wrap');
    if (paxWrap) paxWrap.style.display = dashVehicleType === 'psv' ? '' : 'none';
    renderDashPreview();
  };

  window.dashGenerateQuote = function () {
    var d = validateDashForm();
    if (!d) return;
    renderDashPreview();
  };

  window.dashOpenQuote = function (slug) {
    var d = validateDashForm();
    if (!d) return;
    var co = (window.MIC_COMPANIES || []).find(function (c) { return c.slug === slug; });
    var prefix = co && co.prefix ? co.prefix : 'MIC';
    var qNum = prefix + '-' + currentYear() + '/' + String(Math.floor(Math.random() * 9000) + 1000);
    d.quoteNum = qNum;
    d.mode = slug;
    try { sessionStorage.setItem('micQuoteData', JSON.stringify(d)); } catch (e) {}
    var url = '/quotation/' + slug + '?vehicle_type=' + d.vehicleType + '&num_passengers=' + d.numPassengers;
    window.location.href = url;
  };

  // ── Make / model autocomplete (mirrors home.js setupSearch) ──
  function fetchMakes(cb) {
    if (makesCache.length) { cb(makesCache); return; }
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/api/vehicle-makes', true);
    xhr.onload = function () {
      if (xhr.status === 200) {
        try { makesCache = JSON.parse(xhr.responseText); } catch (e) { makesCache = []; }
        cb(makesCache);
      }
    };
    xhr.send();
  }

  function fetchModels(make, cb) {
    if (modelsCache[make]) { cb(modelsCache[make]); return; }
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/api/vehicle-models/' + encodeURIComponent(make), true);
    xhr.onload = function () {
      if (xhr.status === 200) {
        try { modelsCache[make] = JSON.parse(xhr.responseText); } catch (e) { modelsCache[make] = []; }
        cb(modelsCache[make]);
      }
    };
    xhr.send();
  }

  function setupDashSearch(inputId, dropdownId, items, onSelect) {
    var input = $id(inputId);
    var dropdown = $id(dropdownId);
    if (!input || !dropdown) return;
    var current = items;

    input.addEventListener('input', function () {
      var val = this.value.toLowerCase().trim();
      if (!val) { dropdown.innerHTML = ''; dropdown.classList.add('hidden'); return; }
      var filtered = current.filter(function (i) { return String(i).toLowerCase().indexOf(val) > -1; });
      dropdown.innerHTML = filtered.map(function (i) {
        return '<div class="dash-vehicle-option" style="padding:8px 12px;cursor:pointer;font-size:11px;border-bottom:1px solid #f1f5f9;" data-value="' + String(i).replace(/'/g, '&#39;').replace(/"/g, '&quot;') + '">' + i + '</div>';
      }).join('');
      dropdown.classList.remove('hidden');
    });

    dropdown.addEventListener('click', function (e) {
      var opt = e.target.closest('.dash-vehicle-option');
      if (!opt) return;
      input.value = opt.dataset.value;
      dropdown.classList.add('hidden');
      onSelect(opt.dataset.value);
    });

    input.addEventListener('blur', function () { setTimeout(function () { dropdown.classList.add('hidden'); }, 200); });
    input.addEventListener('focus', function () { if (this.value.trim()) this.dispatchEvent(new Event('input')); });

    return { setItems: function (newItems) { current = newItems; } };
  }

  function initDashSearch() {
    fetchMakes(function (makes) {
      var makeSearch = setupDashSearch('dash-f-make', 'dash-f-make-dropdown', makes, function (val) {
        var modelInput = $id('dash-f-model');
        var modelDropdown = $id('dash-f-model-dropdown');
        if (modelInput) { modelInput.value = ''; modelInput.placeholder = 'Search model...'; }
        if (modelDropdown) modelDropdown.innerHTML = '';
        fetchModels(val, function (models) {
          setupDashSearch('dash-f-model', 'dash-f-model-dropdown', models, function () {});
        });
      });
      if (makeSearch) window.__dashMakeSearch = makeSearch;
    });
  }

  function bindDashEvents() {
    ['dash-f-value', 'dash-f-year', 'dash-f-pax'].forEach(function (id) {
      var el = $id(id);
      if (el) {
        el.addEventListener('input', renderDashPreview);
        el.addEventListener('change', renderDashPreview);
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    initDashSearch();
    bindDashEvents();
    setDashVehicleType(dashVehicleType);
  });
})();
