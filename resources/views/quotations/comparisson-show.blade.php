<x-app-layout>
<div class="max-w-7xl mx-auto space-y-5 pt-10 fade-in font-montserrat">

    {{-- ─── HERO ───────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm print:hidden">
        <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Motor Insurance</p>
        <h1 class="mt-0.5 text-2xl font-bold text-white tracking-tight">Compare All Insurers</h1>
        <p class="mt-1 text-xs text-blue-200">Side-by-side premium comparison across every underwriter that covers your vehicle. Pick one to proceed and save your quotation.</p>
    </div>

    <div x-data="compareAllApp()" x-init="init()">

        <div x-show="loading" class="text-center py-20">
            <svg class="w-8 h-8 mx-auto text-blue-500 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <p class="text-xs text-gray-400 mt-3">Comparing insurers...</p>
        </div>

        <template x-if="!loading && !data">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden p-10 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-4">No quotation data found</p>
                <p class="text-xs text-gray-400 mt-1">Please fill in the vehicle details form first.</p>
                <a href="{{ route('quotations.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 mt-4 text-xs font-semibold bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Go to Form
                </a>
            </div>
        </template>

        <template x-if="!loading && data">
        <div class="space-y-5">

            {{-- ─── CUSTOMER & VEHICLE SUMMARY ─────────────────────────── --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 print:hidden">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Customer</p>
                        <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Personal Details</h2>
                    </div>
                    <div class="p-5 space-y-3 text-xs">
                        <div class="flex justify-between items-start gap-3">
                            <span class="text-gray-400 shrink-0">Full Name</span>
                            <span class="font-medium text-gray-800 dark:text-white text-right" x-text="personalName"></span>
                        </div>
                        <div class="flex justify-between items-start gap-3">
                            <span class="text-gray-400 shrink-0">Phone</span>
                            <span class="font-medium text-gray-800 dark:text-white text-right" x-text="data.phone || '—'"></span>
                        </div>
                        <div class="flex justify-between items-start gap-3">
                            <span class="text-gray-400 shrink-0">Email</span>
                            <span class="font-medium text-gray-800 dark:text-white text-right break-all" x-text="data.email || '—'"></span>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Vehicle</p>
                        <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Vehicle Details</h2>
                    </div>
                    <div class="p-5 space-y-3 text-xs">
                        <div class="flex justify-between items-start gap-3">
                            <span class="text-gray-400 shrink-0">Registration</span>
                            <span class="font-medium text-gray-800 dark:text-white text-right" x-text="data.reg || '—'"></span>
                        </div>
                        <div class="flex justify-between items-start gap-3">
                            <span class="text-gray-400 shrink-0">Make / Model</span>
                            <span class="font-medium text-gray-800 dark:text-white text-right" x-text="((data.make || '') + ' ' + (data.model || '')).trim() || '—'"></span>
                        </div>
                        <div class="flex justify-between items-start gap-3">
                            <span class="text-gray-400 shrink-0">Year / Body</span>
                            <span class="font-medium text-gray-800 dark:text-white text-right" x-text="(data.year || '—') + ' · ' + (data.body || '—')"></span>
                        </div>
                        <div class="flex justify-between items-start gap-3">
                            <span class="text-gray-400 shrink-0">Color</span>
                            <span class="font-medium text-gray-800 dark:text-white text-right" x-text="data.color || '—'"></span>
                        </div>
                        <div class="flex justify-between items-start gap-3">
                            <span class="text-gray-400 shrink-0">Chassis No.</span>
                            <span class="font-medium text-gray-800 dark:text-white text-right" x-text="data.chassis || '—'"></span>
                        </div>
                        <div class="flex justify-between items-start gap-3">
                            <span class="text-gray-400 shrink-0">Engine No.</span>
                            <span class="font-medium text-gray-800 dark:text-white text-right" x-text="data.engine || '—'"></span>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Cover</p>
                        <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Valuation</h2>
                    </div>
                    <div class="p-5 space-y-3 text-xs">
                        <div class="flex justify-between items-start gap-3">
                            <span class="text-gray-400 shrink-0">Estimated Value</span>
                            <span class="font-medium text-gray-800 dark:text-white text-right" x-text="fmt(vehicleValue)"></span>
                        </div>
                        <div class="flex justify-between items-start gap-3">
                            <span class="text-gray-400 shrink-0">Cover Type</span>
                            <span class="font-medium text-gray-800 dark:text-white text-right" x-text="'Comprehensive (' + (vehicleType.charAt(0).toUpperCase() + vehicleType.slice(1)) + ')'"></span>
                        </div>
                        <div class="flex justify-between items-start gap-3">
                            <span class="text-gray-400 shrink-0">Frequency</span>
                            <span class="font-medium text-gray-800 dark:text-white text-right">Annually</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── COMPARISON TABLE ───────────────────────────────────── --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden print:hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Comparison</p>
                    <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Premium Comparison — All Insurers</h2>
                </div>
                <div class="p-6 overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="text-gray-400 uppercase tracking-wider text-[10px]">
                                <th class="text-left font-semibold pb-2">Description</th>
                                <template x-for="r in results" :key="r.company.slug">
                                    <th class="text-right font-semibold pb-2 px-3" x-text="r.company.name"></th>
                                </template>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr>
                                <td class="px-0 py-2">Estimated Vehicle Value</td>
                                <template x-for="r in results" :key="r.company.slug">
                                    <td class="px-3 py-2 text-right font-semibold tabular-nums" x-text="fmt(vehicleValue)"></td>
                                </template>
                            </tr>
                            <tr>
                                <td class="px-0 py-2">Premium Rate</td>
                                <template x-for="r in results" :key="r.company.slug">
                                    <td class="px-3 py-2 text-right font-semibold tabular-nums" x-text="Number(r.breakdown.premium_rate).toFixed(2) + '%'"></td>
                                </template>
                            </tr>
                            <tr>
                                <td class="px-0 py-2">Basic Annual Premium</td>
                                <template x-for="r in results" :key="r.company.slug">
                                    <td class="px-3 py-2 text-right font-semibold tabular-nums" x-text="fmt(r.breakdown.basic_premium)"></td>
                                </template>
                            </tr>
                            <tr>
                                <td class="px-0 py-2">PVT(Political Violence &amp; Terrorism Extension)</td>
                                <template x-for="r in results" :key="r.company.slug">
                                    <td class="px-3 py-2 text-right font-semibold tabular-nums" x-text="fmt(r.breakdown.pvt_premium)"></td>
                                </template>
                            </tr>
                            <tr x-show="results.some(r => r.breakdown.pll_total > 0)">
                                <td class="px-0 py-2">Passenger Legal Liability (PLL)</td>
                                <template x-for="r in results" :key="r.company.slug">
                                    <td class="px-3 py-2 text-right font-semibold tabular-nums" x-text="fmt(r.breakdown.pll_total)"></td>
                                </template>
                            </tr>
                            <tr>
                                <td class="px-0 py-2">Levis</td>
                                <template x-for="r in results" :key="r.company.slug">
                                    <td class="px-3 py-2 text-right font-semibold tabular-nums" x-text="fmt(r.breakdown.levis)"></td>
                                </template>
                            </tr>
                            <tr>
                                <td class="px-0 py-2">Stamp Duty</td>
                                <template x-for="r in results" :key="r.company.slug">
                                    <td class="px-3 py-2 text-right font-semibold tabular-nums" x-text="fmt(r.breakdown.stamp_duty)"></td>
                                </template>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-gray-200 dark:border-gray-600">
                                <td class="px-0 pt-3 font-bold text-gray-800 dark:text-white">Total Premium (incl. taxes)</td>
                                <template x-for="r in results" :key="r.company.slug">
                                    <td class="px-3 pt-3 text-right font-extrabold text-blue-600 dark:text-blue-400 tabular-nums" x-text="fmt(r.breakdown.total_premium)"></td>
                                </template>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- ─── ACTION BAR (combined document across all insurers) ──── --}}
            <div class="action-bar bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center gap-3 print:hidden">
                <span class="action-bar-label text-[11px] font-semibold uppercase tracking-widest text-gray-400 flex-1">Compare All — Combined Document</span>
                <button type="button" class="action-btn action-btn-download inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition" @click="submitCompareAllPdf('download', false)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Download All (PDF)
                </button>
                <button type="button" class="action-btn action-btn-print inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-semibold bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition" @click="submitCompareAllPdf('preview', true)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                    Print All
                </button>
                <button type="button" class="action-btn action-btn-preview inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-semibold bg-purple-600 hover:bg-purple-500 text-white rounded-lg transition" @click="submitCompareAllPdf('preview', true)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    Preview All
                </button>
            </div>

            {{-- ─── SELECT AN INSURER ──────────────────────────────────── --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden print:hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Actions</p>
                    <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Select an Insurer to Proceed</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
                    <template x-for="r in results" :key="r.company.slug">
                        <div class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-sm border-2 border-gray-200 dark:border-gray-700 p-5 text-center transition hover:-translate-y-0.5 hover:shadow-lg hover:border-blue-500 flex flex-col">
                            <span x-show="r.company.slug === cheapestSlug" class="absolute top-3 right-3 text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-emerald-500 text-white">Lowest Premium</span>
                            <div class="h-11 flex items-center justify-center mb-3">
                                <img :src="r.company.logo" :alt="r.company.name" class="max-h-10 max-w-[110px] object-contain" x-show="r.company.logo" onerror="this.style.display='none'">
                                <span class="text-xs font-extrabold tracking-wider text-blue-600" x-show="!r.company.logo" x-text="r.company.name"></span>
                            </div>
                            <div class="text-xs font-bold text-gray-800 dark:text-white mb-1" x-text="r.company.name"></div>
                            <div class="text-[11px] text-gray-400 mb-3.5">Rate: <strong x-text="Number(r.breakdown.premium_rate).toFixed(2) + '%'"></strong> of vehicle value</div>
                            <div class="text-lg font-extrabold text-blue-600 dark:text-blue-400 mb-3.5 tabular-nums" x-text="fmt(r.breakdown.total_premium)"></div>
                            <div class="text-left mb-4 pt-3 border-t border-gray-100 dark:border-gray-700 space-y-1.5" x-show="cardBenefits(r).length">
                                <p class="text-[9px] font-semibold uppercase tracking-wider text-gray-400 mb-1.5">Key Benefits</p>
                                <template x-for="b in cardBenefits(r)" :key="b">
                                    <div class="flex items-start gap-1.5 text-[10.5px] text-gray-600 dark:text-gray-300 leading-snug">
                                        <svg class="w-3 h-3 mt-0.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        <span x-text="b"></span>
                                    </div>
                                </template>
                            </div>
                            <button type="button" @click="selectCompany(r.company.slug)" class="mt-auto block w-full py-2.5 rounded-lg text-xs font-bold text-white transition bg-blue-600 hover:bg-blue-500">
                                Select &amp; Continue →
                            </button>
                        </div>
                    </template>
                </div>
            </div>

        </div>
        </template>

    </div>
</div>

@push('scripts')
<script>
function compareAllApp() {
    return {
        loading: true,
        data: null,
        results: [],
        urlOnBehalf: new URLSearchParams(window.location.search).get('on_behalf_of') || '',
        init() {
            try {
                var raw = sessionStorage.getItem('micQuoteData');
                if (raw) this.data = JSON.parse(raw);
            } catch (e) {}

            if (!this.data) {
                this.loading = false;
                return;
            }

            fetch('{{ route("quotations.compare-all.calculate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify({
                    vehicle_type: this.vehicleType,
                    car_valuation: this.vehicleValue,
                    num_passengers: Number(this.data.numPassengers || this.data.num_passengers) || 1,
                }),
            })
                .then(r => r.json())
                .then(res => { this.results = res.results || []; })
                .catch(() => { this.results = []; })
                .finally(() => { this.loading = false; });
        },
        get vehicleType() {
            return this.data?.vehicle_type || this.data?.vehicleType || 'private';
        },
        get vehicleValue() {
            return parseFloat((this.data?.value || '0').toString().replace(/,/g, '')) || 0;
        },
        get personalName() {
            if (!this.data) return '—';
            var first = this.data.first || this.data.first_name || this.data.firstName || '';
            var middle = this.data.middle || this.data.middle_name || '';
            var last = this.data.last || this.data.last_name || this.data.lastName || '';
            return [this.data.salutation, first, middle, last].filter(Boolean).join(' ') || '—';
        },
        fmt(n) {
            n = Number(n) || 0;
            return 'KES ' + n.toLocaleString('en-KE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        get cheapestSlug() {
            if (!this.results.length) return null;
            return this.results.reduce((min, r) => (
                !min || r.breakdown.total_premium < min.breakdown.total_premium ? r : min
            ), null)?.company.slug || null;
        },
        cardBenefits(r) {
            var b = r.benefits || {};
            var lines = [
                b.min_sum_insured ? 'Min. Sum Insured: ' + b.min_sum_insured : null,
                b.max_age ? 'Max Vehicle Age: ' + b.max_age : null,
                b.excess_protector ? 'Excess Protector: ' + b.excess_protector : null,
            ].filter(Boolean);
            return lines.slice(0, 3).map(t => t.length > 46 ? t.slice(0, 44) + '…' : t);
        },
        selectCompany(slug) {
            if (this.data) {
                this.data.insurer = slug;
                try { sessionStorage.setItem('micQuoteData', JSON.stringify(this.data)); } catch (e) {}
            }
            var params = '?insurer=' + encodeURIComponent(slug);
            if (this.urlOnBehalf) params += '&on_behalf_of=' + encodeURIComponent(this.urlOnBehalf);
            window.location.href = '{{ route("quotations.preview") }}' + params;
        },
        submitCompareAllPdf(mode, newTab) {
            if (!this.data) return;
            var d = this.data;
            var first = d.first || d.first_name || d.firstName || '';
            var middle = d.middle || d.middle_name || '';
            var last = d.last || d.last_name || d.lastName || '';
            var fields = {
                vehicle_type: this.vehicleType,
                car_valuation: this.vehicleValue,
                num_passengers: Number(d.numPassengers || d.num_passengers) || 1,
                salutation: d.salutation || '',
                first_name: first,
                middle_name: middle,
                last_name: last,
                phone: d.phone || '',
                email: d.email || '',
                car_registration: d.reg || '',
                chassis_number: d.chassis || '',
                engine_number: d.engine || '',
                car_make: d.make || '',
                car_model: d.model || '',
                body_type: d.body || '',
                car_year: d.year || '',
                color: d.color || '',
            };
            var routeUrl = mode === 'download'
                ? '{{ route("quotations.compare-all.pdf.download") }}'
                : '{{ route("quotations.compare-all.pdf.preview") }}';
            function doSubmit(token) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = routeUrl;
                if (newTab) form.target = '_blank';
                var csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = document.querySelector('meta[name="csrf-token"]')?.content || '';
                form.appendChild(csrf);
                if (token) {
                    var captcha = document.createElement('input');
                    captcha.type = 'hidden';
                    captcha.name = 'g-recaptcha-response';
                    captcha.value = token;
                    form.appendChild(captcha);
                }
                Object.keys(fields).forEach(function (key) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = fields[key];
                    form.appendChild(input);
                });
                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            }
            if (typeof grecaptcha !== 'undefined' && window.mvipRecaptchaSiteKey) {
                grecaptcha.ready(function () {
                    grecaptcha.execute(window.mvipRecaptchaSiteKey, { action: 'generate_quotation' })
                        .then(doSubmit)
                        .catch(function () { doSubmit(null); });
                });
            } else {
                doSubmit(null);
            }
        },
    };
}
</script>
@endpush

</x-app-layout>
