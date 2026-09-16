function fmt(e) {
    return (
        "KES " +
        Number(e).toLocaleString("en-KE", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        })
    );
}
function getCompanyData() {
    return window.quotationData || [];
}
function getCompanyBySlug(e) {
    return (
        getCompanyData().find(function (t) {
            return t.slug === e;
        }) || null
    );
}
function calcPremiums(e, t, a) {
    var r = t.rate || 0,
        n = t.minPremium || 0;
    t.rateAbove2_5M &&
        e > 25e5 &&
        ((r = t.rateAbove2_5M),
        t.minPremiumAbove2_5M && (n = t.minPremiumAbove2_5M));
    var o = e * r;
    o < n && (o = n);
    var s = t.isPvtInclusive || !1,
        d = 0;
    if (!s) {
        var i = t.pvtRate || 0;
        d = e * i;
        var l = t.pvtMinValue || 0;
        d < l && (d = l);
    }
    var c = t.pllPerPerson || 0,
        u = c * (a || 1),
        b = (o + d + u) * 0.0045,
        g = 40,
        m = o + d + u + b + g;
    return { basic: o, pvt: d, pll: u, levis: b, stampDuty: g, total: m };
}
function getFormData() {
    if (window.quotationDbData) return window.quotationDbData;
    try {
        var e = sessionStorage.getItem("micQuoteData");
        if (e) {
            var t = JSON.parse(e);
            return {
                salutation: t.salutation || "",
                firstName: t.firstName || t.first || "",
                lastName: t.lastName || t.last || "",
                phone: t.phone || "",
                email: t.email || "",
                reg: t.reg || "",
                chassis: t.chassis || "",
                engine: t.engine || "",
                make: t.make || "",
                model: t.model || "",
                body: t.body || "",
                year: t.year || "",
                color: t.color || "",
                value: Number(t.value) || 0,
                quoteNum: t.quoteNum || "",
                numPassengers: Number(t.numPassengers || t.num_passengers) || 1,
                vehicleType: t.vehicleType || t.vehicle_type || "",
            };
        }
    } catch {}
    return {
        salutation: "",
        firstName: "",
        lastName: "",
        phone: "",
        email: "",
        reg: "",
        chassis: "",
        engine: "",
        make: "",
        model: "",
        body: "",
        year: "",
        color: "",
        value: 0,
        quoteNum: "",
        numPassengers: 1,
        vehicleType: "",
    };
}
function genQuoteNum(e) {
    var t = new Date(),
        a = t.getFullYear(),
        r = String(Math.floor(Math.random() * 9e3) + 1e3);
    return (e.prefix || "MIC") + "-" + a + "/" + r;
}
function showActionBar() {
    var e = document.getElementById("action-bar");
    e && e.classList.add("visible");
}
function copyQuoteNumber() {
    var e = document.getElementById("hero-quote-num"),
        t = e ? e.textContent : "";
    !t ||
        t === "\u2014" ||
        (navigator.clipboard &&
            navigator.clipboard.writeText &&
            navigator.clipboard.writeText(t).then(function () {
                var a = document.getElementById("copy-quote-btn");
                (a.classList.add("copied"),
                    (a.innerHTML =
                        '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Copied'),
                    setTimeout(function () {
                        (a.classList.remove("copied"),
                            (a.innerHTML =
                                '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> Copy'));
                    }, 2e3));
            }));
}
function showToast(e, t) {
    if (((t = t || "success"), t === "success")) {
        if (window.showToastSuccess) return window.showToastSuccess(e);
    } else if (window.showToastError) return window.showToastError(e);
    var a = document.getElementById("toast-container");
    if (a) {
        var r = document.createElement("div");
        ((r.className = "toast " + t),
            (r.textContent = e),
            a.appendChild(r),
            setTimeout(function () {
                r.remove();
            }, 3e3));
    }
}
function fallbackLogo(e, t) {
    var a = (window.hardcodedLogos || {})[t];
    if (a && e.src !== a) {
        ((e.onerror = function () {
            ((e.style.display = "none"),
                e.nextElementSibling &&
                    (e.nextElementSibling.style.display = "block"));
        }),
            (e.src = a));
        return;
    }
    ((e.style.display = "none"),
        e.nextElementSibling && (e.nextElementSibling.style.display = "block"));
}
function renderCompanyLogo(e) {
    var t = window.quotationCompanies || [],
        a = t.find(function (n) {
            return n.slug === e;
        }),
        r = document.getElementById("hero-company-logo");
    a && a.logo
        ? ((r.src = a.logo),
          r.classList.remove("hidden"),
          (r.onerror = function () {
              fallbackLogo(r, e);
          }))
        : r && (r.style.display = "none");
}
function renderCompareTable(e, t) {
    for (var a = 3, r = 0; r < a; r++) {
        var n = document.getElementById("cmp-head-" + r),
            o = document.getElementById("cmp-total-" + r);
        if (e[r]) {
            if (n) {
                var logo = e[r].logo || "";
                n.innerHTML =
                    '<div class="flex flex-col items-end gap-1">' +
                    (logo
                        ? '<img src="' + logo + '" alt="' + e[r].name + '" class="h-5 max-w-[80px] object-contain" onerror="this.style.display=\'none\'">'
                        : "") +
                    "<span>" + e[r].name + "</span></div>";
            }
            var s = calcPremiums(t, e[r], window.quotationNumPassengers);
            o && (o.textContent = fmt(s.total));
        } else
            (n && ((n.textContent = "\u2014"), (n.style.display = "none")),
                o && ((o.textContent = "\u2014"), (o.style.display = "none")));
    }
    var d = [
            {
                label: "Estimated Vehicle Value",
                vals: e.map(function () {
                    return fmt(t);
                }),
            },
            {
                label: "Premium Rate",
                vals: e.map(function (l) {
                    return (l.rate * 100).toFixed(1) + "%";
                }),
            },
            {
                label: "Basic Annual Premium",
                vals: e.map(function (l) {
                    return fmt(
                        calcPremiums(t, l, window.quotationNumPassengers).basic,
                    );
                }),
            },
            {
                label: "PVT Premium",
                vals: e.map(function (l) {
                    return l.isPvtInclusive
                        ? "Inclusive"
                        : fmt(
                              calcPremiums(t, l, window.quotationNumPassengers)
                                  .pvt,
                          );
                }),
            },
            {
                label: "PLL Premium",
                vals: e.map(function (l) {
                    return fmt(
                        calcPremiums(t, l, window.quotationNumPassengers).pll,
                    );
                }),
            },
            {
                label: "Levis & Stamp Duty",
                vals: e.map(function (l) {
                    var c = calcPremiums(t, l, window.quotationNumPassengers);
                    return fmt(c.levis + c.stampDuty);
                }),
            },
            {
                label: "Excess Protector (Own Damage)",
                vals: e.map(function () {
                    return "No Cover";
                }),
            },
            {
                label: "Excess Protector (Theft)",
                vals: e.map(function () {
                    return "No Cover";
                }),
            },
            {
                label: "Loss of Use",
                vals: e.map(function () {
                    return "Not Applicable";
                }),
            },
        ],
        i = document.getElementById("compare-rows");
    i &&
        (i.innerHTML = d
            .map(function (l) {
                for (
                    var c =
                            '<td class="py-2 text-gray-800 dark:text-white">' +
                            l.label +
                            "</td>",
                        u = 0;
                    u < a;
                    u++
                )
                    c +=
                        '<td class="py-2 text-right font-semibold text-gray-800 dark:text-white">' +
                        (l.vals[u] || "\u2014") +
                        "</td>";
                return (
                    '<tr class="border-t border-gray-100 dark:border-gray-700">' +
                    c +
                    "</tr>"
                );
            })
            .join(""));
}
function compareAllCardHtml(insurerCount) {
    var logo = window.compareAllLogoUrl || "";
    return (
        '<button type="button" onclick="compareAllPdf(\'download\')" class="company-card no-underline text-left" style="cursor:pointer;display:block;width:100%;border:2px solid #D0021B;position:relative;overflow:hidden;transition:all .25s ease;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.08);background:#fff;padding:16px">' +
        '<div style="position:absolute;top:0;left:0;right:0;height:4px;background:#D0021B"></div>' +
        '<div class="company-logo-box" style="height:48px;margin-bottom:12px;display:flex;align-items:center;justify-content:center">' +
        (logo
            ? '<img src="' + logo + '" alt="Compare All" style="max-height:40px" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'block\'">'
            : "") +
        '<span style="display:' + (logo ? "none" : "block") + ';font-size:18px">📊</span>' +
        "</div>" +
        '<div class="company-name" style="font-size:14px;font-weight:700;margin-bottom:4px;color:#1a1a2e;text-align:center">Compare All</div>' +
        '<div style="display:flex;align-items:center;justify-content:center;gap:16px;margin-bottom:12px">' +
        '<div style="text-align:center">' +
        '<div style="font-size:10px;color:#6b7280;font-weight:500">Insurers</div>' +
        '<div style="font-size:13px;font-weight:800;color:#D0021B">' + insurerCount + "</div>" +
        "</div></div>" +
        '<span class="btn btn-primary" style="display:block;width:100%;text-align:center;box-sizing:border-box;font-size:12px;padding:8px 10px;background:#D0021B;border-color:#D0021B;border-radius:10px;font-weight:700;color:#fff">Download All ↓</span>' +
        "</button>"
    );
}
function renderDownloadCards(e, t, a, r, n) {
    var o = document.getElementById("dl-cards"),
        s = getCompanyData(),
        d = s
            .map(function (i) {
                var l = calcPremiums(t, i, window.quotationNumPassengers),
                    c = i.slug === e.slug,
                    u = i.logo || "";
                return (
                    `<button type="button" onclick="downloadQuote('` +
                    i.slug +
                    `')" class="text-left rounded-2xl border bg-white dark:bg-gray-800 shadow-sm p-4 text-center transition hover:-translate-y-0.5 hover:shadow-md ` +
                    (c
                        ? "border-blue-500 ring-2 ring-blue-500/20"
                        : "border-gray-200 dark:border-gray-700") +
                    '"><div class="h-11 flex items-center justify-center mb-3">' +
                    (u
                        ? '<img src="' +
                          u +
                          '" alt="' +
                          i.name +
                          `" class="max-h-9 max-w-[110px] object-contain" onerror="fallbackLogo(this,'` +
                          i.slug +
                          `')">`
                        : "") +
                    '<span class="text-[12px] font-extrabold tracking-wider text-blue-600 dark:text-blue-400" style="display:' +
                    (u ? "none" : "block") +
                    '">' +
                    (i.prefix || i.slug) +
                    '</span></div><div class="text-xs font-bold text-gray-800 dark:text-white">' +
                    i.name +
                    '</div><div class="text-[11px] text-gray-400 mb-3">Rate: <strong>' +
                    (i.rate * 100).toFixed(1) +
                    '%</strong> of vehicle value</div><div class="text-lg font-extrabold text-blue-600 dark:text-blue-400 mb-3">' +
                    fmt(l.total) +
                    '<small class="block text-[10px] font-semibold text-gray-400">Total incl. taxes</small></div><span class="block w-full rounded-lg py-2 text-xs font-bold text-white" style="background:' +
                    (i.color || "#005F9E") +
                    '">Download ' +
                    i.name.split(" ")[0] +
                    " Quote \u2193</span></button>"
                );
            })
            .join("");
    o.innerHTML = d + compareAllCardHtml(s.length);
}
function renderSingleDownloadCard(company, value, premiums) {
    var el = document.getElementById("dl-card-single");
    if (!el) return;
    var logo = company.logo || "";
    el.innerHTML =
        '<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border-2 border-blue-500 ring-2 ring-blue-500/20 p-5 text-center max-w-sm">' +
        '<div class="h-11 flex items-center justify-center mb-3">' +
        (logo
            ? '<img src="' + logo + '" alt="' + company.name + '" class="max-h-10 max-w-[110px] object-contain" onerror="fallbackLogo(this,\'' + company.slug + '\')">'
            : "") +
        '<span class="text-xs font-extrabold tracking-wider text-blue-600 dark:text-blue-400" style="display:' + (logo ? "none" : "block") + '">' +
        (company.prefix || company.slug) +
        "</span></div>" +
        '<div class="text-xs font-bold text-gray-800 dark:text-white mb-1">' + company.name + "</div>" +
        '<div class="text-[11px] text-gray-400 mb-3.5">Rate: <strong>' + (company.rate * 100).toFixed(1) + '%</strong> of vehicle value</div>' +
        '<div class="text-lg font-extrabold text-blue-600 dark:text-blue-400 mb-3.5">' +
        fmt(premiums.total) +
        '<small class="block text-[10px] font-semibold text-gray-400">Total incl. taxes</small></div>' +
        '<button type="button" onclick="actionDownload()" class="block w-full py-2.5 rounded-lg text-xs font-bold text-white transition bg-blue-600 hover:bg-blue-500">Download Quote ↓</button>' +
        "</div>";
}
var _pdfContext = null;
var _pdfPreviewLoaded = false;
var _previewLoader = null;

function pdfFieldsFor(company, value, formData, numPassengers) {
    return {
        company_slug: company.slug,
        vehicle_type: formData.vehicleType || window.quotationVehicleType || "private",
        car_valuation: value,
        num_passengers: numPassengers || window.quotationNumPassengers || 1,
        salutation: formData.salutation || "",
        first_name: formData.firstName || "",
        middle_name: "",
        last_name: formData.lastName || "",
        phone: formData.phone || "",
        email: formData.email || "",
        car_registration: formData.reg || "",
        chassis_number: formData.chassis || "",
        engine_number: formData.engine || "",
        car_make: formData.make || "",
        car_model: formData.model || "",
        body_type: formData.body || "",
        car_year: formData.year || "",
        color: formData.color || "",
    };
}

function postToTarget(url, fields, target) {
    function submitWithToken(token) {
        var form = document.createElement("form");
        form.method = "POST";
        form.action = url;
        if (target) form.target = target;
        var csrf = document.createElement("input");
        csrf.type = "hidden";
        csrf.name = "_token";
        csrf.value =
            (document.querySelector('meta[name="csrf-token"]') || {}).content || "";
        form.appendChild(csrf);
        if (token) {
            var captcha = document.createElement("input");
            captcha.type = "hidden";
            captcha.name = "g-recaptcha-response";
            captcha.value = token;
            form.appendChild(captcha);
        }
        Object.keys(fields).forEach(function (key) {
            var input = document.createElement("input");
            input.type = "hidden";
            input.name = key;
            input.value = fields[key] == null ? "" : fields[key];
            form.appendChild(input);
        });
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }

    if (typeof grecaptcha !== "undefined" && window.mvipRecaptchaSiteKey) {
        grecaptcha.ready(function () {
            grecaptcha
                .execute(window.mvipRecaptchaSiteKey, { action: "generate_quotation" })
                .then(submitWithToken)
                .catch(function () {
                    submitWithToken(null);
                });
        });
    } else {
        submitWithToken(null);
    }
}

function loadPdfPreview() {
    if (!_pdfContext) return;
    postToTarget("/quotation/pdf/preview", _pdfContext, "pdf-preview-frame");
    _pdfPreviewLoaded = true;
}

function setPdfContext(company, value, formData, numPassengers) {
    _pdfContext = pdfFieldsFor(company, value, formData, numPassengers);
    _pdfPreviewLoaded = false;
    _previewLoader = loadPdfPreview;
    var wrap = document.getElementById("pdf-preview");
    if (wrap && wrap.style.display === "block") loadPdfPreview();
}

function compareAllFieldsFromForm() {
    var formData = getFormData(),
        value = Number(formData.value) || 8e5,
        numPassengers = formData.numPassengers || window.quotationNumPassengers || 1;
    return {
        vehicle_type: formData.vehicleType || window.quotationVehicleType || "private",
        car_valuation: value,
        num_passengers: numPassengers,
        salutation: formData.salutation || "",
        first_name: formData.firstName || "",
        middle_name: "",
        last_name: formData.lastName || "",
        phone: formData.phone || "",
        email: formData.email || "",
        car_registration: formData.reg || "",
        chassis_number: formData.chassis || "",
        engine_number: formData.engine || "",
        car_make: formData.make || "",
        car_model: formData.model || "",
        body_type: formData.body || "",
        car_year: formData.year || "",
        color: formData.color || "",
    };
}

function loadCompareAllPreview() {
    postToTarget("/quotation/compare-all/pdf/preview", compareAllFieldsFromForm(), "pdf-preview-frame");
    _pdfPreviewLoaded = true;
}

function compareAllPdf(mode) {
    var fields = compareAllFieldsFromForm();
    if (mode === "preview") {
        var wrap = document.getElementById("pdf-preview"),
            btn = document.getElementById("btn-preview");
        _previewLoader = loadCompareAllPreview;
        loadCompareAllPreview();
        wrap.style.display = "block";
        if (btn) {
            btn.classList.add("active", "bg-purple-800");
            btn.classList.remove("bg-purple-600", "hover:bg-purple-500");
        }
        wrap.scrollIntoView({ behavior: "smooth", block: "start" });
        showToast("Comparing all insurers", "success");
    } else if (mode === "download") {
        postToTarget("/quotation/compare-all/pdf/download", fields, null);
        showToast("Combined quotation downloaded", "success");
    } else if (mode === "print") {
        postToTarget("/quotation/compare-all/pdf/preview", fields, "_blank");
        showToast("Combined quotation opened for printing", "success");
    } else if (mode === "email") {
        var defaultEmail = fields.email || "";
        var target = window.prompt("Send this comparison to which email address?", defaultEmail);
        if (!target) return;
        fields.email = target;
        fetch("/quotation/compare-all/pdf/email", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN":
                    (document.querySelector('meta[name="csrf-token"]') || {}).content || "",
            },
            body: JSON.stringify(fields),
        })
            .then(function (r) {
                return r.json().then(function (json) {
                    return { ok: r.ok, json: json };
                });
            })
            .then(function (res) {
                if (res.ok && res.json.success) {
                    showToast(res.json.message || "Comparison emailed", "success");
                } else {
                    showToast(res.json.message || "We could not send the email. Please try again.", "error");
                }
            })
            .catch(function () {
                showToast("We could not send the email. Please try again.", "error");
            });
    }
}
function saveSessionQuotation(e, t) {
    if (!window.quotationDbData && window.MIC_AUTH)
        try {
            var a = sessionStorage.getItem("micQuoteData");
            if (!a) return;
            var r = JSON.parse(a),
                n = t ? getCompanyBySlug(t) : null;
            if (!n) return;
            var payload = {
                company_slug: t,
                car_make: r.make || "",
                car_model: r.model || "",
                car_year: r.year || "",
                car_registration: r.reg || "",
                color: r.color || "",
                car_valuation: Number(r.value) || 0,
                vehicle_type:
                    r.vehicleType || window.quotationVehicleType || "private",
                num_passengers: Number(r.numPassengers) || 1,
                salutation: r.salutation || "",
                first_name: r.firstName || "",
                last_name: r.lastName || "",
                phone: r.phone || "",
                email: r.email || "",
                chassis_number: r.chassis || "",
                engine_number: r.engine || "",
                body_type: r.body || "",
            };
            var doSave = function (token) {
                if (token) payload["g-recaptcha-response"] = token;
                fetch("/api/quotation/from-session", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN":
                            document.querySelector('meta[name="csrf-token"]')
                                ?.content || "",
                    },
                    body: JSON.stringify(payload),
                })
                    .then(function (o) {
                        return o.json().then(function (s) {
                            return { ok: o.ok, json: s };
                        });
                    })
                    .then(function (o) {
                        var s = o.json;
                        if (o.ok && s.success) {
                            try {
                                sessionStorage.removeItem("micQuoteData");
                            } catch {}
                            try {
                                sessionStorage.removeItem("pending_quote_company");
                            } catch {}
                            window.location.href =
                                "/quotations/" + s.quotation.id + "?generated=1";
                        } else {
                            var d = s.errors ? Object.values(s.errors)[0] : null,
                                i =
                                    (d && d[0]) ||
                                    s.message ||
                                    "We could not save your quotation. Please try again from your dashboard.";
                            (console.warn("Failed to save quotation:", i),
                                showToast(i, "error"));
                        }
                    })
                    .catch(function (o) {
                        (console.warn("Failed to save quotation:", o),
                            showToast(
                                "We could not save your quotation. Please try again from your dashboard.",
                                "error",
                            ));
                    });
            };
            if (typeof grecaptcha !== "undefined" && window.mvipRecaptchaSiteKey) {
                grecaptcha.ready(function () {
                    grecaptcha
                        .execute(window.mvipRecaptchaSiteKey, { action: "generate_quotation" })
                        .then(doSave)
                        .catch(function () {
                            doSave(null);
                        });
                });
            } else {
                doSave(null);
            }
        } catch {}
}
function populateVehicleSummary(formData, value) {
    var ids = {
        "s-name": [formData.salutation, formData.firstName, formData.lastName].filter(Boolean).join(" ") || "\u2014",
        "s-phone": formData.phone || "\u2014",
        "s-email": formData.email || "\u2014",
        "s-reg": formData.reg || "\u2014",
        "s-make": ((formData.make || "") + " " + (formData.model || "")).trim() || "\u2014",
        "s-year": formData.year || "\u2014",
        "s-chassis": formData.chassis || "\u2014",
        "s-value": fmt(value),
    };
    Object.keys(ids).forEach(function (id) {
        var el = document.getElementById(id);
        if (el) el.textContent = ids[id];
    });
    var compactVehicle = document.getElementById("compact-vehicle");
    if (compactVehicle) {
        compactVehicle.textContent =
            ((formData.make || "") + " " + (formData.model || "")).trim() || "\u2014";
    }
}

function initSingleView() {
    var e = getFormData(),
        t = getCompanyData(),
        a = window.selectedCompanySlug,
        r = Number(e.value) || 8e5,
        n = a ? getCompanyBySlug(a) : t[0] || null;
    if (!n) {
        document.getElementById("hero-quote-num").textContent =
            e.quoteNum || "\u2014";
        return;
    }
    saveSessionQuotation(e, a);
    var o = e.numPassengers || window.quotationNumPassengers || 1,
        s = calcPremiums(r, n, o),
        d = e.quoteNum || genQuoteNum(n),
        i = document.querySelector("[x-data]");
    if (i && window.Alpine)
        try {
            window.Alpine.$data(i).selectedName = n.name;
        } catch {}
    renderCompanyLogo(a);
    document.getElementById("hero-quote-num").textContent = d;
    populateVehicleSummary(e, r);
    document.getElementById("s-company").textContent = n.name;
    document.getElementById("breakdown-company-label").textContent = n.name;
    var l = [
            ["Estimated Vehicle Value", "\u2014", fmt(r)],
            [
                "Basic Annual Premium",
                "@ " + (n.rate * 100).toFixed(1) + "% of vehicle value",
                fmt(s.basic),
            ],
            ["Windscreen Premium", "Included", "KES 0"],
            ["Radio / Cassette Premium", "Included", "KES 0"],
            n.isPvtInclusive
                ? ["PVT Premium", "Inclusive", "Inclusive"]
                : [
                      "PVT Premium",
                      "@ " + (n.pvtRate * 100).toFixed(2) + "%",
                      fmt(s.pvt),
                  ],
            [
                "PLL Premium",
                "@ KES " +
                    (n.pllPerPerson || 0) +
                    " per person \xD7 " +
                    (window.quotationNumPassengers || 1) +
                    " pax",
                fmt(s.pll),
            ],
            ["Levis (0.45%)", "On Basic + PVT + PLL", fmt(s.levis)],
            ["Stamp Duty", "Fixed", fmt(s.stampDuty)],
        ],
        c = document.getElementById("breakdown-rows");
    ((c.innerHTML = l
        .map(function (m) {
            return (
                '<tr><td class="py-2 text-gray-800 dark:text-white">' +
                m[0] +
                '</td><td class="py-2 text-gray-400 text-[12.5px]">' +
                m[1] +
                '</td><td class="py-2 text-right font-semibold text-gray-800 dark:text-white">' +
                m[2] +
                "</td></tr>"
            );
        })
        .join("")),
        (document.getElementById("breakdown-total").textContent = fmt(s.total)),
        renderSingleDownloadCard(n, r, s),
        setPdfContext(n, r, e, o));
    var b = document.getElementById("compact-company");
    if (b) b.textContent = n.name;
    var qEl = document.getElementById("compact-quote");
    if (qEl) qEl.textContent = d;
    try {
        var g = sessionStorage.getItem("micQuoteData");
        g && showToast("Quotation generated", "success");
    } catch {}
}

function initCompareView() {
    var e = getFormData(),
        t = getCompanyData(),
        r = Number(e.value) || 8e5;
    populateVehicleSummary(e, r);
    renderCompareTable(t, r);
    renderDownloadCards({ slug: null }, r);
    _previewLoader = loadCompareAllPreview;
}

function init() {
    if (document.getElementById("single-breakdown-wrap")) {
        initSingleView();
    } else if (document.getElementById("compare-all-wrap")) {
        initCompareView();
    }
}
function actionDownload() {
    if (!_pdfContext) return;
    postToTarget("/quotation/pdf/download", _pdfContext, null);
    showToast("Quotation downloaded", "success");
}
function actionPrint() {
    if (!_pdfContext) return;
    postToTarget("/quotation/pdf/preview", _pdfContext, "_blank");
    showToast("Quotation opened for printing", "success");
}
function actionTogglePreview() {
    var e = document.getElementById("pdf-preview"),
        t = document.getElementById("btn-preview");
    e.style.display === "none" || !e.style.display
        ? (!_pdfPreviewLoaded && _previewLoader && _previewLoader(),
          (e.style.display = "block"),
          t.classList.add("active", "bg-purple-800"),
          t.classList.remove("bg-purple-600", "hover:bg-purple-500"),
          e.scrollIntoView({ behavior: "smooth", block: "start" }))
        : ((e.style.display = "none"),
          t.classList.remove("active", "bg-purple-800"),
          t.classList.add("bg-purple-600", "hover:bg-purple-500"));
}
function downloadQuote(e) {
    var t = getCompanyBySlug(e);
    if (t) {
        var a = getFormData(),
            r = Number(a.value) || 8e5,
            n = a.numPassengers || window.quotationNumPassengers || 1,
            fields = pdfFieldsFor(t, r, a, n);
        (showActionBar(),
            postToTarget("/quotation/pdf/download", fields, null),
            showToast("Quotation downloaded", "success"));
    }
}
(window.addEventListener("message", function (e) {
    (e.data === "quote-generated" &&
        showToast("Quotation generated", "success"),
        e.data === "quote-downloaded" &&
            showToast("Quotation downloaded", "success"),
        e.data === "quote-printed" &&
            showToast("Quotation printed", "success"));
}),
    document.addEventListener("DOMContentLoaded", init));
