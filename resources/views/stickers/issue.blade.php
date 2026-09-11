<x-app-layout>
<div class="space-y-6 pt-10 fade-in" x-data="certificateIssue()">

    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Sticker Issuance</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Issue Sticker</h1>
                <p class="mt-0.5 text-xs text-slate-400">Search for a quotation, upload KYC documents, and issue a sticker to the underwriter</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('stickers.user-lookup') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium border border-white/20 text-white/80 rounded-lg hover:bg-white/10 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Tracker
                </a>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-3 gap-3 max-w-lg">
            <div class="flex items-center gap-2" :class="step >= 1 ? 'opacity-100' : 'opacity-40'">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0 transition-all duration-300"
                     :class="step > 1 ? 'bg-emerald-500 text-white' : (step === 1 ? 'bg-white text-blue-700' : 'bg-white/20 text-white/60')">
                    <svg x-show="step > 1" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span x-show="step <= 1">1</span>
                </div>
                <span class="text-[11px] font-semibold text-white">Find Quotation</span>
            </div>
            <div class="flex items-center gap-2" :class="step >= 2 ? 'opacity-100' : 'opacity-40'">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0 transition-all duration-300"
                     :class="step > 2 ? 'bg-emerald-500 text-white' : (step === 2 ? 'bg-white text-blue-700' : 'bg-white/20 text-white/60')">
                    <svg x-show="step > 2" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span x-show="step <= 2">2</span>
                </div>
                <span class="text-[11px] font-semibold text-white">Upload Documents</span>
            </div>
            <div class="flex items-center gap-2" :class="step >= 3 ? 'opacity-100' : 'opacity-40'">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0 transition-all duration-300"
                     :class="step === 3 ? 'bg-white text-blue-700' : 'bg-white/20 text-white/60'">
                    3
                </div>
                <span class="text-[11px] font-semibold text-white">Issue Sticker</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Search Quotation</h2>
                        <p class="text-[10px] text-gray-400 mt-0.5">Search by quotation number, customer name, or vehicle registration</p>
                    </div>
                </div>
                <div class="p-5">
                    <div class="flex gap-3">
                        <div class="relative flex-1">
                            <input type="text" placeholder="e.g. QT-20250101-001, KCA 123B, John Doe..."
                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pl-9 pr-9"
                                x-ref="searchInput" @input="onSearchInput()" @keydown.enter="searchQuotation()">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <button x-show="$refs.searchInput.value.length > 0" @click="$refs.searchInput.value = ''; searchResults = []; searchError = ''" type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <button @click="searchQuotation()" :disabled="searching"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white rounded-lg transition shrink-0">
                            <svg x-show="!searching" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <svg x-show="searching" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span x-text="searching ? 'Searching...' : 'Search'"></span>
                        </button>
                    </div>

                    <div x-show="searchError" x-transition class="mt-3 flex items-center gap-2 text-xs text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded-lg px-3 py-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span x-text="searchError"></span>
                    </div>

                    <div x-show="searchResults.length > 0" class="mt-4 space-y-2">
                        <template x-for="q in searchResults" :key="q.id">
                            <div @click="selectQuotation(q)"
                                :class="selectedQuotation?.id === q.id ? 'ring-2 ring-blue-500 bg-blue-50 dark:bg-blue-900/20 border-blue-300 dark:border-blue-600' : 'bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 border-gray-200 dark:border-gray-600'"
                                class="p-3 rounded-xl border cursor-pointer transition-all duration-200">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-gray-800 dark:text-white" x-text="q.quotation_number"></span>
                                            <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300" x-text="q.status"></span>
                                            <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300" x-text="q.vehicle_type === 'psv' ? 'PSV' : 'Private'"></span>
                                        </div>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5" x-text="q.customer_name + ' - ' + q.car_make + ' ' + q.car_model + ' (' + q.car_year + ')'"></p>
                                        <p class="text-[10px] text-gray-400 dark:text-gray-500" x-text="q.company + (q.car_registration ? ' | Reg: ' + q.car_registration : '')"></p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="text-xs font-bold text-gray-800 dark:text-white">KES <span x-text="Number(q.total_premium).toLocaleString()"></span></p>
                                        <p class="text-[10px] text-gray-400" x-text="q.created_at"></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div x-show="searchResults.length === 0 && !searching && !searchError" class="mt-4 text-center py-6 text-gray-400 dark:text-gray-500 text-xs">
                        <svg class="w-8 h-8 mx-auto text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Type at least 3 characters to search by quotation number, name, or registration
                    </div>
                </div>
            </div>

            {{-- Cover Details: client type + cover period — captured before KYC
                 upload unlocks, since the underwriter needs this alongside the
                 documents themselves. --}}
            <div x-show="selectedQuotation" x-transition x-cloak
                 class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center" :class="coverDetailsSaved ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-blue-100 dark:bg-blue-900/30'">
                                <svg x-show="!coverDetailsSaved" class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <svg x-show="coverDetailsSaved" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Cover Details</h2>
                                <p class="text-[10px] text-gray-400 mt-0.5">Client type and period of cover — required before KYC upload</p>
                            </div>
                        </div>
                        <button x-show="coverDetailsSaved" @click="editCoverDetails()" type="button"
                            class="text-[10px] font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition">Edit</button>
                    </div>
                </div>

                <div x-show="!coverDetailsSaved" x-transition class="p-5 space-y-5">
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-700 dark:text-gray-300 mb-2">Client Type</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative flex items-center gap-2.5 p-3 rounded-xl border-2 cursor-pointer transition-all duration-200"
                                :class="clientType === 'individual' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-blue-300'">
                                <input type="radio" name="clientType" value="individual" x-model="clientType" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                                <div>
                                    <p class="text-xs font-semibold text-gray-800 dark:text-white">Individual</p>
                                    <p class="text-[9px] text-gray-400 dark:text-gray-500">Vehicle owned by a person</p>
                                </div>
                            </label>
                            <label class="relative flex items-center gap-2.5 p-3 rounded-xl border-2 cursor-pointer transition-all duration-200"
                                :class="clientType === 'corporate' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-blue-300'">
                                <input type="radio" name="clientType" value="corporate" x-model="clientType" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                                <div>
                                    <p class="text-xs font-semibold text-gray-800 dark:text-white">Corporate</p>
                                    <p class="text-[9px] text-gray-400 dark:text-gray-500">Vehicle under corporate ownership</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-[11px] font-semibold text-gray-700 dark:text-gray-300">Dates of Cover</label>
                            {{-- Conspicuous quick-action: 1-year cover starting today, one click. --}}
                            <button type="button" @click="startCoverFromToday()"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white rounded-lg shadow-sm transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                Start From Today (1 Year)
                            </button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-[1fr_1.5fr_1fr] gap-4">
                            <div>
                                <span class="block text-[10px] text-gray-500 dark:text-gray-400 mb-1">Policy Start Date</span>
                                <input type="date" x-model="coverStartDate" @change="onCoverInputsChanged()"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <span class="block text-[10px] text-gray-500 dark:text-gray-400 mb-1">Enter Duration</span>
                                <div class="flex gap-3">
                                    <select x-model="coverDurationUnit" @change="onDurationUnitChanged()"
                                        class="w-1/2 min-w-0 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="week">Week</option>
                                        <option value="month">Month</option>
                                        <option value="year">Year</option>
                                    </select>
                                    <select x-model.number="coverDurationValue" @change="onCoverInputsChanged()" x-show="coverDurationUnit !== 'year'"
                                        class="w-1/2 min-w-0 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <template x-for="n in (coverDurationUnit === 'week' ? 52 : 12)" :key="n">
                                            <option :value="n" x-text="n"></option>
                                        </template>
                                    </select>
                                    <div x-show="coverDurationUnit === 'year'" class="w-1/2 min-w-0 px-3 py-2 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400">1 Year</div>
                                </div>
                            </div>
                            <div>
                                <span class="block text-[10px] text-gray-500 dark:text-gray-400 mb-1">Policy End Date</span>
                                <div class="relative">
                                    <input type="text" readonly :value="coverEndDateDisplay || '—'"
                                        class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold cursor-not-allowed">
                                    <svg x-show="calculatingEndDate" class="animate-spin w-3.5 h-3.5 text-blue-500 absolute right-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                </div>
                                <p class="text-[9px] text-gray-400 mt-1">Calculated automatically</p>
                            </div>
                        </div>
                    </div>

                    <div x-show="coverDetailsError" x-transition class="flex items-center gap-2 text-xs text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded-lg px-3 py-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span x-text="coverDetailsError"></span>
                    </div>

                    <button @click="saveCoverDetails()" :disabled="savingCoverDetails" type="button"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white rounded-lg transition">
                        <svg x-show="!savingCoverDetails" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <svg x-show="savingCoverDetails" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span x-text="savingCoverDetails ? 'Saving...' : 'Save &amp; Continue to Documents'"></span>
                    </button>
                </div>

                <div x-show="coverDetailsSaved" x-transition class="px-5 py-4 flex flex-wrap gap-x-8 gap-y-2">
                    <div>
                        <p class="text-[9px] font-semibold uppercase tracking-wider text-gray-400">Client Type</p>
                        <p class="text-xs font-semibold text-gray-800 dark:text-white capitalize" x-text="clientType"></p>
                    </div>
                    <div>
                        <p class="text-[9px] font-semibold uppercase tracking-wider text-gray-400">Policy Period</p>
                        <p class="text-xs font-semibold text-gray-800 dark:text-white">
                            <span x-text="coverStartDate || 'Not set'"></span> &rarr; <span x-text="coverEndDateDisplay || 'Not set'"></span>
                        </p>
                    </div>
                </div>
            </div>

            <div x-show="selectedQuotation && coverDetailsSaved" x-transition x-cloak
                 class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Upload KYC Documents</h2>
                                <p class="text-[10px] text-gray-400 mt-0.5">Each document is scanned for security threats before acceptance</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-semibold text-gray-500 dark:text-gray-400" x-text="uploadedCount + '/3'"></span>
                            <div class="flex gap-1">
                                <template x-for="docType in (clientType === 'corporate' ? ['kra_pin', 'certificate_of_incorporation', 'logbook_import'] : ['id_copy', 'kra_pin', 'logbook'])" :key="'dot-'+docType">
                                     <div class="w-1.5 h-1.5 rounded-full transition-colors duration-300"
                                          :class="getSlotStatus(docType) === 'clean' ? 'bg-emerald-500' : (getSlotStatus(docType) === 'infected' ? 'bg-red-500' : ((getSlotStatus(docType) === 'pending' || getSlotStatus(docType) === 'scanning') ? 'bg-blue-500 animate-pulse' : 'bg-gray-300 dark:bg-gray-600'))"></div>
                                 </template>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-5">
                    <div class="flex items-start gap-2 mb-4 px-3 py-2.5 rounded-lg bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800">
                        <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-[10px] text-amber-800 dark:text-amber-400 leading-snug">Authenticity of the documents submitted will be subject to verification by the bank.</p>
                    </div>

                    @php
                        $__individualDocTypes = [
                            'id_copy' => ['Copy of ID', 'National ID, Passport, or License', 'M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2'],
                            'kra_pin' => ['KRA PIN Certificate', 'Kenya Revenue Authority PIN', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                            'logbook' => ['Vehicle Logbook', 'NTSA registered logbook', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        ];
                        $__corporateDocTypes = [
                            'kra_pin' => ['KRA PIN Certificate', 'Kenya Revenue Authority PIN', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                            'certificate_of_incorporation' => ['Certificate of Incorporation', 'Company registration certificate', 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                            'logbook_import' => ['Vehicle Logbook / Import Documents', 'NTSA logbook or import/customs documents', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        ];
                    @endphp

                    <div x-show="clientType === 'individual'" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($__individualDocTypes as $type => $info)
                            @include('stickers.partials.kyc-card', ['type' => $type, 'info' => $info])
                        @endforeach
                    </div>

                    {{-- Corporate: three fixed documents (KRA PIN + Certificate
                         of Incorporation + Vehicle Logbook / Import Documents).
                         The "Others" panel below is optional extra material,
                         same as it is for individual clients — not one of the
                         three required documents. --}}
                    <div x-show="clientType === 'corporate'" x-cloak class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($__corporateDocTypes as $type => $info)
                            @include('stickers.partials.kyc-card', ['type' => $type, 'info' => $info])
                        @endforeach
                    </div>

                        {{-- Optional extra documents, one row at a time — available for
                             both client types so anything beyond the fixed
                             required set (an extra ID, a fleet card, etc.) has
                             somewhere to go, each with its own label. For an
                             individual client whose logbook is marked N/A, this
                             same panel additionally becomes the required
                             replacement for that missing document. Each row is
                             its own independent upload — its own name field, its
                             own file, its own status — rather than a single
                             multi-file drop, so a partial batch (one uploaded,
                             one still picking a file) is never ambiguous. --}}
                        <div class="mt-4 border-2 border-dashed border-amber-200 dark:border-amber-700 rounded-xl p-4 bg-amber-50/40 dark:bg-amber-900/5">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <p class="text-[11px] font-semibold text-gray-800 dark:text-white" x-text="(clientType === 'individual' && logbookNA) ? 'Other Supporting Documents (Logbook Replacement)' : 'Additional Supporting Documents (Optional)'"></p>
                                    <p class="text-[9px] text-gray-400 dark:text-gray-500" x-text="(clientType === 'individual' && logbookNA) ? 'Provide at least one document in place of the logbook — e.g. Driver\'s Licence, Fleet Card' : 'Add any extra supporting documents the underwriter might need — give each one a label'"></p>
                                </div>
                                <button type="button" @click="addOtherRow()"
                                    class="inline-flex items-center gap-1 text-[10px] font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Add Document
                                </button>
                            </div>

                            <div class="space-y-2">
                                <template x-for="row in otherDocuments" :key="row.rowId">
                                    <div class="flex items-center gap-2 p-2 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600">
                                        <input type="text" x-model="row.label" placeholder="Document name, e.g. Driver's Licence"
                                            :disabled="!!row.documentId"
                                            class="flex-1 min-w-0 px-2.5 py-1.5 text-xs border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 disabled:opacity-60 disabled:cursor-not-allowed">

                                        <template x-if="!row.documentId">
                                            <span>
                                                <input type="file" :id="'other-doc-' + row.rowId" accept=".pdf,.jpg,.jpeg" class="hidden" @change="uploadOtherDocument($event, row)">
                                                <label :for="'other-doc-' + row.rowId" x-show="!row.uploading"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[10px] font-semibold bg-blue-50 hover:bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400 rounded-lg cursor-pointer transition whitespace-nowrap">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                                    Upload
                                                </label>
                                                <span x-show="row.uploading" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[10px] font-semibold text-blue-600">
                                                    <svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                                    Uploading...
                                                </span>
                                            </span>
                                        </template>

                                        <span x-show="row.documentId && row.scanStatus === 'clean'" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[10px] font-semibold text-emerald-700 dark:text-emerald-400 whitespace-nowrap">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            <span x-text="row.filename" class="truncate max-w-[120px]"></span>
                                        </span>

                                        <button type="button" @click="removeOtherRow(row)" class="text-gray-400 hover:text-red-500 transition p-1 shrink-0">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </template>

                                <p x-show="otherDocuments.length === 0" class="text-[10px] text-gray-400 text-center py-2">No alternate documents added yet.</p>
                            </div>
                        </div>

                   

                    <div x-show="canIssue" x-transition x-cloak class="mt-5 space-y-4">
                       
                        <div class="bg-gradient-to-r from-emerald-50 to-blue-50 dark:from-emerald-900/10 dark:to-blue-900/10 rounded-xl border border-emerald-200 dark:border-emerald-700 p-4">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-emerald-700 dark:text-emerald-400">All documents verified</p>
                                    <p class="text-[10px] text-emerald-600 dark:text-emerald-500">Ready to send to underwriter</p>
                                </div>
                            </div>

                            <textarea x-model="adminNotes" rows="2" placeholder="Notes for the underwriter (optional)..."
                                class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 mb-3"></textarea>
                           <button @click="issueCertificate()" :disabled="issuing"
                                class="w-auto whitespace-nowrap inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white text-xs font-bold rounded-lg transition-all duration-200 disabled:opacity-50 shadow-md hover:shadow-lg">
                                <svg x-show="!issuing" class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <svg x-show="issuing" class="animate-spin w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                <span x-text="issuing ? 'Sending...' : 'Send to Underwriter'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div x-show="selectedQuotation" x-transition x-cloak class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden sticky top-4">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-blue-50 to-transparent dark:from-blue-900/10">
                    <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Quotation Preview</h2>
                    <p class="text-[10px] text-gray-400 mt-0.5" x-text="selectedQuotation?.quotation_number"></p>
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400">Company</p>
                            <p class="text-xs font-semibold text-gray-800 dark:text-white" x-text="selectedQuotation?.company"></p>
                            <p class="text-[9px] text-gray-400" x-show="selectedQuotation?.underwriter_email" x-text="selectedQuotation?.underwriter_email"></p>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-700 pt-3 space-y-2">
                        <div class="flex justify-between text-[11px]">
                            <span class="text-gray-500 dark:text-gray-400">Customer</span>
                            <span class="font-medium text-gray-800 dark:text-white" x-text="selectedQuotation?.customer_name"></span>
                        </div>
                        <div class="flex justify-between text-[11px]">
                            <span class="text-gray-500 dark:text-gray-400">Vehicle</span>
                            <span class="font-medium text-gray-800 dark:text-white text-right" x-text="(selectedQuotation?.car_make || '') + ' ' + (selectedQuotation?.car_model || '') + ' (' + (selectedQuotation?.car_year || '') + ')'"></span>
                        </div>
                        <div class="flex justify-between text-[11px]">
                            <span class="text-gray-500 dark:text-gray-400">Registration</span>
                            <span class="font-medium text-gray-800 dark:text-white" x-text="selectedQuotation?.car_registration || 'N/A'"></span>
                        </div>
                        <div class="flex justify-between text-[11px]">
                            <span class="text-gray-500 dark:text-gray-400">Valuation</span>
                            <span class="font-medium text-gray-800 dark:text-white">KES <span x-text="Number(selectedQuotation?.car_valuation || 0).toLocaleString()"></span></span>
                        </div>
                        <div class="flex justify-between text-[11px]">
                            <span class="text-gray-500 dark:text-gray-400">Cover Type</span>
                            <span class="font-medium text-gray-800 dark:text-white" x-text="selectedQuotation?.vehicle_type === 'psv' ? 'PSV' : 'Private'"></span>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3">
                            <p class="text-[9px] font-semibold uppercase tracking-wider text-blue-600 dark:text-blue-400 mb-1">Premium Breakdown</p>
                            <div class="flex justify-between text-[11px] mb-1">
                                <span class="text-gray-500 dark:text-gray-400">Basic Premium</span>
                                <span class="font-medium text-gray-800 dark:text-white">KES <span x-text="Number(selectedQuotation?.premium || 0).toLocaleString()"></span></span>
                            </div>
                            <div x-show="selectedQuotation?.pvt_premium > 0" class="flex justify-between text-[11px] mb-1">
                                <span class="text-gray-500 dark:text-gray-400">PVT(Political Violence &amp; Terrorism Extension)</span>
                                <span class="font-medium text-gray-800 dark:text-white">KES <span x-text="Number(selectedQuotation?.pvt_premium || 0).toLocaleString()"></span></span>
                            </div>
                            <div class="flex justify-between text-[11px] border-t border-blue-200 dark:border-blue-700 pt-1 mt-1">
                                <span class="font-semibold text-gray-700 dark:text-gray-300">Total Premium</span>
                                <span class="font-bold text-blue-700 dark:text-blue-300">KES <span x-text="Number(selectedQuotation?.total_premium || 0).toLocaleString()"></span></span>
                            </div>
                        </div>
                    </div>

                    <div x-show="selectedQuotation?.breakdown && Object.keys(selectedQuotation?.breakdown || {}).length > 0" class="border-t border-gray-100 dark:border-gray-700 pt-3">
                        <p class="text-[9px] font-semibold uppercase tracking-wider text-gray-400 mb-1.5">Detailed Breakdown</p>
                        <div class="space-y-1">
                            <template x-for="(value, key) in selectedQuotation?.breakdown" :key="key">
                                <div class="flex justify-between text-[10px]">
                                    <span class="text-gray-500 dark:text-gray-400" x-text="key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())"></span>
                                    <span class="font-medium text-gray-800 dark:text-white" x-text="typeof value === 'number' ? 'KES ' + Number(value).toLocaleString() : value"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                        <div class="flex justify-between text-[11px]">
                            <span class="text-gray-500 dark:text-gray-400">Status</span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400" x-text="selectedQuotation?.status"></span>
                        </div>
                        <div class="flex justify-between text-[11px] mt-1">
                            <span class="text-gray-500 dark:text-gray-400">Date</span>
                            <span class="font-medium text-gray-800 dark:text-white" x-text="selectedQuotation?.created_at"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="!selectedQuotation" x-transition class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden sticky top-4">
                <div class="p-8 text-center">
                    <svg class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Search and select a quotation to preview</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function certificateIssue() {
    return {
        searching: false,
        searchResults: [],
        searchError: '',
        selectedQuotation: {!! $quotation ? json_encode([
            'id' => $quotation->id,
            'quotation_number' => $quotation->quotation_number,
            'customer_name' => $quotation->customer_name,
            'company' => $quotation->company->name,
            'company_id' => $quotation->company_id,
            'car_make' => $quotation->car_make,
            'car_model' => $quotation->car_model,
            'car_year' => $quotation->car_year,
            'car_registration' => $quotation->car_registration,
            'car_valuation' => $quotation->car_valuation,
            'premium' => $quotation->premium,
            'pvt_premium' => $quotation->pvt_premium,
            'total_premium' => $quotation->total_premium,
            'excess_own_damage' => $quotation->excess_own_damage,
            'excess_theft' => $quotation->excess_theft,
            'vehicle_type' => $quotation->vehicle_type,
            'num_passengers' => $quotation->num_passengers,
            'status' => $quotation->status,
            'created_at' => $quotation->created_at->format('d M Y'),
            'underwriter_email' => $quotation->company->underwriter_email,
            'breakdown' => $quotation->breakdown ?: [],
            'sticker_id' => $quotation->stickers->first()?->id,
            'client_type' => $quotation->stickers->first()?->client_type ?? 'individual',
            'cover_start_date' => optional($quotation->stickers->first()?->cover_start_date)->format('Y-m-d'),
            'cover_end_date' => optional($quotation->stickers->first()?->cover_end_date)->format('Y-m-d'),
            'cover_duration_unit' => $quotation->stickers->first()?->cover_duration_unit,
            'cover_duration_value' => $quotation->stickers->first()?->cover_duration_value,
            'logbook_na' => (bool) ($quotation->stickers->first()?->logbook_na ?? false),
            'cover_details_saved' => (bool) $quotation->stickers->first(),
            'existing_documents' => $quotation->stickers->first()?->certificateDocuments->map(fn($d) => [
                'id' => $d->id,
                'document_type' => $d->document_type,
                'document_label' => $d->document_label,
                'scan_status' => $d->scan_status,
                'filename' => $d->filename,
            ])->toArray() ?? [],
        ]) : 'null' !!},
        documents: {},
        otherDocuments: [],
        uploading: { id_copy: false, kra_pin: false, logbook: false },
        polling: { id_copy: false, kra_pin: false, logbook: false },
        issuing: false,
        adminNotes: '',
        searchTimeout: null,
        stickerId: null,

        // --- Cover details (client type + cover period) ---
        clientType: 'individual',
        coverStartDate: '',
        coverDurationUnit: 'year',
        coverDurationValue: 1,
        coverEndDate: '',           // Y-m-d, authoritative value submitted to the server
        coverEndDateDisplay: '',    // d/m/Y, what the user sees — always derived, never typed
        calculatingEndDate: false,
        coverCalcTimeout: null,
        coverDetailsSaved: false,
        savingCoverDetails: false,
        coverDetailsError: '',

        // --- Logbook N/A -> alternate "other" documents ---
        logbookNA: false,
        togglingLogbookNA: false,
        nextOtherRowId: 1,

        get step() {
            if (!this.selectedQuotation) return 1;
            if (!this.canIssue) return 2;
            return 3;
        },

        get uploadedCount() {
            const inProgress = ['clean', 'pending', 'scanning'];

            if (this.clientType === 'corporate') {
                return ['kra_pin', 'certificate_of_incorporation', 'logbook_import'].filter(t => inProgress.includes(this.documents[t]?.scan_status)).length;
            }

            let n = ['id_copy', 'kra_pin'].filter(t => inProgress.includes(this.documents[t]?.scan_status)).length;
            if (this.logbookNA) {
                if (this.otherDocuments.some(r => inProgress.includes(r.scanStatus))) n++;
            } else if (inProgress.includes(this.documents['logbook']?.scan_status)) {
                n++;
            }
            return n;
        },

        hydrateFromQuotation(q) {
            this.documents = {};
            this.otherDocuments = [];
            this.stickerId = q.sticker_id || null;
            this.clientType = q.client_type || 'individual';
            this.coverStartDate = q.cover_start_date || '';
            this.coverDurationUnit = q.cover_duration_unit || 'year';
            this.coverDurationValue = q.cover_duration_value || 1;
            this.coverEndDate = q.cover_end_date || '';
            this.coverEndDateDisplay = this.coverEndDate ? this.formatDateDisplay(this.coverEndDate) : '';
            this.coverDetailsSaved = !!q.cover_details_saved;
            this.logbookNA = !!q.logbook_na;

            (q.existing_documents || []).forEach(doc => {
                if (doc.document_type === 'other') {
                    this.otherDocuments.push({
                        rowId: this.nextOtherRowId++,
                        label: doc.document_label || '',
                        documentId: doc.id,
                        filename: doc.filename,
                        scanStatus: doc.scan_status,
                        uploading: false,
                    });
                } else {
                    this.documents[doc.document_type] = { scan_status: doc.scan_status, filename: doc.filename };
                    if (doc.scan_status === 'pending' || doc.scan_status === 'scanning') {
                        this.pollScanStatus(doc.document_type);
                    }
                }
            });

            // Seed one starter row only when "Others" is actually required —
            // an individual client's logbook marked N/A. Otherwise it's
            // optional extra material and starts empty until the user clicks
            // "Add Document".
            if (this.logbookNA && this.otherDocuments.length === 0) {
                this.addOtherRow();
            }
        },

        formatDateDisplay(ymd) {
            if (!ymd) return '';
            const [y, m, d] = ymd.split('-');
            return `${d}/${m}/${y}`;
        },

        init() {
            if (this.selectedQuotation) {
                this.selectedQuotation.breakdown = this.selectedQuotation.breakdown || {};
                this.hydrateFromQuotation(this.selectedQuotation);
            }
        },

        onSearchInput() {
            clearTimeout(this.searchTimeout);
            const query = this.$refs.searchInput.value.trim();
            if (query.length < 3) {
                this.searchResults = [];
                this.searchError = '';
                return;
            }
            this.searchTimeout = setTimeout(() => {
                this.searchQuotation();
            }, 350);
        },

        async searchQuotation() {
            const query = this.$refs.searchInput.value.trim();
            if (query.length < 3) return;

            this.searching = true;
            this.searchError = '';
            this.searchResults = [];

            try {
                const response = await fetch('{{ route("stickers.search-quotation") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ search: query }),
                });

                const data = await response.json();
                if (data.success) {
                    this.searchResults = data.quotations;
                    if (this.searchResults.length === 0) {
                        this.searchError = 'No quotations found matching your search.';
                    }
                } else {
                    this.searchError = data.error || 'Search failed.';
                }
            } catch (e) {
                this.searchError = 'An error occurred during search.';
            } finally {
                this.searching = false;
            }
        },

        selectQuotation(q) {
            this.selectedQuotation = q;
            this.hydrateFromQuotation(q);
            this.searchResults = [];
            this.$refs.searchInput.value = '';
            this.$nextTick(() => {
                const el = document.querySelector('[x-show="selectedQuotation"]');
                if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        },

        getDocumentStatus(docType) {
            return this.documents[docType]?.scan_status || null;
        },

        getDocumentFilename(docType) {
            return this.documents[docType]?.filename || null;
        },

        // Like getDocumentStatus, but the "other" slot (or an individual
        // client's logbook once marked N/A, which falls back to the same
        // row-based panel) reflects THEIR combined status instead — used for
        // the summary dots. Corporate's logbook_import is a real fixed slot
        // like kra_pin, so it falls through to the normal status lookup.
        getSlotStatus(docType) {
            const reflectsOtherDocs = docType === 'other' || (docType === 'logbook' && this.logbookNA);
            if (reflectsOtherDocs) {
                if (this.otherDocuments.some(r => r.scanStatus === 'infected')) return 'infected';
                if (this.otherDocuments.some(r => r.scanStatus === 'clean')) return 'clean';
                if (this.otherDocuments.some(r => r.scanStatus === 'pending' || r.scanStatus === 'scanning')) return 'pending';
                return null;
            }
            return this.getDocumentStatus(docType);
        },

        async uploadDocument(event, docType) {
            const file = event.target.files[0];
            if (!file || !this.selectedQuotation) return;

            this.uploading[docType] = true;

            const formData = new FormData();
            formData.append('document', file);
            formData.append('document_type', docType);
            formData.append('quotation_id', this.selectedQuotation.id);

            try {
                const response = await fetch('{{ route("stickers.upload-document") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData,
                });

                const data = await response.json();

                if (data.success) {
                    this.documents[docType] = { scan_status: data.scan_status, filename: data.filename };
                    if (data.sticker_id) {
                        this.stickerId = data.sticker_id;
                    }
                    if (typeof showToastSuccess === 'function') showToastSuccess(data.message || 'Document uploaded.');
                    if (data.scan_status === 'pending' || data.scan_status === 'scanning') {
                        this.pollScanStatus(docType);
                    }
                } else {
                    this.documents[docType] = { scan_status: data.scan_status || 'error', filename: null };
                    if (typeof showToastError === 'function') showToastError(data.error || 'Upload failed.');
                    else alert(data.error || 'Upload failed.');
                }
            } catch (e) {
                this.documents[docType] = { scan_status: 'error', filename: null };
                if (typeof showToastError === 'function') showToastError('Upload error. Please try again.');
                else alert('Upload error. Please try again.');
            } finally {
                this.uploading[docType] = false;
                event.target.value = '';
            }
        },

        async pollScanStatus(docType) {
            if (this.polling[docType]) return;
            this.polling[docType] = true;

            const poll = async () => {
                try {
                    const response = await fetch('{{ route("stickers.scan-status") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ sticker_id: this.stickerId || this.selectedQuotation?.id }),
                    });
                    const data = await response.json();
                    if (data.success && data.documents) {
                        for (const doc of data.documents) {
                            if (doc.document_type === docType && doc.scan_status !== 'pending' && doc.scan_status !== 'scanning') {
                                this.documents[docType] = { scan_status: doc.scan_status, filename: doc.filename };
                                this.polling[docType] = false;
                                return;
                            }
                        }
                        setTimeout(poll, 1500);
                    } else {
                        this.polling[docType] = false;
                    }
                } catch (e) {
                    this.polling[docType] = false;
                }
            };
            setTimeout(poll, 1500);
        },

        removeDocument(docType) {
            delete this.documents[docType];
        },

        get canIssue() {
            if (!this.selectedQuotation || !this.coverDetailsSaved) return false;

            // Any optional "Others" documents the user has started must finish
            // clean before the request can go out — an infected or still-
            // scanning optional file shouldn't silently ship alongside the
            // required set.
            const othersClean = this.otherDocuments.every(r => r.scanStatus === 'clean');

            if (this.clientType === 'corporate') {
                const kraOk = this.documents['kra_pin']?.scan_status === 'clean';
                const coiOk = this.documents['certificate_of_incorporation']?.scan_status === 'clean';
                const logbookOk = this.documents['logbook_import']?.scan_status === 'clean';
                return kraOk && coiOk && logbookOk && othersClean;
            }

            const baseOk = ['id_copy', 'kra_pin'].every(t => this.documents[t]?.scan_status === 'clean');
            const logbookOk = this.logbookNA
                ? (this.otherDocuments.length > 0 && this.otherDocuments.every(r => r.scanStatus === 'clean'))
                : this.documents['logbook']?.scan_status === 'clean';
            return baseOk && logbookOk && othersClean;
        },

        // --- Cover details: client type + cover period ---

        // Conspicuous one-click default: cover starts today and runs a full
        // year — the common case, so it doesn't require touching the
        // start-date picker or the duration dropdowns at all.
        startCoverFromToday() {
            const d = new Date();
            const pad = n => String(n).padStart(2, '0');
            this.coverStartDate = `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
            this.coverDurationUnit = 'year';
            this.coverDurationValue = 1;
            this.recalculateCoverEndDate();
        },

        onDurationUnitChanged() {
            // Clamp the value into whatever range the newly-selected unit
            // actually offers (52 weeks / 12 months) rather than leaving a
            // stale number like "45" sitting under "Month".
            const max = this.coverDurationUnit === 'week' ? 52 : 12;
            if (this.coverDurationValue > max) this.coverDurationValue = max;
            if (!this.coverDurationValue) this.coverDurationValue = 1;
            this.onCoverInputsChanged();
        },

        onCoverInputsChanged() {
            clearTimeout(this.coverCalcTimeout);
            this.coverCalcTimeout = setTimeout(() => this.recalculateCoverEndDate(), 200);
        },

        async recalculateCoverEndDate() {
            if (!this.coverStartDate) { this.coverEndDate = ''; this.coverEndDateDisplay = ''; return; }
            this.calculatingEndDate = true;

            try {
                const response = await fetch('{{ route("stickers.calculate-cover-end-date") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({
                        cover_start_date: this.coverStartDate,
                        cover_duration_unit: this.coverDurationUnit,
                        cover_duration_value: this.coverDurationValue,
                    }),
                });
                const data = await response.json();
                if (data.success) {
                    this.coverEndDate = data.cover_end_date;
                    this.coverEndDateDisplay = data.cover_end_date_display;
                }
            } catch (e) {
                // leave the previous value in place rather than clearing it
            } finally {
                this.calculatingEndDate = false;
            }
        },

        async saveCoverDetails() {
            if (!this.selectedQuotation) return;
            if (!this.coverStartDate) {
                this.coverDetailsError = 'Set a policy start date first.';
                return;
            }

            this.savingCoverDetails = true;
            this.coverDetailsError = '';

            try {
                const response = await fetch('{{ route("stickers.cover-details") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({
                        quotation_id: this.selectedQuotation.id,
                        client_type: this.clientType,
                        cover_start_date: this.coverStartDate,
                        cover_duration_unit: this.coverDurationUnit,
                        cover_duration_value: this.coverDurationValue,
                    }),
                });
                const data = await response.json();
                if (data.success) {
                    this.coverDetailsSaved = true;
                    this.stickerId = data.sticker_id;
                    this.coverEndDate = data.cover_end_date;
                    this.coverEndDateDisplay = data.cover_end_date_display;
                    if (typeof showToastSuccess === 'function') showToastSuccess('Cover details saved.');
                } else {
                    this.coverDetailsError = data.error || 'Failed to save cover details.';
                }
            } catch (e) {
                this.coverDetailsError = 'An error occurred while saving cover details.';
            } finally {
                this.savingCoverDetails = false;
            }
        },

        editCoverDetails() {
            this.coverDetailsSaved = false;
        },

        // --- Logbook N/A -> alternate "other" documents ---
        async toggleLogbookNA() {
            if (!this.selectedQuotation) return;
            const next = !this.logbookNA;
            this.togglingLogbookNA = true;

            try {
                const response = await fetch('{{ route("stickers.toggle-logbook-na") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ quotation_id: this.selectedQuotation.id, logbook_na: next }),
                });
                const data = await response.json();
                if (data.success) {
                    this.logbookNA = data.logbook_na;
                    this.stickerId = data.sticker_id;
                    if (this.logbookNA) {
                        delete this.documents['logbook'];
                        if (this.otherDocuments.length === 0) this.addOtherRow();
                    } else {
                        this.otherDocuments = [];
                    }
                } else if (typeof showToastError === 'function') {
                    showToastError(data.error || 'Failed to update logbook status.');
                }
            } catch (e) {
                if (typeof showToastError === 'function') showToastError('An error occurred.');
            } finally {
                this.togglingLogbookNA = false;
            }
        },

        addOtherRow(defaultLabel = '') {
            this.otherDocuments.push({
                rowId: this.nextOtherRowId++,
                label: defaultLabel,
                documentId: null,
                filename: null,
                scanStatus: null,
                uploading: false,
            });
        },

        async uploadOtherDocument(event, row) {
            const file = event.target.files[0];
            if (!file || !this.selectedQuotation) return;

            if (!row.label || !row.label.trim()) {
                if (typeof showToastError === 'function') showToastError('Give this document a name first (e.g. "Driver\'s Licence").');
                else alert('Give this document a name first.');
                event.target.value = '';
                return;
            }

            row.uploading = true;

            const formData = new FormData();
            formData.append('document', file);
            formData.append('document_type', 'other');
            formData.append('document_label', row.label.trim());
            formData.append('quotation_id', this.selectedQuotation.id);

            try {
                const response = await fetch('{{ route("stickers.upload-document") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData,
                });
                const data = await response.json();

                if (data.success) {
                    row.documentId = data.document_id;
                    row.filename = data.filename;
                    row.scanStatus = data.scan_status;
                    if (data.sticker_id) this.stickerId = data.sticker_id;
                    if (typeof showToastSuccess === 'function') showToastSuccess(data.message || 'Document uploaded.');
                } else {
                    row.scanStatus = data.scan_status || 'error';
                    if (typeof showToastError === 'function') showToastError(data.error || 'Upload failed.');
                    else alert(data.error || 'Upload failed.');
                }
            } catch (e) {
                row.scanStatus = 'error';
                if (typeof showToastError === 'function') showToastError('Upload error. Please try again.');
                else alert('Upload error. Please try again.');
            } finally {
                row.uploading = false;
                event.target.value = '';
            }
        },

        async removeOtherRow(row) {
            if (row.documentId) {
                try {
                    await fetch('{{ route("stickers.remove-document") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ document_id: row.documentId }),
                    });
                } catch (e) { /* row is removed from the UI regardless */ }
            }
            this.otherDocuments = this.otherDocuments.filter(r => r.rowId !== row.rowId);
            if (this.logbookNA && this.otherDocuments.length === 0) this.addOtherRow();
        },

        async issueCertificate() {
            if (!this.canIssue || !this.selectedQuotation) return;
            this.issuing = true;

            try {
                const response = await fetch('{{ route("stickers.issue.store") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ quotation_id: this.selectedQuotation.id, admin_notes: this.adminNotes }),
                });

                const data = await response.json();

                if (data.success) {
                    if (typeof showToastSuccess === 'function') showToastSuccess(data.message || 'Sticker issued!');
                    setTimeout(() => { window.location.href = data.redirect_url; }, 800);
                } else {
                    if (typeof showToastError === 'function') showToastError(data.error || 'Failed to issue.');
                    else alert(data.error || 'Failed to issue.');
                }
            } catch (e) {
                if (typeof showToastError === 'function') showToastError('An error occurred.');
                else alert('An error occurred.');
            } finally {
                this.issuing = false;
            }
        },

    }
}
</script>
@endpush
</x-app-layout>
