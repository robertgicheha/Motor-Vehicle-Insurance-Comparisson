/**
 * user-dashboard-charts.js
 * -----------------------------------------------------------------
 * Renders the Quotation Insights charts (status pie, company bar,
 * monthly line, premium comparison bar) inside the left column of
 * the user dashboard. Reads its data from a single JSON payload
 * embedded in #mic-quote-charts via a data-chart-data attribute,
 * so nothing here needs to touch the Blade template's existing
 * markup or PHP.
 *
 * Requires Chart.js (v3 or v4) to already be loaded on the page.
 * -----------------------------------------------------------------
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', init);

    function init() {
        var root = document.getElementById('mic-quote-charts');
        if (!root) return;

        if (typeof Chart === 'undefined') {
            console.warn('[user-dashboard-charts] Chart.js not found on this page.');
            return;
        }

        var data = safeParse(root.dataset.chartData);
        if (!data) return;

        configureChartDefaults();

        var palette = {
            blue: '#2563eb',
            blueSoft: 'rgba(37, 99, 235, 0.14)',
            emerald: '#10b981',
            emeraldSoft: 'rgba(16, 185, 129, 0.14)',
            amber: '#f59e0b',
            purple: '#8b5cf6',
            rose: '#f43f5e',
            slate: '#94a3b8',
        };

        renderStatusPieChart(data.status, palette);
        renderCompanyBarChart(data.byCompany, palette, data.companyColors);
        renderMonthlyLineChart(data.monthly, palette);
        renderPremiumBarChart(data.premiumByCompany, palette, data.companyColors);
    }

    function safeParse(json) {
        if (!json) return null;
        try {
            return JSON.parse(json);
        } catch (err) {
            console.error('[user-dashboard-charts] Could not parse chart data JSON.', err);
            return null;
        }
    }

    function configureChartDefaults() {
        Chart.defaults.font.family =
            "'Inter', ui-sans-serif, system-ui, -apple-system, sans-serif";
        Chart.defaults.font.size = 10;
        Chart.defaults.color = '#9ca3af';
        Chart.defaults.plugins.tooltip.backgroundColor = '#111827';
        Chart.defaults.plugins.tooltip.titleFont = { size: 10, weight: '600' };
        Chart.defaults.plugins.tooltip.bodyFont = { size: 10 };
        Chart.defaults.plugins.tooltip.padding = 8;
        Chart.defaults.plugins.tooltip.cornerRadius = 6;
        Chart.defaults.plugins.tooltip.displayColors = false;
    }

    function seriesColors(count, palette) {
        var base = [
            palette.blue,
            palette.emerald,
            palette.amber,
            palette.purple,
            palette.rose,
            palette.slate,
        ];
        var out = [];
        for (var i = 0; i < count; i++) out.push(base[i % base.length]);
        return out;
    }

    // Prefer the app's existing per-company color map (companyColorMap from the
    // controller) so a given insurer always renders in the same color across the
    // whole dashboard. Falls back to the generic palette for any company missing
    // from the map, or when no map was supplied at all.
    function colorsForLabels(labels, companyColors, palette) {
        var fallback = seriesColors(labels.length, palette);
        if (!companyColors) return fallback;
        return labels.map(function (label, i) {
            return companyColors[label] || fallback[i];
        });
    }

    // ── C1. Status breakdown — minimalistic doughnut with custom legend ──
    function renderStatusPieChart(statusMap, palette) {
        var canvas = document.getElementById('statusPieChart');
        if (!canvas || !statusMap) return;

        var labels = Object.keys(statusMap);
        var values = labels.map(function (k) { return statusMap[k]; });
        var colors = seriesColors(labels.length, palette);

        new Chart(canvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels.map(capitalize),
                datasets: [{
                    data: values,
                    backgroundColor: colors,
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    hoverOffset: 4,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true },
                },
            },
        });

        var legendEl = document.getElementById('statusPieLegend');
        if (legendEl) {
            legendEl.innerHTML = labels.map(function (label, i) {
                return (
                    '<li class="chart-legend-item">' +
                    '<span class="chart-legend-dot" style="background:' + colors[i] + '"></span>' +
                    '<span class="chart-legend-label">' + capitalize(label) + '</span>' +
                    '<span class="chart-legend-value">' + values[i] + '</span>' +
                    '</li>'
                );
            }).join('');
        }
    }

    // ── C2. Quotations by company — minimalistic horizontal bar ──
    function renderCompanyBarChart(companyMap, palette, companyColors) {
        var canvas = document.getElementById('companyBarChart');
        if (!canvas || !companyMap) return;

        var labels = Object.keys(companyMap);
        var values = labels.map(function (k) { return companyMap[k]; });
        var colors = colorsForLabels(labels, companyColors, palette);

        new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors,
                    borderRadius: 6,
                    maxBarThickness: 22,
                }],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: 'rgba(148, 163, 184, 0.15)' },
                        ticks: { precision: 0 },
                    },
                    y: { grid: { display: false } },
                },
            },
        });
    }

    // ── C3. Quotations monthly — minimalistic smoothed line/area ──
    function renderMonthlyLineChart(monthlyMap, palette) {
        var canvas = document.getElementById('monthlyLineChart');
        if (!canvas || !monthlyMap) return;

        var labels = Object.keys(monthlyMap);
        var values = labels.map(function (k) { return monthlyMap[k]; });

        new Chart(canvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    borderColor: palette.emerald,
                    backgroundColor: palette.emeraldSoft,
                    borderWidth: 2,
                    tension: 0.35,
                    fill: true,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                    pointHoverBackgroundColor: palette.emerald,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(148, 163, 184, 0.15)' },
                        ticks: { precision: 0 },
                    },
                },
            },
        });
    }

    // ── C4. Premium comparison by company — minimalistic vertical bar ──
    function renderPremiumBarChart(premiumMap, palette, companyColors) {
        var canvas = document.getElementById('premiumBarChart');
        if (!canvas || !premiumMap) return;

        var labels = Object.keys(premiumMap);
        var values = labels.map(function (k) { return premiumMap[k]; });
        var colors = colorsForLabels(labels, companyColors, palette);

        new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors,
                    borderRadius: 6,
                    maxBarThickness: 40,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                return ' ' + Number(ctx.raw).toLocaleString();
                            },
                        },
                    },
                },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(148, 163, 184, 0.15)' },
                        ticks: {
                            callback: function (val) {
                                return Number(val).toLocaleString();
                            },
                        },
                    },
                },
            },
        });
    }

    function capitalize(str) {
        return String(str).charAt(0).toUpperCase() + String(str).slice(1);
    }
})();