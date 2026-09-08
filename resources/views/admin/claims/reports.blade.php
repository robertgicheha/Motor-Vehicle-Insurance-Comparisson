<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Reporting</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Claim Reports</h1>
                <p class="mt-0.5 text-xs text-slate-400">Generate, compare, and export claim reports — per user, per underwriter, or across the whole system</p>
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
                <p class="kpi-eyebrow">Claim Types</p>
                <p class="kpi-value mt-1">{{ count($claimTypes) }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Own damage, TPO, etc.</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#818cf8,#a5b4fc)"></div>
                <p class="kpi-eyebrow">Users</p>
                <p class="kpi-value mt-1">{{ $users->count() }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Can file a claim</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#fb7185,#fda4af)"></div>
                <p class="kpi-eyebrow">Export Formats</p>
                <p class="kpi-value mt-1">3</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">PDF, Excel &amp; CSV</span>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Report Filters</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">Filter by underwriter, user, claim type, status, or period</p>
            </div>
            <button onclick="toggleCompareMode()" id="compareToggleBtn"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border border-purple-200 dark:border-purple-700 text-purple-700 dark:text-purple-400 rounded-lg hover:bg-purple-50 dark:hover:bg-purple-900/20 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                Compare Reports
            </button>
        </div>

        <div class="px-5 py-4 space-y-4">
            <form id="reportForm" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Underwriter</label>
                    <select name="company_id" id="filter_company"
                            class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">All Underwriters</option>
                        @foreach($companies as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">User</label>
                    <select name="user_id" id="filter_user"
                            class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">All Users</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Claim Type</label>
                    <select name="claim_type" id="filter_claim_type"
                            class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">All Types</option>
                        @foreach($claimTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Status</label>
                    <select name="status" id="filter_status"
                            class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
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
                <button onclick="previewReport()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Preview / Print
                </button>
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
                <div class="w-px h-5 bg-gray-200 dark:bg-gray-600 hidden sm:block"></div>
                <button onclick="toggleEmailBox()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border border-blue-200 dark:border-blue-700 text-blue-700 dark:text-blue-400 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Email Report
                </button>
            </div>

            {{-- Email report inline box --}}
            <div id="emailBox" class="hidden flex items-center gap-2 pt-2 border-t border-gray-100 dark:border-gray-700 mt-2">
                <input type="email" id="emailReportInput" placeholder="Recipient email address"
                       class="flex-1 min-w-0 px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
                <button onclick="sendReportEmail()" id="sendReportBtn" class="px-3 py-1.5 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition whitespace-nowrap">Send</button>
                <span id="emailReportStatus" class="text-[10px] text-gray-500 dark:text-gray-400"></span>
            </div>
        </div>
    </div>

    {{-- Compare panel --}}
    <div id="comparePanel" class="hidden bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-purple-200 dark:border-purple-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div>
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Compare Reports</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">Set up 2 or 3 filtered slices to compare side by side in one document</p>
            </div>
            <button onclick="addCompareSlot()" id="addCompareSlotBtn" class="inline-flex items-center gap-1 text-[11px] font-semibold text-purple-600 hover:text-purple-800 dark:text-purple-400">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Slot
            </button>
        </div>
        <div class="p-5 space-y-3" id="compareSlots"></div>
        <div class="px-5 pb-5 flex items-center gap-2">
            <button onclick="runCompare()" class="px-4 py-1.5 text-xs font-semibold bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">Run Comparison</button>
            <button onclick="downloadComparePdf()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border border-purple-200 dark:border-purple-700 text-purple-700 dark:text-purple-400 rounded-lg hover:bg-purple-50 dark:hover:bg-purple-900/20 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download Comparison PDF
            </button>
        </div>
        <div id="compareResults" class="hidden px-5 pb-5"></div>
    </div>

    {{-- Report results --}}
    <div id="reportResults" class="hidden space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3" id="summaryCards"></div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5" style="max-width:380px">
                <h3 class="text-xs font-semibold text-gray-800 dark:text-white mb-3">Claims by Underwriter</h3>
                <canvas id="companyChart" style="max-height:200px"></canvas>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="text-xs font-semibold text-gray-800 dark:text-white mb-3">Daily Trend</h3>
                <canvas id="dailyChart" height="200" style="max-height:200px"></canvas>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5" style="max-width:380px">
                <h3 class="text-xs font-semibold text-gray-800 dark:text-white mb-3">Claims by Status</h3>
                <canvas id="statusChart" style="max-height:200px"></canvas>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="text-xs font-semibold text-gray-800 dark:text-white mb-3">Claims by Type</h3>
                <canvas id="typeChart" height="200" style="max-height:200px"></canvas>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-xs font-semibold text-gray-800 dark:text-white">Detailed Report</h3>
                <span class="text-[10px] text-gray-400">Claim data</span>
            </div>
            <div class="overflow-x-auto" id="reportTable"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let charts = {};
    let compareSlotCount = 0;

    function getFormData() {
        const form = document.getElementById('reportForm');
        const data = new FormData(form);
        const params = new URLSearchParams();
        data.forEach((v, k) => { if (v) params.append(k, v); });
        return params;
    }

    function generateReport() {
        const params = getFormData();
        fetch('{{ route("admin.claims.reports.generate") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: params
        })
        .then(r => r.json())
        .then(d => {
            document.getElementById('reportResults').classList.remove('hidden');
            renderReport(d);
        })
        .catch(() => { if (typeof showToastError === 'function') showToastError('Failed to generate report.'); });
    }

    function renderReport(d) {
        document.getElementById('summaryCards').innerHTML = `
            <div class="kpi-card p-4">
                <div class="accent-bar"></div>
                <p class="kpi-eyebrow">Total Claims</p>
                <p class="kpi-value mt-1">${d.summary.total_claims}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Logged claims</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#34d399,#6ee7b7)"></div>
                <p class="kpi-eyebrow">Total Settlement</p>
                <p class="kpi-value mt-1 text-emerald-500">KES ${Number(d.summary.total_settlement).toLocaleString()}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Total settled amount</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#818cf8,#a5b4fc)"></div>
                <p class="kpi-eyebrow">Avg Settlement</p>
                <p class="kpi-value mt-1 text-blue-500">KES ${Number(d.summary.average_settlement).toLocaleString()}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Average per settled claim</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#fb7185,#fda4af)"></div>
                <p class="kpi-eyebrow">Police Notified</p>
                <p class="kpi-value mt-1 text-rose-500">${d.summary.police_notified_count}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Claims with police involved</span>
            </div>
        `;

        renderChart('companyChart', 'company', 'doughnut', Object.keys(d.by_company), Object.values(d.by_company).map(v => v.count), 'Claims');
        renderChart('dailyChart', 'daily', 'line', Object.keys(d.by_day), Object.values(d.by_day).map(v => v.count), 'Claims');
        renderChart('statusChart', 'status', 'doughnut', Object.keys(d.by_status), Object.values(d.by_status).map(v => v.count), 'Claims');
        renderChart('typeChart', 'type', 'bar', Object.keys(d.by_claim_type), Object.values(d.by_claim_type).map(v => v.count), 'Claims');

        renderTable(d.claims);
    }

    function renderChart(canvasId, key, type, labels, data, label) {
        if (charts[key]) charts[key].destroy();
        const ctx = document.getElementById(canvasId).getContext('2d');
        const options = {
            responsive: true,
            maintainAspectRatio: type !== 'line',
            plugins: { legend: { display: type !== 'line' && type !== 'bar', position: 'bottom', labels: { font: { size: 10 } } } },
            scales: type === 'line' ? { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } }, x: { grid: { display: false } } }
                   : (type === 'bar' ? { y: { beginAtZero: true }, x: { grid: { display: false } } } : {}),
        };
        charts[key] = new Chart(ctx, {
            type: type,
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: data,
                    backgroundColor: (type === 'doughnut' || type === 'bar') ? ['#1756c8','#7c3aed','#0f7a65','#d4860e','#c0392b','#0891b2'] : 'rgba(59,130,246,0.1)',
                    borderColor: type === 'line' ? 'rgb(59,130,246)' : (type === 'bar' ? '#1756c8' : ['#1756c8','#7c3aed','#0f7a65','#d4860e','#c0392b','#0891b2']),
                    borderWidth: type === 'line' ? 2 : 0,
                    fill: type === 'line',
                    tension: type === 'line' ? 0.4 : 0,
                }]
            },
            options: options,
        });
    }

    function renderTable(claims) {
        if (!claims.length) {
            document.getElementById('reportTable').innerHTML = '<p class="p-5 text-center text-xs text-gray-400">No claims found for the selected filters.</p>';
            return;
        }
        let html = '<table class="w-full text-xs"><thead><tr class="bg-gray-50 dark:bg-gray-700/40">';
        ['Claim #', 'Filed By', 'Customer', 'Registration', 'Type', 'Status', 'Date'].forEach(h => {
            html += `<th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">${h}</th>`;
        });
        html += '</tr></thead><tbody class="divide-y divide-gray-100 dark:divide-gray-700">';
        claims.forEach(c => {
            html += `<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                <td class="px-5 py-2.5 font-medium text-gray-800 dark:text-white">${c.claim_number}</td>
                <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">${c.user?.name || 'N/A'}</td>
                <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">${c.on_behalf_of_user?.name || c.customer_name}</td>
                <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">${c.registration_number}</td>
                <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">${c.claim_type || '—'}</td>
                <td class="px-5 py-2.5"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">${c.status}</span></td>
                <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">${new Date(c.created_at).toLocaleDateString()}</td>
            </tr>`;
        });
        html += '</tbody></table>';
        document.getElementById('reportTable').innerHTML = html;
    }

    function exportReport(type) {
        const params = getFormData();
        const endpoints = {
            csv: '{{ route("admin.claims.reports.export-csv") }}',
            excel: '{{ route("admin.claims.reports.export-excel") }}',
            pdf: '{{ route("admin.claims.reports.export-pdf") }}',
        };
        window.location.href = endpoints[type] + '?' + params.toString();
    }

    function previewReport() {
        const params = getFormData();
        window.open('{{ route("admin.claims.reports.preview-pdf") }}?' + params.toString(), '_blank');
    }

    // --- Email report ---
    function toggleEmailBox() {
        document.getElementById('emailBox').classList.toggle('hidden');
    }

    function sendReportEmail() {
        const email = document.getElementById('emailReportInput').value.trim();
        const statusEl = document.getElementById('emailReportStatus');
        if (!email) { statusEl.textContent = 'Enter a recipient email first.'; return; }

        const btn = document.getElementById('sendReportBtn');
        btn.disabled = true;
        statusEl.textContent = 'Sending...';

        const params = getFormData();
        params.append('email', email);

        fetch('{{ route("admin.claims.reports.email") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: params,
        })
        .then(r => r.json())
        .then(d => { statusEl.textContent = d.success ? (d.message || 'Sent.') : (d.error || 'Failed to send.'); })
        .catch(() => { statusEl.textContent = 'Failed to send.'; })
        .finally(() => { btn.disabled = false; });
    }

    // --- Compare ---
    function toggleCompareMode() {
        const panel = document.getElementById('comparePanel');
        panel.classList.toggle('hidden');
        if (!panel.classList.contains('hidden') && compareSlotCount === 0) {
            addCompareSlot();
            addCompareSlot();
        }
    }

    function addCompareSlot() {
        if (compareSlotCount >= 3) return;
        compareSlotCount++;
        const id = compareSlotCount;
        const div = document.createElement('div');
        div.className = 'compare-slot border border-gray-200 dark:border-gray-600 rounded-xl p-3';
        div.dataset.slot = id;
        div.innerHTML = `
            <div class="flex items-center gap-2 mb-2">
                <input type="text" class="compare-label flex-1 min-w-0 px-2.5 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200" placeholder="Label, e.g. Sanlam Allianz" value="Set ${id}">
                ${compareSlotCount > 2 ? '<button onclick="this.closest(\\'.compare-slot\\').remove(); compareSlotCount--;" class="text-red-500 text-xs">&times;</button>' : ''}
            </div>
            <div class="grid grid-cols-2 gap-2">
                <select class="compare-company px-2.5 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                    <option value="">All Underwriters</option>
                    @foreach($companies as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
                <select class="compare-period px-2.5 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                    <option value="">All Time</option>
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                </select>
            </div>
        `;
        document.getElementById('compareSlots').appendChild(div);
        if (compareSlotCount >= 3) document.getElementById('addCompareSlotBtn').classList.add('hidden');
    }

    function collectCompareFilters() {
        return [...document.querySelectorAll('.compare-slot')].map(slot => ({
            label: slot.querySelector('.compare-label').value || 'Untitled',
            company_id: slot.querySelector('.compare-company').value,
            period: slot.querySelector('.compare-period').value,
        }));
    }

    function runCompare() {
        const filters = collectCompareFilters();
        if (filters.length < 2) { if (typeof showToastError === 'function') showToastError('Add at least 2 slots to compare.'); return; }

        fetch('{{ route("admin.claims.reports.compare") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ filters }),
        })
        .then(r => r.json())
        .then(d => renderCompareResults(d.results))
        .catch(() => { if (typeof showToastError === 'function') showToastError('Comparison failed.'); });
    }

    function renderCompareResults(results) {
        const el = document.getElementById('compareResults');
        el.classList.remove('hidden');
        let html = '<div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-2">';
        results.forEach(r => {
            html += `<div class="border border-purple-200 dark:border-purple-700 rounded-xl p-4">
                <p class="text-xs font-bold text-purple-700 dark:text-purple-400 mb-2">${r.label}</p>
                <div class="space-y-1 text-[11px]">
                    <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Total Claims</span><span class="font-semibold text-gray-800 dark:text-white">${r.summary.total_claims}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Total Settlement</span><span class="font-semibold text-gray-800 dark:text-white">KES ${Number(r.summary.total_settlement).toLocaleString()}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Avg Settlement</span><span class="font-semibold text-gray-800 dark:text-white">KES ${Number(r.summary.average_settlement).toLocaleString()}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Police Notified</span><span class="font-semibold text-gray-800 dark:text-white">${r.summary.police_notified_count}</span></div>
                </div>
            </div>`;
        });
        html += '</div>';
        el.innerHTML = html;
    }

    function downloadComparePdf() {
        const filters = collectCompareFilters();
        if (filters.length < 2) { if (typeof showToastError === 'function') showToastError('Add at least 2 slots to compare.'); return; }

        // A POST that triggers a file download can't be a plain fetch — build
        // a throwaway form and submit it so the browser handles the response
        // as a normal download.
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.claims.reports.compare-pdf") }}';
        form.style.display = 'none';

        const csrf = document.createElement('input');
        csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);

        filters.forEach((f, i) => {
            Object.entries(f).forEach(([k, v]) => {
                const input = document.createElement('input');
                input.name = `filters[${i}][${k}]`;
                input.value = v;
                form.appendChild(input);
            });
        });

        document.body.appendChild(form);
        form.submit();
        form.remove();
    }
</script>
@endpush
</x-app-layout>
