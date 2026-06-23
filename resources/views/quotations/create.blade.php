<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6 fade-in">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Generate Quotation</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Enter vehicle details to compare insurance premiums</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <form id="quotationForm" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Car Make</label>
                        <input type="text" name="car_make" id="car_make" required
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Car Model</label>
                        <input type="text" name="car_model" id="car_model" required
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Year of Manufacture</label>
                        <input type="number" name="car_year" id="car_year" min="2000" max="2030" value="{{ date('Y') }}" required
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Registration Number (Optional)</label>
                        <input type="text" name="car_registration" id="car_registration"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Car Valuation (KES) <span class="text-xs text-gray-400">(Min: 800,000 - Max: 1,300,000)</span>
                        </label>
                        <input type="number" name="car_valuation" id="car_valuation" min="800000" max="1300000" step="1000" required
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                               placeholder="e.g. 1000000">
                        <div class="mt-2">
                            <input type="range" id="valuationSlider" min="800000" max="1300000" step="1000" value="1000000"
                                   class="w-full h-2 bg-gray-200 dark:bg-gray-600 rounded-lg appearance-none cursor-pointer accent-blue-600">
                            <div class="flex justify-between text-xs text-gray-400 mt-1">
                                <span>KES 800,000</span>
                                <span id="valuationDisplay" class="font-medium text-blue-600 dark:text-blue-400">KES 1,000,000</span>
                                <span>KES 1,300,000</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-6 flex-wrap">
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <input type="checkbox" name="include_pvt" value="1" checked
                               class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                        Include PVT
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <input type="checkbox" name="has_anti_theft" value="1" checked
                               class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                        Has Anti-Theft Device
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <input type="checkbox" name="is_young_driver" value="1"
                               class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                        Young/Novice Driver
                    </label>
                </div>

                <button type="button" onclick="calculatePremium()"
                        class="w-full py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition font-medium">
                    Calculate Premiums
                </button>
            </form>
        </div>

        <div id="results" class="hidden space-y-4"></div>

        <div id="comparisonTable" class="hidden bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Coverage Comparison</h2>
            </div>
            <div class="overflow-x-auto" id="coverageComparison"></div>
        </div>
    </div>

    @push('scripts')
    <script>
        const slider = document.getElementById('valuationSlider');
        const valuationInput = document.getElementById('car_valuation');
        const valuationDisplay = document.getElementById('valuationDisplay');

        slider.addEventListener('input', () => {
            valuationInput.value = slider.value;
            valuationDisplay.textContent = 'KES ' + Number(slider.value).toLocaleString();
        });

        valuationInput.addEventListener('input', () => {
            slider.value = valuationInput.value;
            valuationDisplay.textContent = 'KES ' + Number(valuationInput.value).toLocaleString();
        });

        const coverLimits = {
            'sanlam-allianz': {
                'Windscreen / Glass': 50000,
                'Car Entertainment': 50000,
                'Towing & Recovery Expenses': 50000,
                'Authorized Repair': 50000,
                'Medical Expenses': 50000,
            },
            'ga-insurance': {
                'Windscreen / Glass': 75000,
                'Car Entertainment': 75000,
                'Towing & Recovery Expenses': 75000,
                'Authorized Repair': 100000,
                'Medical Expenses': 50000,
            }
        };

        function calculatePremium() {
            const form = document.getElementById('quotationForm');
            const formData = new FormData(form);

            document.getElementById('results').classList.add('hidden');
            document.getElementById('comparisonTable').classList.add('hidden');

            fetch('{{ route('quotations.calculate') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.errors) {
                    alert(Object.values(data.errors).flat().join('\n'));
                    return;
                }
                showResults(data.results);
            })
            .catch(err => {
                alert('Error calculating premiums. Please try again.');
                console.error(err);
            });
        }

        function showResults(results) {
            const container = document.getElementById('results');
            container.classList.remove('hidden');
            container.innerHTML = '<h2 class="text-xl font-bold text-gray-900 dark:text-white">Premium Comparison</h2>';

            const companies = @json(\App\Models\Company::active()->get());
            const companyNames = {};
            companies.forEach(c => { companyNames[c.slug] = c.name; });

            results.forEach((r, idx) => {
                const colors = ['from-blue-500 to-indigo-600', 'from-emerald-500 to-teal-600'];
                const color = colors[idx % colors.length];
                const companyKey = Object.keys(companyNames).find(k => companyNames[k] === r.company) || r.company.toLowerCase().replace(/\s+/g, '-');

                container.innerHTML += `
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden card-hover">
                        <div class="bg-gradient-to-r ${color} p-5 text-white">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold">${r.company}</h3>
                                <span class="text-sm opacity-90">Premium Rate: ${r.premium_rate}%</span>
                            </div>
                        </div>
                        <div class="p-5 grid grid-cols-2 md:grid-cols-3 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Valuation</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">KES ${Number(r.valuation).toLocaleString()}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Basic Premium</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">KES ${Number(r.premium).toLocaleString()}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">PVT (${r.pvt_rate}%)</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">KES ${Number(r.pvt_premium).toLocaleString()}</p>
                            </div>
                            <div class="col-span-2 md:col-span-3">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Total Premium</p>
                                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">KES ${Number(r.total_premium).toLocaleString()}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Excess - Own Damage</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">KES ${Number(r.excess_own_damage).toLocaleString()}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Excess - Theft</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">KES ${Number(r.excess_theft).toLocaleString()}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Young Driver Excess</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">KES ${Number(r.excess_young_driver).toLocaleString()}</p>
                            </div>
                        </div>
                        <div class="p-5 border-t border-gray-200 dark:border-gray-700">
                            <form action="{{ route('quotations.store') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="car_make" value="${document.getElementById('car_make').value}">
                                <input type="hidden" name="car_model" value="${document.getElementById('car_model').value}">
                                <input type="hidden" name="car_year" value="${document.getElementById('car_year').value}">
                                <input type="hidden" name="car_registration" value="${document.getElementById('car_registration').value}">
                                <input type="hidden" name="car_valuation" value="${r.valuation}">
                                <input type="hidden" name="company_id" value="${companies[idx].id}">
                                <input type="hidden" name="premium" value="${r.premium}">
                                <input type="hidden" name="pvt_premium" value="${r.pvt_premium}">
                                <input type="hidden" name="total_premium" value="${r.total_premium}">
                                <input type="hidden" name="excess_own_damage" value="${r.excess_own_damage}">
                                <input type="hidden" name="excess_theft" value="${r.excess_theft}">
                                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                                    Save This Quotation
                                </button>
                            </form>
                        </div>
                    </div>
                `;
            });

            showCoverageComparison(companies);
        }

        function showCoverageComparison(companies) {
            const container = document.getElementById('coverageComparison');
            const table = document.getElementById('comparisonTable');
            table.classList.remove('hidden');

            let html = '<table class="w-full text-sm"><thead><tr class="bg-gray-50 dark:bg-gray-700/50">';
            html += '<th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Cover Item</th>';
            companies.forEach(c => {
                html += `<th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">${c.name}</th>`;
            });
            html += '</tr></thead><tbody>';

            const coverItems = [
                'Windscreen / Glass', 'Car Entertainment', 'Towing & Recovery Expenses',
                'Authorized Repair', 'Medical Expenses',
                'Third Party Bodily Injury - Per Person',
                'Third Party Bodily Injury - Per Event',
                'Passenger Liability Limit - Any One Person',
                'Passenger Liability Limit - Any One Event',
                'Third Party Property Damage Limit'
            ];

            const allRates = @json(\App\Models\Rate::where('category', '!=', 'Premium Rate')->get()->groupBy('company_id'));

            coverItems.forEach(item => {
                html += `<tr class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/30">
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">${item}</td>`;
                companies.forEach(c => {
                    const rates = allRates[c.id] || [];
                    const rate = rates.find(r => r.item_name === item);
                    const val = rate ? (rate.value ? 'KES ' + Number(rate.value).toLocaleString() : (rate.percentage ? rate.percentage + '%' : '-')) : '-';
                    html += `<td class="px-4 py-3 text-right">${val}</td>`;
                });
                html += '</tr>';
            });

            html += '</tbody></table>';
            container.innerHTML = html;
        }
    </script>
    @endpush
</x-app-layout>
