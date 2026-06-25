<x-app-layout>
<div class="space-y-6 pt-10 fade-in max-w-4xl mx-auto">

    {{-- ─── HERO ─────────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Motor Insurance</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Quotation Preview</h1>
                <p class="mt-0.5 text-xs text-slate-400">Review your quotation details before confirming.</p>
            </div>
            <a href="{{ route('quotations.create') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-medium border border-white/20 text-white/80 rounded-lg hover:bg-white/10 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Form
            </a>
        </div>
    </div>

    {{-- ─── PREVIEW CONTENT ───────────────────────────────────────────────── --}}
    <div x-data="previewApp()">
        <div x-show="loading" class="text-center py-20">
            <svg class="w-8 h-8 mx-auto text-blue-500 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <p class="text-xs text-gray-400 mt-3">Loading preview...</p>
        </div>

        <template x-if="!loading && !data">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden p-10 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-4">No quotation data found</p>
                <p class="text-xs text-gray-400 mt-1">Please fill in the form to generate a quotation.</p>
                <a href="{{ route('quotations.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 mt-4 text-xs font-semibold bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Go to Form
                </a>
            </div>
        </template>

        <template x-if="!loading && data">
            <div>
                {{-- Personal Details Card --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Personal Details</h2>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-5 text-xs">
                        <div>
                            <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Name</span>
                            <span class="block font-medium text-gray-800 dark:text-white mt-1" x-text="personalName"></span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Email</span>
                            <span class="block font-medium text-gray-800 dark:text-white mt-1" x-text="data.email"></span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Phone</span>
                            <span class="block font-medium text-gray-800 dark:text-white mt-1" x-text="data.phone"></span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Insurer</span>
                            <span class="block font-medium text-gray-800 dark:text-white mt-1" x-text="selectedCompanyName"></span>
                        </div>
                    </div>
                </div>

                {{-- Vehicle Details Card --}}
                <div class="mt-5 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Vehicle Details</h2>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-5 text-xs">
                        <div>
                            <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Make</span>
                            <span class="block font-medium text-gray-800 dark:text-white mt-1" x-text="data.make"></span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Model</span>
                            <span class="block font-medium text-gray-800 dark:text-white mt-1" x-text="data.model"></span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Year</span>
                            <span class="block font-medium text-gray-800 dark:text-white mt-1" x-text="data.year"></span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Registration</span>
                            <span class="block font-medium text-gray-800 dark:text-white mt-1" x-text="data.reg || '—'"></span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Body Type</span>
                            <span class="block font-medium text-gray-800 dark:text-white mt-1" x-text="data.body || '—'"></span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Color</span>
                            <span class="block font-medium text-gray-800 dark:text-white mt-1" x-text="data.color || '—'"></span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Chassis</span>
                            <span class="block font-medium text-gray-800 dark:text-white mt-1" x-text="data.chassis || '—'"></span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Value (KES)</span>
                            <span class="block font-medium text-gray-800 dark:text-white mt-1" x-text="fmt(parseFloat((data.value || '0').replace(/,/g, '')))"></span>
                        </div>
                    </div>
                </div>

                {{-- Premium Breakdown Card --}}
                <div class="mt-5 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Premium Breakdown</h2>
                    </div>
                    <div class="p-5">
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between py-1.5">
                                <span class="text-gray-500">Vehicle Value</span>
                                <span class="font-semibold text-gray-800 dark:text-white" x-text="fmt(vehicleValue)"></span>
                            </div>
                            <div class="flex justify-between py-1.5">
                                <span class="text-gray-500">Rate</span>
                                <span class="font-semibold text-gray-800 dark:text-white" x-text="(selectedCompany.rate * 100).toFixed(1) + '%'"></span>
                            </div>
                            <div class="flex justify-between py-1.5">
                                <span class="text-gray-500">Base Premium</span>
                                <span class="font-semibold text-gray-800 dark:text-white" x-text="fmt(basePremium)"></span>
                            </div>
                            <div class="flex justify-between py-1.5">
                                <span class="text-gray-500">IRA Levy (0.5%)</span>
                                <span class="font-semibold text-gray-800 dark:text-white" x-text="fmt(iraLevy)"></span>
                            </div>
                            <div class="flex justify-between py-2.5 border-t border-gray-100 dark:border-gray-700">
                                <span class="font-semibold text-gray-800 dark:text-white">Total Premium</span>
                                <span class="font-bold text-lg text-blue-600" x-text="fmt(totalPremium)"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Confirm Form --}}
                <form method="POST" action="{{ route('quotations.store') }}" class="mt-5">
                    @csrf
                    <input type="hidden" name="_signed" :value="data._signed">
                    <input type="hidden" name="car_make" :value="data.make">
                    <input type="hidden" name="car_model" :value="data.model">
                    <input type="hidden" name="car_year" :value="data.year">
                    <input type="hidden" name="car_registration" :value="data.reg">
                    <input type="hidden" name="car_valuation" :value="vehicleValue">
                    <input type="hidden" name="on_behalf_of" :value="data.userId">
                    <input type="hidden" name="company_id" :value="selectedCompany.id">
                    <input type="hidden" name="premium" :value="basePremium">
                    <input type="hidden" name="pvt_premium" value="0">
                    <input type="hidden" name="total_premium" :value="totalPremium">
                    <input type="hidden" name="excess_own_damage" value="0">
                    <input type="hidden" name="excess_theft" value="0">

                    <div class="flex items-center justify-between gap-3 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                        <a href="{{ route('quotations.create') }}" class="px-5 py-2.5 text-xs font-medium border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            ← Edit Details
                        </a>
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Confirm &amp; Save Quotation
                        </button>
                    </div>
                </form>
            </div>
        </template>
    </div>
</div>

@push('scripts')
<script>
window.MIC_COMPANIES_PREVIEW = @json($companies);

function previewApp() {
    return {
        loading: true,
        data: null,
        companies: window.MIC_COMPANIES_PREVIEW || [],
        init() {
            try {
                var raw = sessionStorage.getItem('micQuoteData');
                if (raw) {
                    this.data = JSON.parse(raw);
                }
            } catch(e) {}
            this.loading = false;
        },
        get vehicleValue() {
            return parseFloat((this.data?.value || '0').replace(/,/g, '')) || 0;
        },
        get selectedCompany() {
            if (!this.data) return { id: 0, rate: 0, taxRate: 0, name: '' };
            var slugMap = { 'GA': 'GA', 'BRITAM': 'BRITAM', 'SANLAM': 'SANLAM' };
            var slug = slugMap[this.data.insurer] || '';
            return this.companies.find(function(c) { return c.slug === slug; }) || this.companies[0] || { id: 0, rate: 0, taxRate: 0, name: '' };
        },
        get selectedCompanyName() {
            return this.selectedCompany.name || this.data?.insurer || '—';
        },
        get personalName() {
            if (!this.data) return '—';
            return [this.data.salutation, this.data.first, this.data.middle, this.data.last].filter(Boolean).join(' ');
        },
        get basePremium() {
            return this.vehicleValue * (this.selectedCompany.rate || 0);
        },
        get iraLevy() {
            return this.vehicleValue * (this.selectedCompany.taxRate || 0);
        },
        get totalPremium() {
            return this.basePremium + this.iraLevy;
        },
        fmt(n) {
            if (isNaN(n)) return '—';
            return 'KES ' + n.toLocaleString('en-KE', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
    };
}
</script>
@endpush
</x-app-layout>
