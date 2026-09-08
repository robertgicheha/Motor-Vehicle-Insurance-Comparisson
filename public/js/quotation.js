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
function cardBenefits(company) {
    var candidates = [
        company.minSumInsuredText
            ? "Min. Sum Insured: " + company.minSumInsuredText.replace(/^Minimum Sum Insured-?\s*/i, "")
            : null,
        company.maxAgeText && company.maxAgeText !== "—"
            ? "Max Vehicle Age: " + company.maxAgeText.replace(/^Maximum Age New [Bb]usiness-?\s*/i, "")
            : null,
        company.excessProtector ? "Excess Protector: " + company.excessProtector : null,
        company.geoArea ? "Cover Area: " + company.geoArea : null,
    ].filter(Boolean);
    return candidates.slice(0, 3).map(function (text) {
        return text.length > 46 ? text.slice(0, 44) + "…" : text;
    });
}
function renderComparisonGrid(companies, value, selectedSlug) {
    var grid = document.getElementById("comparison-grid");
    if (!grid) return;
    var cheapest = companies.reduce(function (min, c) {
        var total = calcPremiums(value, c, window.quotationNumPassengers).total;
        return min === null || total < min ? total : min;
    }, null);
    grid.innerHTML = companies
        .map(function (company) {
            var premiums = calcPremiums(value, company, window.quotationNumPassengers),
                isSelected = company.slug === selectedSlug,
                isCheapest = premiums.total === cheapest,
                logo = company.logo || "",
                benefits = cardBenefits(company);
            var badge = isSelected
                ? '<span class="absolute top-3 right-3 text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-blue-500 text-white">Selected</span>'
                : isCheapest
                  ? '<span class="absolute top-3 right-3 text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-emerald-500 text-white">Lowest Premium</span>'
                  : "";
            var benefitsHtml = benefits.length
                ? '<div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 space-y-1.5">' +
                  '<p class="text-[9px] font-semibold uppercase tracking-wider text-gray-400 mb-1.5">Key Benefits</p>' +
                  benefits
                      .map(function (b) {
                          return (
                              '<div class="flex items-start gap-1.5 text-[10.5px] text-gray-600 dark:text-gray-300 leading-snug">' +
                              '<svg class="w-3 h-3 mt-0.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>' +
                              "<span>" + b + "</span></div>"
                          );
                      })
                      .join("") +
                  "</div>"
                : "";
            return (
                '<button type="button" onclick="downloadQuote(\'' + company.slug + '\')" class="group relative text-left rounded-2xl border bg-white dark:bg-gray-800 shadow-sm p-5 transition hover:-translate-y-0.5 hover:shadow-lg flex flex-col ' +
                (isSelected
                    ? "border-blue-500 ring-2 ring-blue-500/20"
                    : "border-gray-200 dark:border-gray-700") +
                '">' +
                badge +
                '<div class="flex items-center gap-3 mb-3 h-10 overflow-hidden">' +
                (logo
                    ? '<img src="' + logo + '" alt="' + company.name + '" class="max-h-9 max-w-[120px] object-contain" onerror="fallbackLogo(this,\'' + company.slug + '\')">'
                    : "") +
                '<span class="text-[11px] font-extrabold tracking-wider text-blue-600 dark:text-blue-400" style="' +
                (logo ? "display:none" : "") +
                '">' +
                (company.prefix || company.slug.toUpperCase()) +
                '</span></div>' +
                '<div class="flex items-baseline justify-between">' +
                '<span class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Total Premium</span>' +
                '<span class="text-lg font-extrabold text-blue-600 dark:text-blue-400">' + fmt(premiums.total) + '</span>' +
                '</div>' +
                '<div class="mt-1 text-[10.5px] text-gray-400">Rate ' + (company.rate * 100).toFixed(1) + '% &middot; Basic ' + fmt(premiums.basic) + '</div>' +
                benefitsHtml +
                '<div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">' +
                '<span class="text-[10.5px] font-semibold text-blue-600 dark:text-blue-400 group-hover:underline">View &amp; Download</span>' +
                '<svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400 transition group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/></svg>' +
                "</div></button>"
            );
        })
        .join("");
}
function renderCompareTable(e, t) {
    for (var a = 3, r = 0; r < a; r++) {
        var n = document.getElementById("cmp-head-" + r),
            o = document.getElementById("cmp-total-" + r);
        if (e[r]) {
            n && (n.textContent = e[r].name);
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
                    return fmt(
                        calcPremiums(t, l, window.quotationNumPassengers).pvt,
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
    o.innerHTML = d;
}
function buildPDFPreview(e, t, a, r, n) {
    var o =
            [r.salutation, r.firstName, r.lastName].filter(Boolean).join(" ") ||
            "\u2014",
        s = new Date().toLocaleString("en-KE", {
            dateStyle: "long",
            timeStyle: "short",
        }),
        d = e.logo || "",
        i =
            '<div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 mb-6"><div class="flex justify-between items-stretch min-h-[130px]"><div class="flex items-center p-5">' +
            (d
                ? '<img src="' +
                  d +
                  '" alt="' +
                  e.name +
                  `" class="max-h-20 max-w-[260px] object-contain" onerror="fallbackLogo(this,'` +
                  e.slug +
                  `')">`
                : "") +
            '<span class="text-gray-800 dark:text-white text-2xl font-extrabold" style="display:' +
            (d ? "none" : "block") +
            '">' +
            (e.prefix || e.slug) +
            '</span></div><div class="flex items-center justify-end p-5 text-right"><p class="text-[12px] leading-7 text-gray-600 dark:text-gray-300 m-0">' +
            e.name +
            '</p></div></div></div><div class="px-2"><p class="text-[13px]">' +
            s +
            '</p><p class="text-gray-800 dark:text-white">Dear ' +
            o +
            '</p><h2 class="text-[15px] font-bold uppercase tracking-wider mb-1">RE: PRIVATE CAR INSURANCE QUOTE</h2><p class="text-[12px] text-gray-500 mb-4">Quotation Reference: <strong>' +
            n +
            `</strong></p><p class="text-[13px] leading-7 mb-4 text-gray-700 dark:text-gray-300">Thank you for requesting us to provide a quote for the above mentioned. We are pleased to submit the quote</p><table class="w-full text-[13px] border-collapse mb-4"><thead><tr><th class="bg-gray-100 dark:bg-gray-700 text-left p-2 border border-gray-200 dark:border-gray-600">Owner's Name</th><th class="bg-gray-100 dark:bg-gray-700 text-left p-2 border border-gray-200 dark:border-gray-600">Vehicle Make</th><th class="bg-gray-100 dark:bg-gray-700 text-left p-2 border border-gray-200 dark:border-gray-600">Year of Manufacture</th></tr></thead><tbody><tr><td class="p-2 border border-gray-200 dark:border-gray-600">` +
            o +
            '</td><td class="p-2 border border-gray-200 dark:border-gray-600">' +
            (r.make || "\u2014") +
            '</td><td class="p-2 border border-gray-200 dark:border-gray-600">' +
            (r.year || "\u2014") +
            '</td></tr></tbody></table><table class="w-full text-[13px] border-collapse mb-4"><thead><tr><th class="bg-gray-100 dark:bg-gray-700 text-left p-2 border border-gray-200 dark:border-gray-600">Registration Number</th><th class="bg-gray-100 dark:bg-gray-700 text-left p-2 border border-gray-200 dark:border-gray-600">Vehicle Estimated Price</th><th class="bg-gray-100 dark:bg-gray-700 text-left p-2 border border-gray-200 dark:border-gray-600">Phone Number</th></tr></thead><tbody><tr><td class="p-2 border border-gray-200 dark:border-gray-600">' +
            (r.reg || "\u2014") +
            '</td><td class="p-2 border border-gray-200 dark:border-gray-600">' +
            fmt(t) +
            '</td><td class="p-2 border border-gray-200 dark:border-gray-600">' +
            (r.phone || "\u2014") +
            '</td></tr></tbody></table><table class="w-full text-[13px] border-collapse mb-4"><thead><tr><th colspan="2" class="text-left p-2 border text-white" style="background:' +
            e.color +
            '">COVER TYPE</th></tr></thead><tbody><tr><td class="p-2 border border-gray-200 dark:border-gray-600">Scope of Cover</td><td class="p-2 border border-gray-200 dark:border-gray-600">Comprehensive</td></tr><tr><td class="p-2 border border-gray-200 dark:border-gray-600">Frequency</td><td class="p-2 border border-gray-200 dark:border-gray-600">Annually</td></tr></tbody></table><table class="w-full text-[13px] border-collapse mb-4"><thead><tr><th colspan="2" class="text-left p-2 border text-white" style="background:' +
            e.color +
            '">SCHEDULE OF INSURANCE</th></tr></thead><tbody><tr><td class="p-2 border border-gray-200 dark:border-gray-600">Basic Annual Premium</td><td class="p-2 border border-gray-200 dark:border-gray-600">' +
            fmt(a.basic) +
            '</td></tr><tr><td class="p-2 border border-gray-200 dark:border-gray-600">Annual Premium + Taxes</td><td class="p-2 border border-gray-200 dark:border-gray-600">' +
            fmt(a.total) +
            '</td></tr><tr><td class="p-2 border border-gray-200 dark:border-gray-600">Windscreen Annual Premium</td><td class="p-2 border border-gray-200 dark:border-gray-600">KES 0</td></tr><tr><td class="p-2 border border-gray-200 dark:border-gray-600">Radio Cassette Premium</td><td class="p-2 border border-gray-200 dark:border-gray-600">KES 0</td></tr></tbody></table></div><div class="px-5 py-4 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 text-[11px] leading-7 text-gray-400">KENYA | UGANDA | TANZANIA | Rate subject to satisfactory valuation report</div>',
        l = document.getElementById("pdf-preview");
    ((l.innerHTML = i), (l.style.display = "none"));
}
function saveSessionQuotation(e, t) {
    if (!window.quotationDbData && window.MIC_AUTH)
        try {
            var a = sessionStorage.getItem("micQuoteData");
            if (!a) return;
            var r = JSON.parse(a),
                n = t ? getCompanyBySlug(t) : null;
            if (!n) return;
            fetch("/api/quotation/from-session", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document.querySelector('meta[name="csrf-token"]')
                            ?.content || "",
                },
                body: JSON.stringify({
                    company_slug: t,
                    car_make: r.make || "",
                    car_model: r.model || "",
                    car_year: r.year || "",
                    car_registration: r.reg || "",
                    color: r.color || "",
                    car_valuation: Number(r.value) || 0,
                    vehicle_type:
                        r.vehicleType ||
                        window.quotationVehicleType ||
                        "private",
                    num_passengers: Number(r.numPassengers) || 1,
                    salutation: r.salutation || "",
                    first_name: r.firstName || "",
                    last_name: r.lastName || "",
                    phone: r.phone || "",
                    email: r.email || "",
                    chassis_number: r.chassis || "",
                    engine_number: r.engine || "",
                    body_type: r.body || "",
                }),
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
        } catch {}
}
function init() {
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
    (renderCompanyLogo(a),
        (document.getElementById("hero-quote-num").textContent = d),
        (document.getElementById("s-name").textContent =
            [e.salutation, e.firstName, e.lastName].filter(Boolean).join(" ") ||
            "\u2014"),
        (document.getElementById("s-phone").textContent = e.phone || "\u2014"),
        (document.getElementById("s-email").textContent = e.email || "\u2014"),
        (document.getElementById("s-company").textContent = n.name),
        (document.getElementById("s-reg").textContent = e.reg || "\u2014"),
        (document.getElementById("s-make").textContent =
            ((e.make || "") + " " + (e.model || "")).trim() || "\u2014"),
        (document.getElementById("s-year").textContent =
            (e.year || "\u2014") + "  \u2022  " + (e.body || "\u2014")),
        (document.getElementById("s-color").textContent = e.color || "\u2014"),
        (document.getElementById("s-chassis").textContent =
            e.chassis || "\u2014"),
        (document.getElementById("s-value").textContent = fmt(r)),
        (document.getElementById("single-breakdown-wrap").style.display = ""),
        (document.getElementById("compare-all-wrap").style.display = ""),
        (document.getElementById("breakdown-company-label").textContent =
            n.name));
    var l = [
            ["Estimated Vehicle Value", "\u2014", fmt(r)],
            [
                "Basic Annual Premium",
                "@ " + (n.rate * 100).toFixed(1) + "% of vehicle value",
                fmt(s.basic),
            ],
            ["Windscreen Premium", "Included", "KES 0"],
            ["Radio / Cassette Premium", "Included", "KES 0"],
            [
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
        renderComparisonGrid(t, r, a),
        renderCompareTable(t, r),
        renderDownloadCards(n, r, s, e, d),
        buildPDFPreview(n, r, s, e, d));
    var u = document.getElementById("compact-vehicle"),
        b = document.getElementById("compact-company");
    (u &&
        (u.textContent =
            ((e.make || "") + " " + (e.model || "")).trim() || "\u2014"),
        b && (b.textContent = n.name));
    try {
        var g = sessionStorage.getItem("micQuoteData");
        g && showToast("Quotation generated", "success");
    } catch {}
}
function actionDownload() {
    var e = document.getElementById("pdf-preview");
    e.innerHTML.trim() &&
        ((e.style.display = "block"),
        setTimeout(function () {
            (window.print(),
                setTimeout(function () {
                    document
                        .getElementById("btn-preview")
                        .classList.contains("active") ||
                        (e.style.display = "none");
                }, 500));
        }, 150),
        showToast("Quotation downloaded", "success"));
}
function actionPrint() {
    var e = document.getElementById("pdf-preview");
    e.innerHTML.trim() &&
        ((e.style.display = "block"),
        setTimeout(function () {
            window.print();
        }, 150),
        showToast("Quotation printed", "success"));
}
function actionTogglePreview() {
    var e = document.getElementById("pdf-preview"),
        t = document.getElementById("btn-preview");
    e.style.display === "none" || !e.style.display
        ? ((e.style.display = "block"),
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
            o = calcPremiums(r, t, n),
            s = a.quoteNum || genQuoteNum(t);
        (buildPDFPreview(t, r, o, a, s),
            document
                .getElementById("btn-preview")
                .classList.remove("active", "bg-purple-800"),
            document
                .getElementById("btn-preview")
                .classList.add("bg-purple-600", "hover:bg-purple-500"),
            showActionBar(),
            (document.getElementById("pdf-preview").style.display = "block"),
            setTimeout(function () {
                window.print();
            }, 150),
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
