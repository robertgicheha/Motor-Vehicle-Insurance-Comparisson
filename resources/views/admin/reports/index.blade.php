<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Business Intelligence</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Reports</h1>
                <p class="mt-0.5 text-xs text-slate-400">Quantitative analytics, forecasts and qualitative insight for quotations and stickers</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="kpi-card p-4">
                <div class="accent-bar"></div>
                <p class="kpi-eyebrow">Underwriters</p>
                <p class="kpi-value mt-1">{{ $companies->count() }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Active insurers</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#34d399,#6ee7b7)"></div>
                <p class="kpi-eyebrow">Quotations</p>
                <p class="kpi-value mt-1">{{ number_format($stats->quotations_count) }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Generated quotes</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#818cf8,#a5b4fc)"></div>
                <p class="kpi-eyebrow">Stickers</p>
                <p class="kpi-value mt-1">{{ number_format($stats->stickers_count) }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Issued certificates</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#fb7185,#fda4af)"></div>
                <p class="kpi-eyebrow">Premium Written</p>
                <p class="kpi-value mt-1">KES {{ number_format($stats->total_premium, 0) }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">All sources</span>
            </div>
        </div>
    </div>

    {{-- Report Studio: pick a report, scope it, then get / preview / download --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Report Studio</h2>
            <p class="text-[10px] text-gray-400 mt-0.5">Choose a report, set the scope, then generate it or preview &amp; download the PDF</p>
        </div>

        <div class="px-5 py-4 space-y-4">
            {{-- Step 1 — report type --}}
            <div>
                <p class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">1. Report Type</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
                    <button type="button" onclick="switchTab('quotations')" id="tab-quotations"
                        class="text-left border border-blue-500 ring-2 ring-blue-500/20 bg-blue-50 dark:bg-blue-900/20 text-gray-800 dark:text-white rounded-xl px-4 py-3 transition">
                        <span class="flex items-center gap-2.5">
                            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-600/10 text-blue-600 dark:text-blue-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </span>
                            <span>
                                <span class="block text-xs font-semibold">Quotations</span>
                                <span class="block text-[10px] text-gray-400">Premium, valuation &amp; lifecycle analytics</span>
                            </span>
                        </span>
                    </button>
                    <button type="button" onclick="switchTab('stickers')" id="tab-stickers"
                        class="text-left border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 rounded-xl px-4 py-3 transition hover:border-gray-300 dark:hover:border-gray-600">
                        <span class="flex items-center gap-2.5">
                            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-600/10 text-emerald-600 dark:text-emerald-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </span>
                            <span>
                                <span class="block text-xs font-semibold">Stickers</span>
                                <span class="block text-[10px] text-gray-400">Issued certificates &amp; download analytics</span>
                            </span>
                        </span>
                    </button>
                    <button type="button" onclick="switchTab('users')" id="tab-users"
                        class="text-left border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 rounded-xl px-4 py-3 transition hover:border-gray-300 dark:hover:border-gray-600">
                        <span class="flex items-center gap-2.5">
                            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-600/10 text-indigo-600 dark:text-indigo-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </span>
                            <span>
                                <span class="block text-xs font-semibold">Users</span>
                                <span class="block text-[10px] text-gray-400">User performance &amp; activity</span>
                            </span>
                        </span>
                    </button>
                    <button type="button" onclick="switchTab('underwriters')" id="tab-underwriters"
                        class="text-left border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 rounded-xl px-4 py-3 transition hover:border-gray-300 dark:hover:border-gray-600">
                        <span class="flex items-center gap-2.5">
                            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-amber-600/10 text-amber-600 dark:text-amber-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/></svg>
                            </span>
                            <span>
                                <span class="block text-xs font-semibold">Underwriters</span>
                                <span class="block text-[10px] text-gray-400">Availability &amp; coverage</span>
                            </span>
                        </span>
                    </button>
                </div>
            </div>

            {{-- Step 2 — scope --}}
            <div>
                <p class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">2. Scope</p>
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
            </div>

            {{-- Step 3 — get, preview & download --}}
            <div>
                <p class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">3. Get, Preview &amp; Download</p>
                <div class="flex flex-wrap items-center gap-2">
                    <button onclick="generateReport()" class="inline-flex items-center gap-1.5 px-4 py-1.5 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Generate Report
                    </button>
                    <button onclick="previewDocument()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Preview Document
                    </button>
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
    </div>

    <div id="reportResults" class="hidden space-y-6">
        {{-- Result view switcher --}}
        <div class="flex gap-1 bg-gray-100 dark:bg-gray-700 p-0.5 rounded-lg w-fit">
            <button onclick="switchPane('dashboard')" id="pane-tab-dashboard" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition bg-white dark:bg-gray-600 text-gray-800 dark:text-white shadow-sm">Analytics Dashboard</button>
            <button onclick="switchPane('preview')" id="pane-tab-preview" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition text-gray-500 dark:text-gray-400 hover:text-gray-700">Document Preview</button>
        </div>

        {{-- Dashboard pane --}}
        {{-- Dashboard pane --}}
        <div id="pane-dashboard" class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3" id="summaryCards"></div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                <div class="lg:col-span-3 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="text-xs font-semibold text-gray-800 dark:text-white" id="chart1-title">Volume Trend &amp; Forecast</h3>
                            <p class="text-[10px] text-gray-400 mt-0.5" id="chart1-desc">Actual monthly volume with a linear-regression projection for the next 3 months</p>
                        </div>
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 font-medium">Forecast</span>
                    </div>
                    <div style="height:260px"><canvas id="reportTrendChart"></canvas></div>
                </div>

                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                    <h3 class="text-xs font-semibold text-gray-800 dark:text-white mb-1" id="chart2-title">Premium by Underwriter</h3>
                    <p class="text-[10px] text-gray-400 mb-3" id="chart2-desc">Aggregate premium contribution</p>
                    <div style="height:280px"><canvas id="reportCompanyChart"></canvas></div>
                </div>

                <div class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                    <h3 class="text-xs font-semibold text-gray-800 dark:text-white mb-1" id="chart3-title">Status Distribution</h3>
                    <p class="text-[10px] text-gray-400 mb-3" id="chart3-desc">Records by lifecycle status</p>
                    <div style="height:280px"><canvas id="reportStatusChart"></canvas></div>
                </div>

                <div class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                    <h3 class="text-xs font-semibold text-gray-800 dark:text-white mb-1" id="chart4-title">Valuation Mix</h3>
                    <p class="text-[10px] text-gray-400 mb-3" id="chart4-desc">Vehicle value bands</p>
                    <div style="height:250px"><canvas id="reportValuationChart"></canvas></div>
                </div>

                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                    <h3 class="text-xs font-semibold text-gray-800 dark:text-white mb-1" id="chart5-title">Underwriter Share</h3>
                    <p class="text-[10px] text-gray-400 mb-3" id="chart5-desc">Share of total records</p>
                    <div style="height:250px"><canvas id="reportShareChart"></canvas></div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-xs font-semibold text-gray-800 dark:text-white">Key Insights &amp; Observations</h3>
                    <p class="text-[10px] text-gray-400 mt-0.5">Qualitative read-out of the quantitative data</p>
                </div>
                <ul id="insightsList" class="px-5 py-4 space-y-2 text-xs text-gray-600 dark:text-gray-300"></ul>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-xs font-semibold text-gray-800 dark:text-white" id="table-title">Detailed Report</h3>
                    <span class="text-[10px] text-gray-400" id="table-count">Report data</span>
                </div>
                <div class="overflow-x-auto" id="reportTable"></div>
            </div>
        </div>

        {{-- Preview pane --}}
        <div id="pane-preview" class="hidden">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="text-xs font-semibold text-gray-800 dark:text-white" id="preview-title">Document Preview</h3>
                        <p class="text-[10px] text-gray-400 mt-0.5">Rendered live from the current scope — print, save or download it</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="openPreviewInTab()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            Open in new tab
                        </button>
                        <button onclick="exportReport('pdf')" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-red-600 hover:bg-red-500 text-white rounded-lg transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Download PDF
                        </button>
                    </div>
                </div>
                <div class="relative bg-gray-100 dark:bg-gray-900" style="height:76vh">
                    <div id="previewEmpty" class="absolute inset-0 flex flex-col items-center justify-center text-center px-6">
                        <svg class="w-9 h-9 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">No preview yet</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">Click <span class="font-semibold">Preview Document</span> to render the PDF here.</p>
                    </div>
                    <iframe id="previewFrame" class="w-full h-full hidden" src="about:blank" title="Report preview"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const PALETTE = ['#1756c8','#7c3aed','#0f7a65','#d4860e','#c0392b','#0891b2','#db2777','#65a30d','#6366f1','#ea580c'];
    let charts = {};
    let currentTab = 'quotations';

    const fmtInt = (v) => Number(v || 0).toLocaleString();
    const fmtMoney = (v) => Number(v || 0).toLocaleString(undefined, { maximumFractionDigits: 0 });

    function destroyChart(id) {
        if (charts[id]) { charts[id].destroy(); charts[id] = null; }
    }

    function makeChart(id, config) {
        destroyChart(id);
        const el = document.getElementById(id);
        if (!el) return;
        charts[id] = new Chart(el.getContext('2d'), config);
    }

    function switchTab(tab) {
        currentTab = tab;
        document.getElementById('report_type').value = tab;
        ['quotations', 'stickers', 'users', 'underwriters'].forEach(t => {
            const el = document.getElementById('tab-' + t);
            const active = t === tab;
            el.classList.toggle('border-blue-500', active);
            el.classList.toggle('ring-2', active);
            el.classList.toggle('ring-blue-500/20', active);
            el.classList.toggle('bg-blue-50', active);
            el.classList.toggle('dark:bg-blue-900/20', active);
            el.classList.toggle('text-gray-800', active);
            el.classList.toggle('dark:text-white', active);
            el.classList.toggle('border-gray-200', !active);
            el.classList.toggle('dark:border-gray-700', !active);
            el.classList.toggle('text-gray-500', !active);
            el.classList.toggle('dark:text-gray-400', !active);
        });
        generateReport();
        switchPane('dashboard');
    }

    function switchPane(pane) {
        const isDashboard = pane === 'dashboard';
        document.getElementById('pane-dashboard').classList.toggle('hidden', !isDashboard);
        document.getElementById('pane-preview').classList.toggle('hidden', isDashboard);
        const paint = (el, on) => {
            el.classList.toggle('bg-white', on);
            el.classList.toggle('dark:bg-gray-600', on);
            el.classList.toggle('text-gray-800', on);
            el.classList.toggle('dark:text-white', on);
            el.classList.toggle('shadow-sm', on);
            el.classList.toggle('text-gray-500', !on);
            el.classList.toggle('dark:text-gray-400', !on);
        };
        paint(document.getElementById('pane-tab-dashboard'), isDashboard);
        paint(document.getElementById('pane-tab-preview'), !isDashboard);
    }

    function getFormData() {
        const form = document.getElementById('reportForm');
        const data = new FormData(form);
        const params = new URLSearchParams();
        data.forEach((v, k) => { if (v) params.append(k, v); });
        return params;
    }

    const ENDPOINTS = {
        quotations:   { generate: '{{ route('admin.reports.generate') }}',                preview: '{{ route('admin.reports.preview-pdf') }}',                csv: '{{ route('admin.reports.export-csv') }}',                excel: '{{ route('admin.reports.export-excel') }}',                pdf: '{{ route('admin.reports.export-pdf') }}' },
        stickers:     { generate: '{{ route('admin.reports.stickers') }}',                preview: '{{ route('admin.reports.stickers.preview-pdf') }}',       csv: '{{ route('admin.reports.stickers.export-csv') }}',       excel: '{{ route('admin.reports.stickers.export-excel') }}',       pdf: '{{ route('admin.reports.stickers.export-pdf') }}' },
        users:        { generate: '{{ route('admin.reports.users') }}',                   preview: '{{ route('admin.reports.users.preview-pdf') }}',          csv: '{{ route('admin.reports.users.export-csv') }}',          excel: '{{ route('admin.reports.users.export-excel') }}',          pdf: '{{ route('admin.reports.users.export-pdf') }}' },
        underwriters: { generate: '{{ route('admin.reports.underwriters') }}',            preview: '{{ route('admin.reports.underwriters.preview-pdf') }}',   csv: '{{ route('admin.reports.underwriters.export-csv') }}',   excel: '{{ route('admin.reports.underwriters.export-excel') }}',   pdf: '{{ route('admin.reports.underwriters.export-pdf') }}' },
    };

    const CHART_META = {
        quotations: [
            ['Volume Trend & Forecast', 'Actual monthly volume with a linear-regression projection for the next 3 months'],
            ['Premium by Underwriter', 'Aggregate premium contribution'],
            ['Status Distribution', 'Records by lifecycle status'],
            ['Valuation Mix', 'Vehicle value bands'],
            ['Underwriter Share', 'Share of total records'],
        ],
        stickers: [
            ['Volume Trend & Forecast', 'Actual monthly volume with a linear-regression projection for the next 3 months'],
            ['Premium by Underwriter', 'Aggregate premium contribution'],
            ['Status Distribution', 'Records by lifecycle status'],
            ['Valuation Mix', 'Vehicle value bands'],
            ['Underwriter Share', 'Share of total records'],
        ],
        users: [
            ['Activity Trend & Forecast', 'Monthly quotations and certificates generated by users'],
            ['Top Performers by Premium', 'Users ranked by generated premium'],
            ['Account Status', 'Active vs disabled accounts'],
            ['Activity by Branch', 'Users per branch'],
            ['Branch Share', 'Share of accounts by branch'],
        ],
        underwriters: [
            ['Activity Trend & Forecast', 'Monthly quotations and certificates across the portfolio'],
            ['Premium by Underwriter', 'Aggregate premium contribution'],
            ['Availability Status', 'Available vs disabled underwriters'],
            ['Rates Configured', 'Pricing rule items per underwriter'],
            ['Portfolio Share', 'Share of records by underwriter'],
        ],
    };

    function applyChartMeta(type) {
        const meta = CHART_META[type] || [];
        meta.forEach((m, i) => {
            const t = document.getElementById('chart' + (i + 1) + '-title');
            const dd = document.getElementById('chart' + (i + 1) + '-desc');
            if (t) t.textContent = m[0];
            if (dd) dd.textContent = m[1];
        });
    }

    function generateReport() {
        const params = getFormData();
        const endpoint = ENDPOINTS[currentTab].generate;

        fetch(endpoint, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: params
        })
        .then(r => r.json())
        .then(d => {
            document.getElementById('reportResults').classList.remove('hidden');
            renderReport(d);
        })
        .catch(() => {});
    }

    function renderReport(d) {
        applyChartMeta(currentTab);
        const a = d.analytics;
        if (currentTab === 'users') {
            renderUserReport(d);
        } else if (currentTab === 'underwriters') {
            renderUnderwriterReport(d);
        } else {
            renderKpis(a);
            renderTrend(a);
            renderCompany(a);
            renderStatus(a);
            renderValuation(a);
            renderShare(a);
            renderInsights(a);
            renderTable(currentTab === 'stickers' ? d.stickers : d.quotations);
        }
    }

    function previewUrl() {
        return ENDPOINTS[currentTab].preview + '?' + getFormData().toString();
    }

    function previewDocument() {
        document.getElementById('reportResults').classList.remove('hidden');
        switchPane('preview');
        document.getElementById('preview-title').textContent =
            (currentTab === 'stickers' ? 'Certificates Report' : 'Quotations Report') + ' — Preview';
        document.getElementById('previewEmpty').classList.add('hidden');
        const frame = document.getElementById('previewFrame');
        frame.classList.remove('hidden');
        frame.src = previewUrl();
    }

    function openPreviewInTab() {
        window.open(previewUrl(), '_blank');
    }

    function renderKpis(a) {
        const s = a.summary;
        const unit = currentTab === 'stickers' ? 'Stickers' : 'Quotations';
        const cards = [
            { eyebrow: 'Total ' + unit, value: fmtInt(s.total_count), sub: 'Records in scope', accent: '', cls: '' },
            { eyebrow: 'Total Premium', value: 'KES ' + fmtMoney(s.total_premium), sub: 'Aggregate premium', accent: 'linear-gradient(90deg,#34d399,#6ee7b7)', cls: 'text-emerald-500' },
            { eyebrow: 'Avg Premium', value: 'KES ' + fmtMoney(s.average_premium), sub: 'Mean per record', accent: 'linear-gradient(90deg,#818cf8,#a5b4fc)', cls: 'text-blue-500' },
            { eyebrow: 'Median Premium', value: 'KES ' + fmtMoney(s.median_premium), sub: '50th percentile', accent: 'linear-gradient(90deg,#f59e0b,#fcd34d)', cls: 'text-amber-500' },
            { eyebrow: 'Avg Valuation', value: 'KES ' + fmtMoney(s.average_valuation), sub: 'Mean vehicle value', accent: 'linear-gradient(90deg,#fb7185,#fda4af)', cls: 'text-rose-500' },
            currentTab === 'stickers'
                ? { eyebrow: 'Total Downloads', value: fmtInt(s.total_downloads), sub: 'Certificate downloads', accent: 'linear-gradient(90deg,#22d3ee,#67e8f9)', cls: 'text-cyan-500' }
                : { eyebrow: 'Forecast Next', value: fmtInt(Math.round(a.forecast.counts[0] || 0)), sub: a.forecast.labels[0] || 'Next period', accent: 'linear-gradient(90deg,#a78bfa,#c4b5fd)', cls: '' }
        ];
        document.getElementById('summaryCards').innerHTML = cards.map(c => `
            <div class="kpi-card p-4" style="background-color:#263B8B;border:1px solid rgba(255,255,255,0.12);">
                <div class="accent-bar" ${c.accent ? `style="background:${c.accent}"` : ''}></div>
                <p class="kpi-eyebrow text-blue-200/70">${c.eyebrow}</p>
                <p class="kpi-value mt-1 ${c.cls || 'text-white'}">${c.value}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block text-blue-200/70">${c.sub}</span>
            </div>`).join('');
    }

    function renderTrend(a) {
        const m = a.monthly, f = a.forecast;
        const labels = [...m.labels, ...f.labels];
        const actual = [...m.counts, ...f.labels.map(() => null)];
        const projected = [...m.counts.map((v, i) => i === m.counts.length - 1 ? v : null), ...f.counts];
        makeChart('reportTrendChart', {
            type: 'line',
            data: {
                labels,
                datasets: [
                    { label: 'Actual', data: actual, borderColor: '#1756c8', backgroundColor: 'rgba(23,86,200,.08)', fill: true, tension: .35, pointRadius: 2, borderWidth: 2 },
                    { label: 'Forecast', data: projected, borderColor: '#d4860e', backgroundColor: 'rgba(212,134,14,.06)', borderDash: [6, 4], fill: false, tension: .35, pointRadius: 2, borderWidth: 2 }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'top', labels: { font: { size: 10 }, boxWidth: 12 } } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.05)' }, ticks: { font: { size: 9 }, precision: 0 } },
                    x: { grid: { display: false }, ticks: { font: { size: 9 } } }
                }
            }
        });
    }

    function renderCompany(a) {
        const entries = Object.entries(a.byCompany).slice(0, 10);
        makeChart('reportCompanyChart', {
            type: 'bar',
            data: {
                labels: entries.map(e => e[0]),
                datasets: [{ label: 'Total Premium (KES)', data: entries.map(e => e[1].total_premium), backgroundColor: '#1756c8', borderRadius: 4, maxBarThickness: 22 }]
            },
            options: {
                indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => 'KES ' + fmtMoney(c.parsed.x) } } },
                scales: {
                    x: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.05)' }, ticks: { font: { size: 9 }, callback: v => fmtMoney(v) } },
                    y: { grid: { display: false }, ticks: { font: { size: 9 } } }
                }
            }
        });
    }

    function renderStatus(a) {
        const entries = Object.entries(a.byStatus);
        makeChart('reportStatusChart', {
            type: 'doughnut',
            data: {
                labels: entries.map(e => (e[0] || 'unknown').replace(/^\w/, c => c.toUpperCase())),
                datasets: [{ data: entries.map(e => e[1]), backgroundColor: PALETTE, borderWidth: 0 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '58%',
                plugins: { legend: { position: 'bottom', labels: { font: { size: 10 }, boxWidth: 10 } } }
            }
        });
    }

    function renderValuation(a) {
        const entries = Object.entries(a.valuationBuckets);
        makeChart('reportValuationChart', {
            type: 'bar',
            data: {
                labels: entries.map(e => e[0]),
                datasets: [{ label: 'Records', data: entries.map(e => e[1]), backgroundColor: ['#1756c8', '#7c3aed', '#0f7a65', '#d4860e', '#c0392b'], borderRadius: 4, maxBarThickness: 26 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.05)' }, ticks: { font: { size: 9 }, precision: 0 } },
                    x: { grid: { display: false }, ticks: { font: { size: 9 } } }
                }
            }
        });
    }

    function renderShare(a) {
        const entries = Object.entries(a.byCompany).slice(0, 8);
        makeChart('reportShareChart', {
            type: 'doughnut',
            data: {
                labels: entries.map(e => e[0]),
                datasets: [{ data: entries.map(e => e[1].count), backgroundColor: PALETTE, borderWidth: 0 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '55%',
                plugins: { legend: { position: 'right', labels: { font: { size: 10 }, boxWidth: 10 } } }
            }
        });
    }

    function renderInsights(a) {
        const el = document.getElementById('insightsList');
        const insights = a.insights || [];
        if (!insights.length) {
            el.innerHTML = '<li class="text-gray-400">No observations for the selected scope.</li>';
            return;
        }
        el.innerHTML = insights.map(i => `
            <li class="flex items-start gap-2">
                <svg class="w-3.5 h-3.5 mt-0.5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>${i}</span>
            </li>`).join('');
    }

    function renderTable(records) {
        document.getElementById('table-title').textContent = currentTab === 'stickers' ? 'Detailed Report - Stickers' : 'Detailed Report - Quotations';
        document.getElementById('table-count').textContent = (records ? records.length : 0) + ' records';
        if (!records || !records.length) {
            document.getElementById('reportTable').innerHTML = '<p class="p-5 text-center text-xs text-gray-400">No data found</p>';
            return;
        }
        const th = (label, align = 'left') => `<th class="px-5 py-2.5 text-${align} font-semibold text-gray-400 uppercase tracking-wider text-[10px]">${label}</th>`;
        let html = '<table class="w-full text-xs"><thead><tr class="bg-gray-50 dark:bg-gray-700/40">';

        if (currentTab === 'stickers') {
            html += th('#') + th('Company') + th('Reg. No') + th('Vehicle') + th('Premium', 'right') + th('Downloads', 'center') + th('Via', 'center') + th('Status', 'center') + th('Date', 'right') + '</tr></thead><tbody class="divide-y divide-gray-100 dark:divide-gray-700">';
            records.forEach(s => {
                html += `<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                    <td class="px-5 py-2.5 font-mono text-[10px] font-bold text-gray-800 dark:text-white">${s.sticker_number}</td>
                    <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">${s.company?.name || 'N/A'}</td>
                    <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">${s.vehicle_registration || '—'}</td>
                    <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">${s.car_make} ${s.car_model} (${s.car_year})</td>
                    <td class="px-5 py-2.5 text-right font-medium text-gray-800 dark:text-white">KES ${fmtMoney(s.premium)}</td>
                    <td class="px-5 py-2.5 text-center font-semibold text-gray-800 dark:text-white">${s.download_count || 0}</td>
                    <td class="px-5 py-2.5 text-center"><span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">${(s.created_via || 'web').toUpperCase()}</span></td>
                    <td class="px-5 py-2.5 text-center"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">${s.status}</span></td>
                    <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">${new Date(s.created_at).toLocaleDateString()}</td>
                </tr>`;
            });
        } else {
            html += th('#') + th('Username') + th('Company') + th('Registration') + th('Vehicle') + th('Valuation', 'right') + th('Premium', 'right') + th('Status', 'center') + th('Date', 'right') + '</tr></thead><tbody class="divide-y divide-gray-100 dark:divide-gray-700">';
            records.forEach(q => {
                html += `<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                    <td class="px-5 py-2.5 font-medium text-gray-800 dark:text-white">${q.quotation_number}</td>
                    <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">${q.user?.username || 'N/A'}</td>
                    <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">${q.company?.name || 'N/A'}</td>
                    <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">${q.car_registration || '—'}</td>
                    <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">${q.car_make} ${q.car_model} (${q.car_year})</td>
                    <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">${fmtMoney(q.car_valuation)}</td>
                    <td class="px-5 py-2.5 text-right font-medium text-gray-800 dark:text-white">KES ${fmtMoney(q.total_premium)}</td>
                    <td class="px-5 py-2.5 text-center"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">${q.status}</span></td>
                    <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">${new Date(q.created_at).toLocaleDateString()}</td>
                </tr>`;
            });
        }
        html += '</tbody></table>';
        document.getElementById('reportTable').innerHTML = html;
    }

    function exportReport(type) {
        const params = getFormData();
        window.location.href = ENDPOINTS[currentTab][type] + '?' + params.toString();
    }

    function kpiCard(eyebrow, value, sub, accent, valueClass) {
        return `<div class="kpi-card p-4">
            <div class="accent-bar" ${accent ? `style="background:${accent}"` : ''}></div>
            <p class="kpi-eyebrow">${eyebrow}</p>
            <p class="kpi-value mt-1 ${valueClass || ''}">${value}</p>
            <span class="kpi-delta neutral mt-1.5 inline-block">${sub}</span>
        </div>`;
    }

    function renderDistribution(dist, label) {
        const entries = Object.entries(dist || {}).slice(0, 8);
        makeChart('reportValuationChart', {
            type: 'bar',
            data: {
                labels: entries.map(e => e[0]),
                datasets: [{ label: label || 'Records', data: entries.map(e => e[1]), backgroundColor: ['#1756c8', '#7c3aed', '#0f7a65', '#d4860e', '#c0392b', '#0891b2', '#db2777', '#65a30d'], borderRadius: 4, maxBarThickness: 26 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.05)' }, ticks: { font: { size: 9 }, precision: 0 } },
                    x: { grid: { display: false }, ticks: { font: { size: 9 } } }
                }
            }
        });
    }

    function renderUserReport(d) {
        const a = d.analytics, s = a.summary;
        document.getElementById('summaryCards').innerHTML = [
            kpiCard('Total Users', fmtInt(s.total_users), 'Registered accounts', ''),
            kpiCard('Active Accounts', fmtInt(s.active_users), 'Enabled logins', 'linear-gradient(90deg,#34d399,#6ee7b7)', 'text-emerald-500'),
            kpiCard('With Activity', fmtInt(s.users_with_activity), 'In selected scope', 'linear-gradient(90deg,#818cf8,#a5b4fc)', 'text-blue-500'),
            kpiCard('Quotations', fmtInt(s.total_quotations), 'Created by users', 'linear-gradient(90deg,#0891b2,#67e8f9)', 'text-cyan-500'),
            kpiCard('Certificates', fmtInt(s.total_stickers), 'Issued by users', 'linear-gradient(90deg,#a78bfa,#c4b5fd)', 'text-violet-500'),
            kpiCard('Premium Generated', 'KES ' + fmtMoney(s.total_premium), 'Across all users', 'linear-gradient(90deg,#f59e0b,#fcd34d)', 'text-amber-500'),
        ].join('');
        renderTrend(a);
        renderCompany(a);
        renderStatus(a);
        renderDistribution(a.distribution, 'Users');
        renderShare(a);
        renderInsights(a);
        renderUserTable(d.users);
    }

    function renderUserTable(users) {
        document.getElementById('table-title').textContent = 'Detailed Report - Users';
        document.getElementById('table-count').textContent = (users ? users.length : 0) + ' users';
        if (!users || !users.length) {
            document.getElementById('reportTable').innerHTML = '<p class="p-5 text-center text-xs text-gray-400">No data found</p>';
            return;
        }
        const th = (label, align = 'left') => `<th class="px-5 py-2.5 text-${align} font-semibold text-gray-400 uppercase tracking-wider text-[10px]">${label}</th>`;
        let html = '<table class="w-full text-xs"><thead><tr class="bg-gray-50 dark:bg-gray-700/40">';
        html += th('Username') + th('Branch') + th('Role') + th('Status', 'center') + th('Quotations', 'right') + th('Certificates', 'right') + th('Premium', 'right') + th('Conversion', 'right') + '</tr></thead><tbody class="divide-y divide-gray-100 dark:divide-gray-700">';
        users.forEach(u => {
            const status = u.is_active
                ? '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Active</span>'
                : '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400">Inactive</span>';
            html += `<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                <td class="px-5 py-2.5 font-mono text-[10px] font-bold text-gray-800 dark:text-white">${u.username}</td>
                <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">${u.branch || '—'}</td>
                <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">${u.roles || '—'}</td>
                <td class="px-5 py-2.5 text-center">${status}</td>
                <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">${u.quotations_count}</td>
                <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">${u.stickers_count}</td>
                <td class="px-5 py-2.5 text-right font-medium text-gray-800 dark:text-white">KES ${fmtMoney(u.total_premium)}</td>
                <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">${Number(u.conversion).toFixed(1)}%</td>
            </tr>`;
        });
        html += '</tbody></table>';
        document.getElementById('reportTable').innerHTML = html;
    }

    function renderUnderwriterReport(d) {
        const a = d.analytics, s = a.summary;
        document.getElementById('summaryCards').innerHTML = [
            kpiCard('Underwriters', fmtInt(s.total_underwriters), 'In the portfolio', ''),
            kpiCard('Available', fmtInt(s.active_underwriters), 'Active & quotable', 'linear-gradient(90deg,#34d399,#6ee7b7)', 'text-emerald-500'),
            kpiCard('Unavailable', fmtInt(s.inactive_underwriters), 'Currently disabled', 'linear-gradient(90deg,#fb7185,#fda4af)', 'text-rose-500'),
            kpiCard('Pricing Configured', fmtInt(s.configured_underwriters), 'Have rates set', 'linear-gradient(90deg,#818cf8,#a5b4fc)', 'text-blue-500'),
            kpiCard('Rate Items', fmtInt(s.total_rates), 'Pricing rules in total', 'linear-gradient(90deg,#0891b2,#67e8f9)', 'text-cyan-500'),
            kpiCard('Total Premium', 'KES ' + fmtMoney(s.total_premium), 'Portfolio premium', 'linear-gradient(90deg,#f59e0b,#fcd34d)', 'text-amber-500'),
        ].join('');
        renderTrend(a);
        renderCompany(a);
        renderStatus(a);
        renderDistribution(a.rates, 'Rate items');
        renderShare(a);
        renderInsights(a);
        renderUnderwriterTable(d.underwriters);
    }

    function renderUnderwriterTable(underwriters) {
        document.getElementById('table-title').textContent = 'Detailed Report - Underwriters';
        document.getElementById('table-count').textContent = (underwriters ? underwriters.length : 0) + ' underwriters';
        if (!underwriters || !underwriters.length) {
            document.getElementById('reportTable').innerHTML = '<p class="p-5 text-center text-xs text-gray-400">No data found</p>';
            return;
        }
        const th = (label, align = 'left') => `<th class="px-5 py-2.5 text-${align} font-semibold text-gray-400 uppercase tracking-wider text-[10px]">${label}</th>`;
        let html = '<table class="w-full text-xs"><thead><tr class="bg-gray-50 dark:bg-gray-700/40">';
        html += th('Underwriter') + th('Availability', 'center') + th('Rates', 'right') + th('Categories') + th('Quotations', 'right') + th('Certificates', 'right') + th('Premium', 'right') + '</tr></thead><tbody class="divide-y divide-gray-100 dark:divide-gray-700">';
        underwriters.forEach(u => {
            const status = u.is_active
                ? '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Available</span>'
                : '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400">Unavailable</span>';
            html += `<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                <td class="px-5 py-2.5 font-medium text-gray-800 dark:text-white">${u.name}</td>
                <td class="px-5 py-2.5 text-center">${status}</td>
                <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">${u.rates_count}</td>
                <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">${u.categories || '—'}</td>
                <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">${u.quotations_count}</td>
                <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">${u.stickers_count}</td>
                <td class="px-5 py-2.5 text-right font-medium text-gray-800 dark:text-white">KES ${fmtMoney(u.total_premium)}</td>
            </tr>`;
        });
        html += '</tbody></table>';
        document.getElementById('reportTable').innerHTML = html;
    }

    document.addEventListener('DOMContentLoaded', function () {
        generateReport();
    });
</script>
@endpush
</x-app-layout>
