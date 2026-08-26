/**
 * dashboard-charts.js
 * Initializes the 4 analytics charts on the admin/super-admin dashboard.
 * Expects `window.dashboardData` to be set via @json(...) in the Blade view.
 */

(function () {
  if (typeof Chart === 'undefined') {
    console.error('Chart.js not loaded — check the CDN script tag order.');
    return;
  }

  const data = window.dashboardData || {};

  const PALETTE = ['#1756c8', '#7c3aed', '#0f7a65', '#d4860e', '#c0392b', '#1E40AF', '#F59E0B', '#10B981', '#8B5CF6', '#EF4444'];

  Chart.defaults.font.family = "'Inter', -apple-system, BlinkMacSystemFont, sans-serif";
  Chart.defaults.font.size = 11;
  Chart.defaults.color = '#7a8baa';
  Chart.defaults.plugins.legend.labels.usePointStyle = true;
  Chart.defaults.plugins.legend.labels.boxWidth = 8;
  Chart.defaults.plugins.legend.labels.boxHeight = 8;

  function formatMonthLabel(ym) {
    // "2026-01" -> "Jan 2026"; if already formatted (e.g. "Jan 2026"), pass through
    if (!ym) return '';
    if (/^\d{4}-\d{2}$/.test(ym)) {
      const [y, m] = ym.split('-');
      const d = new Date(Number(y), Number(m) - 1, 1);
      return d.toLocaleString('en-US', { month: 'short', year: 'numeric' });
    }
    return ym;
  }

  function money(v) {
    return 'KES ' + Number(v || 0).toLocaleString('en-KE');
  }

  // ── 1. Quotations by Company (Doughnut) ──────────────────────────────
  (function companyChart() {
    const el = document.getElementById('companyChart');
    if (!el) return;

    const companyData = data.quotationsByCompany || {};
    const colorMap = data.companyColorMap || {};
    const labels = Object.keys(companyData);
    const values = Object.values(companyData);
    const colors = labels.map((name, i) => colorMap[name] || PALETTE[i % PALETTE.length]);

    if (labels.length === 0 || values.every(v => v === 0)) {
      renderEmptyState(el, 'No quotations yet');
      return;
    }

    new Chart(el, {
      type: 'doughnut',
      data: {
        labels,
        datasets: [{
          data: values,
          backgroundColor: colors,
          borderWidth: 0,
          hoverOffset: 6,
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '68%',
        plugins: {
          legend: { position: 'bottom' },
          tooltip: {
            callbacks: {
              label: (ctx) => `${ctx.label}: ${ctx.parsed} quotation${ctx.parsed === 1 ? '' : 's'}`,
            },
          },
        },
      },
    });
  })();

  // ── 2. Monthly Trend (Line) ───────────────────────────────────────────
  (function monthlyChart() {
    const el = document.getElementById('monthlyChart');
    if (!el) return;

    const rows = data.monthlyQuotations || [];
    if (rows.length === 0) {
      renderEmptyState(el, 'No trend data yet');
      return;
    }

    const labels = rows.map(r => formatMonthLabel(r.month));
    const values = rows.map(r => r.total);

    new Chart(el, {
      type: 'line',
      data: {
        labels,
        datasets: [{
          label: 'Quotations',
          data: values,
          borderColor: '#1756c8',
          backgroundColor: 'rgba(23,86,200,0.08)',
          borderWidth: 2,
          tension: 0.35,
          fill: true,
          pointRadius: 3,
          pointBackgroundColor: '#1756c8',
          pointBorderColor: '#fff',
          pointBorderWidth: 1.5,
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
            ticks: { precision: 0 },
            grid: { color: '#eef1f6' },
          },
        },
      },
    });
  })();

  // ── 3. Total Distribution of Quotations (Bar) ─────────────────────────
  (function distributionChart() {
    const el = document.getElementById('distributionChart');
    if (!el) return;

    const dist = data.statusDistribution || { generated: 0, completed: 0 };
    const labels = ['Generated', 'Completed'];
    const values = [dist.generated || 0, dist.completed || 0];
    const colors = ['#1756c8', '#0f7a65'];

    if (values.every(v => v === 0)) {
      renderEmptyState(el, 'No status data yet');
      return;
    }

    new Chart(el, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          data: values,
          backgroundColor: colors,
          borderRadius: 6,
          maxBarThickness: 56,
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
            ticks: { precision: 0 },
            grid: { color: '#eef1f6' },
          },
        },
      },
    });
  })();

  // ── 4. Most Subscribed Premium / Avg Premium Trend (Bar) ──────────────
  (function premiumChart() {
    const el = document.getElementById('premiumChart');
    if (!el) return;

    const rows = data.premiumTrend || [];
    if (rows.length === 0) {
      renderEmptyState(el, 'No premium data yet');
      return;
    }

    const labels = rows.map(r => formatMonthLabel(r.month));
    const values = rows.map(r => r.avg_premium);

    new Chart(el, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: 'Avg Premium',
          data: values,
          backgroundColor: '#7c3aed',
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
              label: (ctx) => money(ctx.parsed.y),
            },
          },
        },
        scales: {
          x: { grid: { display: false } },
          y: {
            beginAtZero: true,
            ticks: { callback: (v) => 'KES ' + Number(v).toLocaleString('en-KE') },
            grid: { color: '#eef1f6' },
          },
        },
      },
    });
  })();

  // ── Helper: empty-state message inside a card when there's no data ────
  function renderEmptyState(canvasEl, message) {
    const wrapper = canvasEl.parentElement;
    canvasEl.style.display = 'none';
    const msg = document.createElement('div');
    msg.textContent = message;
    msg.style.cssText = 'padding:32px 0;text-align:center;font-size:12px;color:#a3b0c2;';
    wrapper.appendChild(msg);
  }
})();