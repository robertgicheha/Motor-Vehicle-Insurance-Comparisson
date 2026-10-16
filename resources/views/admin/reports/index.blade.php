<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Reporting</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Reports</h1>
                <p class="mt-0.5 text-xs text-slate-400">Generate and export reports for quotations and stickers</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="kpi-card p-4">
                <div class="accent-bar"></div>
                <p class="kpi-eyebrow">Companies</p>
                <p class="kpi-value mt-1">{{ $companies->count() }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Active insurers</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#34d399,#6ee7b7)"></div>
                <p class="kpi-eyebrow">Quotations</p>
                <p class="kpi-value mt-1">{{ $stats->quotations_count ?? 0 }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Generated quotes</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#818cf8,#a5b4fc)"></div>
                <p class="kpi-eyebrow">Stickers</p>
                <p class="kpi-value mt-1">{{ $stats->stickers_count ?? 0 }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Insurance stickers</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#fb7185,#fda4af)"></div>
                <p class="kpi-eyebrow">Report Types</p>
                <p class="kpi-value mt-1">2</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Quotations &amp; Stickers</span>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Report Filters</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">Configure and generate your report</p>
            </div>
            <div class="flex gap-1 bg-gray-100 dark:bg-gray-700 p-0.5 rounded-lg">
                <button onclick="switchTab('quotations')" id="tab-quotations" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition bg-white dark:bg-gray-600 text-gray-800 dark:text-white shadow-sm">Quotations</button>
                <button onclick="switchTab('stickers')" id="tab-stickers" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition text-gray-500 dark:text-gray-400 hover:text-gray-700">Stickers</button>
            </div>
        </div>
        <div class="px-5 py-4 space-y-4">
            <form id="reportForm" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="hidden" name="report_type" id="report_type" value="quotations">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Company</label>
                    <select name="company_id" id="filter_company"
                            class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">All Companies</option>
                        @foreach($companies as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Period</label>
                    <select name="period" id="filter_period"
                            class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">All Time</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">From</label>
                    <input type="date" name="date_from" id="filter_from"
                           class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">To</label>
                    <input type="date" name="date_to" id="filter_to"
                           class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </form>
            <div class="flex flex-wrap items-center gap-2">
                <button onclick="generateReport()" class="px-4 py-1.5 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Generate Report</button>
                <div class="w-px h-5 bg-gray-200 dark:bg-gray-600 hidden sm:block"></div>
                <button onclick="exportReport('pdf')" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-red-600 hover:bg-red-500 text-white rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    PDF
                </button>
                <button onclick="exportReport('excel')" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Excel
                </button>
                <button onclick="exportReport('csv')" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m0 0l-4-4m4 4l4-4"/></svg>
                    CSV
                </button>
            </div>
        </div>
    </div>

    <div id="reportResults" class="hidden space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3" id="summaryCards"></div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5" style="max-width:380px">
                <h3 class="text-xs font-semibold text-gray-800 dark:text-white mb-3" id="chart1-title">Quotations by Company</h3>
                <canvas id="reportCompanyChart" style="max-height:200px;cursor:pointer"></canvas>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="text-xs font-semibold text-gray-800 dark:text-white mb-3" id="chart2-title">Daily Trend</h3>
                <canvas id="reportDailyChart" height="200" style="max-height:200px"></canvas>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-xs font-semibold text-gray-800 dark:text-white" id="table-title">Detailed Report</h3>
                <span class="text-[10px] text-gray-400">Report data</span>
            </div>
            <div class="overflow-x-auto" id="reportTable"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-poll reports every 30 seconds for real-time data
    var reportPollTimer = setInterval(function() {
        if (!document.getElementById('reportResults').classList.contains('hidden')) {
            generateReport();
        }
    }, 30000);
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) { clearInterval(reportPollTimer); }
        else { reportPollTimer = setInterval(function() {
            if (!document.getElementById('reportResults').classList.contains('hidden')) {
                generateReport();
            }
        }, 30000); }
    });

    let charts = { company: null, daily: null };
    let currentTab = 'quotations';

    function switchTab(tab) {
        currentTab = tab;
        document.getElementById('report_type').value = tab;
        document.querySelectorAll('[id^="tab-"]').forEach(el => {
            el.classList.remove('bg-white', 'dark:bg-gray-600', 'text-gray-800', 'dark:text-white', 'shadow-sm');
            el.classList.add('text-gray-500', 'dark:text-gray-400');
        });
        const btn = document.getElementById('tab-' + tab);
        btn.classList.remove('text-gray-500', 'dark:text-gray-400');
        btn.classList.add('bg-white', 'dark:bg-gray-600', 'text-gray-800', 'dark:text-white', 'shadow-sm');
        document.getElementById('reportResults').classList.add('hidden');
    }

    function getFormData() {
        const form = document.getElementById('reportForm');
        const data = new FormData(form);
        const params = new URLSearchParams();
        data.forEach((v, k) => { if(v) params.append(k, v); });
        return params;
    }

    function generateReport() {
        const params = getFormData();
        const endpoint = currentTab === 'stickers'
            ? '{{ route('admin.reports.stickers') }}'
            : '{{ route('admin.reports.generate') }}';

        fetch(endpoint, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: params
        })
        .then(r => r.json())
        .then(d => {
            document.getElementById('reportResults').classList.remove('hidden');
            if (currentTab === 'stickers') {
                renderStickerReport(d);
            } else {
                renderQuotationReport(d);
            }
        });
    }

    function renderQuotationReport(d) {
        document.getElementById('chart1-title').textContent = 'Quotations by Company';
        document.getElementById('chart2-title').textContent = 'Daily Trend';
        document.getElementById('table-title').textContent = 'Detailed Report - Quotations';
        document.getElementById('summaryCards').innerHTML = `
            <div class="kpi-card p-4">
                <div class="accent-bar"></div>
                <p class="kpi-eyebrow">Total Quotations</p>
                <p class="kpi-value mt-1">${d.summary.total_quotations}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Generated quotes</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#34d399,#6ee7b7)"></div>
                <p class="kpi-eyebrow">Total Premium</p>
                <p class="kpi-value mt-1 text-emerald-500">KES ${Number(d.summary.total_premium).toLocaleString()}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Total premium amount</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#818cf8,#a5b4fc)"></div>
                <p class="kpi-eyebrow">Avg Premium</p>
                <p class="kpi-value mt-1 text-blue-500">KES ${Number(d.summary.average_premium).toLocaleString()}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Average per quote</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#fb7185,#fda4af)"></div>
                <p class="kpi-eyebrow">Avg Valuation</p>
                <p class="kpi-value mt-1 text-rose-500">KES ${Number(d.summary.average_valuation).toLocaleString()}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Average car value</span>
            </div>
        `;
        const labels = Object.keys(d.by_company);
        const counts = Object.values(d.by_company).map(v => v.count);
        renderChart('reportCompanyChart', labels, counts, 'bar', 'Quotations');
        renderChart('reportDailyChart', Object.keys(d.by_day), Object.values(d.by_day).map(v => v.count), 'line', 'Quotations');
        renderQuotationTable(d.quotations);
    }

    function renderStickerReport(d) {
        document.getElementById('chart1-title').textContent = 'Stickers by Underwriter';
        document.getElementById('chart2-title').textContent = 'Daily Trend';
        document.getElementById('table-title').textContent = 'Detailed Report - Stickers';
        document.getElementById('summaryCards').innerHTML = `
            <div class="kpi-card p-4">
                <div class="accent-bar"></div>
                <p class="kpi-eyebrow">Total Stickers</p>
                <p class="kpi-value mt-1">${d.summary.total_stickers}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Generated stickers</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#34d399,#6ee7b7)"></div>
                <p class="kpi-eyebrow">Total Premium</p>
                <p class="kpi-value mt-1 text-emerald-500">KES ${Number(d.summary.total_premium).toLocaleString()}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Total premium amount</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#818cf8,#a5b4fc)"></div>
                <p class="kpi-eyebrow">Total Downloads</p>
                <p class="kpi-value mt-1 text-blue-500">${d.summary.total_downloads}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Certificate downloads</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#fb7185,#fda4af)"></div>
                <p class="kpi-eyebrow">Avg Premium</p>
                <p class="kpi-value mt-1 text-rose-500">KES ${Number(d.summary.average_premium).toLocaleString()}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Average per certificate</span>
            </div>
        `;
        const labels = Object.keys(d.by_company);
        const counts = Object.values(d.by_company).map(v => v.count);
        renderChart('reportCompanyChart', labels, counts, 'bar', 'Stickers');
        renderChart('reportDailyChart', Object.keys(d.by_day), Object.values(d.by_day).map(v => v.count), 'line', 'Stickers');
        renderStickerTable(d.stickers);
    }

    function renderChart(canvasId, labels, data, type, label) {
        var key = (type === 'doughnut' || type === 'bar') ? 'company' : 'daily';
        if (charts[key]) { charts[key].destroy(); }
        const ctx = document.getElementById(canvasId).getContext('2d');
        var options = {
            responsive: true,
            maintainAspectRatio: type === 'doughnut',
            plugins: { legend: { display: type === 'doughnut', position: 'bottom', labels: { font: { size: 10 } } } },
            scales: (type === 'line') ? { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } }, x: { grid: { display: false } } } : {}
        };
        if (type === 'doughnut') {
            options.onClick = function(e, item) {
                if (item.length > 0) {
                    window.location.href = '{{ route("quotations.index") }}?company=' + encodeURIComponent(this.data.labels[item[0].index]);
                }
            };
        }
        const chart = new Chart(ctx, {
            type: type === 'bar' ? 'doughnut' : type,
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: data,
                    backgroundColor: type === 'bar' ? ['#1756c8','#7c3aed','#0f7a65','#d4860e','#c0392b'] : 'rgba(59,130,246,0.1)',
                    borderColor: type === 'bar' ? ['#1756c8','#7c3aed','#0f7a65','#d4860e','#c0392b'] : 'rgb(59,130,246)',
                    borderWidth: type === 'bar' ? 0 : 2,
                    borderRadius: 0,
                    fill: type === 'line',
                    tension: type === 'line' ? 0.4 : 0,
                }]
            },
            options: options
        });
        charts[key] = chart;
    }

    function renderQuotationTable(quotations) {
        if (!quotations.length) {
            document.getElementById('reportTable').innerHTML = '<p class="p-5 text-center text-xs text-gray-400">No data found</p>';
            return;
        }
        let html = '<table class="w-full text-xs"><thead><tr class="bg-gray-50 dark:bg-gray-700/40">';
        html += '<th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">#</th><th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Username</th><th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Company</th><th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Vehicle</th><th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Valuation</th><th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Premium</th><th class="px-5 py-2.5 text-center font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Status</th><th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Date</th></tr></thead><tbody class="divide-y divide-gray-100 dark:divide-gray-700">';
        quotations.forEach(q => {
            html += `<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                <td class="px-5 py-2.5 font-medium text-gray-800 dark:text-white">${q.quotation_number}</td>
                <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">${q.user?.username || 'N/A'}</td>
                <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">${q.company?.name || 'N/A'}</td>
                <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">${q.car_make} ${q.car_model}</td>
                <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">${Number(q.car_valuation).toLocaleString()}</td>
                <td class="px-5 py-2.5 text-right font-medium text-gray-800 dark:text-white">${Number(q.total_premium).toLocaleString()}</td>
                <td class="px-5 py-2.5 text-center"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">${q.status}</span></td>
                <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">${new Date(q.created_at).toLocaleDateString()}</td>
            </tr>`;
        });
        html += '</tbody></table>';
        document.getElementById('reportTable').innerHTML = html;
    }

    function renderStickerTable(stickers) {
        if (!stickers.length) {
            document.getElementById('reportTable').innerHTML = '<p class="p-5 text-center text-xs text-gray-400">No data found</p>';
            return;
        }
        let html = '<table class="w-full text-xs"><thead><tr class="bg-gray-50 dark:bg-gray-700/40">';
        html += '<th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">#</th><th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Company</th><th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Reg. No</th><th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Vehicle</th><th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Premium</th><th class="px-5 py-2.5 text-center font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Downloads</th><th class="px-5 py-2.5 text-center font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Via</th><th class="px-5 py-2.5 text-center font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Status</th><th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Date</th></tr></thead><tbody class="divide-y divide-gray-100 dark:divide-gray-700">';
        stickers.forEach(s => {
            html += `<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                <td class="px-5 py-2.5 font-mono text-[10px] font-bold text-gray-800 dark:text-white">${s.sticker_number}</td>
                <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">${s.company?.name || 'N/A'}</td>
                <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">${s.vehicle_registration || '—'}</td>
                <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">${s.car_make} ${s.car_model} (${s.car_year})</td>
                <td class="px-5 py-2.5 text-right font-medium text-gray-800 dark:text-white">KES ${Number(s.premium).toLocaleString()}</td>
                <td class="px-5 py-2.5 text-center font-semibold text-gray-800 dark:text-white">${s.download_count || 0}</td>
                <td class="px-5 py-2.5 text-center"><span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">${(s.created_via || 'web').toUpperCase()}</span></td>
                <td class="px-5 py-2.5 text-center"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">${s.status}</span></td>
                <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">${new Date(s.created_at).toLocaleDateString()}</td>
            </tr>`;
        });
        html += '</tbody></table>';
        document.getElementById('reportTable').innerHTML = html;
    }

    function exportReport(type) {
        const params = getFormData();
        const endpoints = currentTab === 'stickers'
            ? {
                csv:   '{{ route('admin.reports.stickers.export-csv') }}',
                excel: '{{ route('admin.reports.stickers.export-excel') }}',
                pdf:   '{{ route('admin.reports.stickers.export-pdf') }}',
              }
            : {
                csv:   '{{ route('admin.reports.export-csv') }}',
                excel: '{{ route('admin.reports.export-excel') }}',
                pdf:   '{{ route('admin.reports.export-pdf') }}',
              };
        window.location.href = endpoints[type] + '?' + params.toString();
    }
</script>
@endpush
</x-app-layout>
