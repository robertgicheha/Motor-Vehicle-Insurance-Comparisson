<x-app-layout>
    <div class="space-y-6 fade-in">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Reports</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Generate and export quotation reports</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Report Filters</h3>
            <form id="reportForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company</label>
                    <select name="company_id" id="filter_company" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm">
                        <option value="">All Companies</option>
                        @foreach($companies as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Period</label>
                    <select name="period" id="filter_period" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm">
                        <option value="">All Time</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From</label>
                    <input type="date" name="date_from" id="filter_from"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">To</label>
                    <input type="date" name="date_to" id="filter_to"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm">
                </div>
            </form>
            <div class="flex flex-wrap gap-3 mt-4">
                <button onclick="generateReport()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">Generate Report</button>
                <button onclick="exportReport('csv')" class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition text-sm font-medium">Export CSV</button>
                <button onclick="exportReport('excel')" class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition text-sm font-medium">Export Excel</button>
            </div>
        </div>

        <div id="reportResults" class="hidden space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4" id="summaryCards"></div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quotations by Company</h3>
                    <canvas id="reportCompanyChart" height="200"></canvas>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Daily Trend</h3>
                    <canvas id="reportDailyChart" height="200"></canvas>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-5 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Detailed Report</h3>
                </div>
                <div class="overflow-x-auto" id="reportTable"></div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function getFormData() {
            const form = document.getElementById('reportForm');
            const data = new FormData(form);
            const params = new URLSearchParams();
            data.forEach((v, k) => { if(v) params.append(k, v); });
            return params;
        }

        function generateReport() {
            const params = getFormData();
            fetch('{{ route('admin.reports.generate') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: params
            })
            .then(r => r.json())
            .then(d => {
                document.getElementById('reportResults').classList.remove('hidden');
                renderSummary(d.summary);
                renderCompanyChart(d.by_company);
                renderDailyChart(d.by_day);
                renderTable(d.quotations);
            });
        }

        function renderSummary(s) {
            document.getElementById('summaryCards').innerHTML = `
                <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500">Total Quotations</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">${s.total_quotations}</p>
                </div>
                <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500">Total Premium</p>
                    <p class="text-2xl font-bold text-blue-600">KES ${Number(s.total_premium).toLocaleString()}</p>
                </div>
                <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500">Avg Premium</p>
                    <p class="text-2xl font-bold text-green-600">KES ${Number(s.average_premium).toLocaleString()}</p>
                </div>
                <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500">Avg Valuation</p>
                    <p class="text-2xl font-bold text-purple-600">KES ${Number(s.average_valuation).toLocaleString()}</p>
                </div>
            `;
        }

        function renderCompanyChart(data) {
            const labels = Object.keys(data);
            const counts = Object.values(data).map(v => v.count);
            new Chart(document.getElementById('reportCompanyChart'), {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Quotations',
                        data: counts,
                        backgroundColor: ['rgba(59,130,246,0.7)', 'rgba(139,92,246,0.7)'],
                        borderColor: ['rgb(59,130,246)', 'rgb(139,92,246)'],
                        borderWidth: 2,
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } }, x: { grid: { display: false } } }
                }
            });
        }

        function renderDailyChart(data) {
            const labels = Object.keys(data);
            const values = Object.values(data).map(v => v.count);
            new Chart(document.getElementById('reportDailyChart'), {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Quotations',
                        data: values,
                        borderColor: 'rgb(59,130,246)',
                        backgroundColor: 'rgba(59,130,246,0.1)',
                        fill: true,
                        tension: 0.4,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } }, x: { grid: { display: false } } }
                }
            });
        }

        function renderTable(quotations) {
            if (!quotations.length) {
                document.getElementById('reportTable').innerHTML = '<p class="p-4 text-center text-gray-500">No data found</p>';
                return;
            }
            let html = '<table class="w-full text-sm"><thead><tr class="bg-gray-50 dark:bg-gray-700/50">';
            html += '<th class="px-4 py-3 text-left">#</th><th class="px-4 py-3 text-left">User</th><th class="px-4 py-3 text-left">Company</th><th class="px-4 py-3 text-left">Vehicle</th><th class="px-4 py-3 text-right">Valuation</th><th class="px-4 py-3 text-right">Premium</th><th class="px-4 py-3 text-center">Status</th><th class="px-4 py-3 text-right">Date</th></tr></thead><tbody>';
            quotations.forEach(q => {
                html += `<tr class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/30">
                    <td class="px-4 py-3 font-medium">${q.quotation_number}</td>
                    <td class="px-4 py-3">${q.user.name}</td>
                    <td class="px-4 py-3">${q.company.name}</td>
                    <td class="px-4 py-3">${q.car_make} ${q.car_model}</td>
                    <td class="px-4 py-3 text-right">${Number(q.car_valuation).toLocaleString()}</td>
                    <td class="px-4 py-3 text-right font-medium">${Number(q.total_premium).toLocaleString()}</td>
                    <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-800">${q.status}</span></td>
                    <td class="px-4 py-3 text-right text-gray-500">${new Date(q.created_at).toLocaleDateString()}</td>
                </tr>`;
            });
            html += '</tbody></table>';
            document.getElementById('reportTable').innerHTML = html;
        }

        function exportReport(type) {
            const params = getFormData();
            const url = type === 'csv' ? '{{ route('admin.reports.export-csv') }}' : '{{ route('admin.reports.export-excel') }}';
            window.location.href = url + '?' + params.toString();
        }
    </script>
    @endpush
</x-app-layout>
