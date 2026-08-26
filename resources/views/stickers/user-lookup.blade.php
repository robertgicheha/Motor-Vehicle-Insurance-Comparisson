<x-app-layout>
<div class="space-y-6 pt-10 fade-in" x-data="{
    number: '',
    loading: false,
    result: null,
    error: null,
    search() {
        if (!this.number || this.number.length < 2) return;
        this.loading = true;
        this.error = null;
        this.result = null;
        fetch('{{ route('stickers.search-quotation') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
            },
            body: JSON.stringify({ search: this.number })
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) { this.error = data.error; return; }
            if (data.success && data.quotations.length > 0) {
                this.result = data.quotations[0];
            } else {
                this.error = 'No quotation found matching your search.';
            }
        })
        .catch(() => { this.error = 'Failed to look up. Please try again.'; })
        .finally(() => { this.loading = false; });
    }
}">

    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Certificates</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Certificate Tracker</h1>
                <p class="mt-0.5 text-xs text-slate-400">Search by quotation number, customer name, or vehicle registration</p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
        <div class="flex items-end gap-3">
            <div class="flex-1">
                <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Search</label>
                <input type="text" x-model="number" @keydown.enter="search" placeholder="Quotation number, name, or registration..."
                       class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <button @click="search" :disabled="!number || loading"
                    class="inline-flex items-center gap-1.5 px-5 py-2 text-xs font-semibold bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-lg transition shrink-0">
                <span x-show="!loading">Search</span>
                <span x-show="loading" class="inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    Searching...
                </span>
            </button>
        </div>
    </div>

    <div x-show="error" x-cloak
         class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-red-200 dark:border-red-800 p-5">
        <div class="flex items-center gap-2 text-xs font-medium text-red-600 dark:text-red-400">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span x-text="error"></span>
        </div>
    </div>

    <div x-show="result" x-cloak
         class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div>
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Quotation Details</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">Found matching quotation</p>
            </div>
            <a :href="`/quotations/${result.id}`"
               class="inline-flex items-center gap-1 text-[10px] font-medium text-blue-600 hover:text-blue-700">
                View Quotation
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
            </a>
        </div>

        <div class="px-5 py-4 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Quotation #</p>
                <p class="text-xs font-bold text-gray-900 dark:text-white mt-0.5" x-text="result.quotation_number"></p>
            </div>
            <div>
                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Insurance Company</p>
                <p class="text-xs font-semibold text-gray-800 dark:text-white mt-0.5 flex items-center gap-1.5">
                    <span class="w-5 h-5 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-[8px] font-bold shrink-0" x-text="(result.company || '?').charAt(0)"></span>
                    <span x-text="result.company"></span>
                </p>
            </div>
            <div>
                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Status</p>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium mt-0.5"
                      :class="result.status === 'completed'
                          ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                          : 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'">
                    <span class="w-1 h-1 rounded-full"
                          :class="result.status === 'completed'
                              ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                    <span x-text="result.status.charAt(0).toUpperCase() + result.status.slice(1)"></span>
                </span>
            </div>
            <div>
                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Date</p>
                <p class="text-xs font-medium text-gray-700 dark:text-gray-300 mt-0.5" x-text="result.created_at"></p>
            </div>
        </div>

        <div class="px-5 py-4 bg-gray-50 dark:bg-gray-700/20 border-t border-gray-100 dark:border-gray-700">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Vehicle</p>
                    <p class="text-xs font-semibold text-gray-800 dark:text-white mt-0.5">
                        <span x-text="result.car_make"></span> <span x-text="result.car_model"></span> (<span x-text="result.car_year"></span>)
                    </p>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Registration</p>
                    <p class="text-xs font-semibold text-gray-800 dark:text-white mt-0.5" x-text="result.car_registration || 'N/A'"></p>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Valuation</p>
                    <p class="text-xs font-semibold text-gray-800 dark:text-white mt-0.5">
                        KES <span x-text="Number(result.car_valuation).toLocaleString('en-KE', { minimumFractionDigits: 2 })"></span>
                    </p>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Premium</p>
                    <p class="text-xs font-bold text-blue-600 dark:text-blue-400 mt-0.5">
                        KES <span x-text="Number(result.total_premium || result.premium).toLocaleString('en-KE', { minimumFractionDigits: 2 })"></span>
                    </p>
                </div>
            </div>
        </div>

        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700">
            <template x-if="result.underwriter_email">
                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mb-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>Underwriter: <span x-text="result.underwriter_email" class="font-medium text-gray-700 dark:text-gray-300"></span></span>
                </div>
            </template>
            <a :href="`/stickers/issue?quotation_id=${result.id}`"
               class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Issue Certificate
            </a>
        </div>
    </div>
</div>
</x-app-layout>
