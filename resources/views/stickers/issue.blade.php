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

            <div x-show="selectedQuotation" x-transition x-cloak
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
                                <template x-for="docType in ['id_copy', 'kra_pin', 'logbook']" :key="'dot-'+docType">
                                     <div class="w-1.5 h-1.5 rounded-full transition-colors duration-300"
                                          :class="documents[docType]?.scan_status === 'clean' ? 'bg-emerald-500' : (documents[docType]?.scan_status === 'infected' ? 'bg-red-500' : ((documents[docType]?.scan_status === 'pending' || documents[docType]?.scan_status === 'scanning') ? 'bg-blue-500 animate-pulse' : 'bg-gray-300 dark:bg-gray-600'))"></div>
                                 </template>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach(['id_copy' => ['Copy of ID', 'National ID, Passport, or License', 'M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2'], 'kra_pin' => ['KRA PIN Certificate', 'Kenya Revenue Authority PIN', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'], 'logbook' => ['Vehicle Logbook', 'NTSA registered logbook', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z']] as $type => $info)
                            <div class="group relative border-2 rounded-xl transition-all duration-300 overflow-hidden"
                                 :class="getDocumentStatus('{{ $type }}') === 'clean' ? 'border-emerald-300 dark:border-emerald-600 bg-emerald-50/50 dark:bg-emerald-900/10' : (getDocumentStatus('{{ $type }}') === 'infected' ? 'border-red-300 dark:border-red-600 bg-red-50/50 dark:bg-red-900/10' : ((getDocumentStatus('{{ $type }}') === 'pending' || getDocumentStatus('{{ $type }}') === 'scanning') ? 'border-blue-300 dark:border-blue-600 bg-blue-50/50 dark:bg-blue-900/10' : (getDocumentStatus('{{ $type }}') === 'error' ? 'border-amber-300 dark:border-amber-600 bg-amber-50/50 dark:bg-amber-900/10' : 'border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-750 hover:border-blue-300 dark:hover:border-blue-600')))">
                                <div class="p-4">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex items-center gap-2">
                                             <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0"
                                                  :class="getDocumentStatus('{{ $type }}') === 'clean' ? 'bg-emerald-100 dark:bg-emerald-900/30' : ((getDocumentStatus('{{ $type }}') === 'pending' || getDocumentStatus('{{ $type }}') === 'scanning') ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-gray-100 dark:bg-gray-700')">
                                                <svg class="w-3.5 h-3.5" :class="getDocumentStatus('{{ $type }}') === 'clean' ? 'text-emerald-600 dark:text-emerald-400' : ((getDocumentStatus('{{ $type }}') === 'pending' || getDocumentStatus('{{ $type }}') === 'scanning') ? 'text-blue-600 dark:text-blue-400 animate-pulse' : 'text-gray-500 dark:text-gray-400')" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{!! $info[2] !!}"/></svg>
                                            </div>
                                            <div>
                                                <p class="text-[11px] font-semibold text-gray-800 dark:text-white">{!! $info[0] !!}</p>
                                                <p class="text-[9px] text-gray-400 dark:text-gray-500">{!! $info[1] !!}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div x-show="uploading['{{ $type }}']" class="mt-2">
                                        <div class="flex items-center gap-2 p-2.5 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                            <svg class="animate-spin w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                            <span class="text-[10px] font-medium text-blue-600 dark:text-blue-400">Uploading...</span>
                                        </div>
                                    </div>

                                    <div x-show="(getDocumentStatus('{{ $type }}') === 'pending' || getDocumentStatus('{{ $type }}') === 'scanning') && !uploading['{{ $type }}']" class="mt-2">
                                        <div class="flex items-center gap-2 p-2.5 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                            <svg class="animate-spin w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-[10px] font-semibold text-blue-700 dark:text-blue-400 truncate" x-text="getDocumentFilename('{{ $type }}')"></p>
                                                <p class="text-[9px] text-blue-600 dark:text-blue-500">Scanning in background...</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div x-show="!getDocumentStatus('{{ $type }}') && !uploading['{{ $type }}']" class="mt-2">
                                        <input type="file" id="doc-{{ $type }}" accept=".pdf,.jpg,.jpeg,.png" class="hidden" @change="uploadDocument($event, '{{ $type }}')">
                                        <label for="doc-{{ $type }}"
                                           class="flex flex-col items-center justify-center w-full py-5 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:border-blue-400 dark:hover:border-blue-500 hover:bg-blue-50/50 dark:hover:bg-blue-900/10 transition-all duration-200">
                                            <svg class="w-7 h-7 text-gray-300 dark:text-gray-500 mb-1.5 group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                            <span class="text-[10px] font-medium text-gray-500 dark:text-gray-400">Drop or click</span>
                                            <span class="text-[9px] text-gray-400 dark:text-gray-500 mt-0.5">PDF, JPG, PNG up to 10MB</span>
                                        </label>
                                    </div>

                                    <div x-show="getDocumentStatus('{{ $type }}') === 'clean' && !uploading['{{ $type }}']" class="mt-2">
                                        <div class="flex items-center gap-2 p-2.5 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                                            <div class="w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center shrink-0">
                                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-[10px] font-semibold text-emerald-700 dark:text-emerald-400 truncate" x-text="getDocumentFilename('{{ $type }}')"></p>
                                                <p class="text-[9px] text-emerald-600 dark:text-emerald-500">Verified</p>
                                            </div>
                                            <button @click="removeDocument('{{ $type }}')" class="text-emerald-600 hover:text-red-500 dark:text-emerald-400 dark:hover:text-red-400 transition-colors p-0.5">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div x-show="getDocumentStatus('{{ $type }}') === 'infected' && !uploading['{{ $type }}']" class="mt-2">
                                        <div class="flex items-center gap-2 p-2.5 bg-red-100 dark:bg-red-900/30 rounded-lg">
                                            <div class="w-6 h-6 rounded-full bg-red-500 flex items-center justify-center shrink-0">
                                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-[10px] font-semibold text-red-700 dark:text-red-400">Rejected</p>
                                                <p class="text-[9px] text-red-600 dark:text-red-500">Security threat</p>
                                            </div>
                                            <input type="file" id="doc-retry-{{ $type }}" accept=".pdf,.jpg,.jpeg,.png" class="hidden" @change="uploadDocument($event, '{{ $type }}')">
                                            <button @click="$el.closest('.bg-white, .dark\\:bg-gray-800').querySelector('#doc-retry-{{ $type }}').click()" class="text-[10px] font-semibold text-red-600 hover:text-red-800 px-1.5 py-0.5 bg-red-50 dark:bg-red-900/20 rounded">Retry</button>
                                        </div>
                                    </div>

                                    <div x-show="getDocumentStatus('{{ $type }}') === 'error' && !uploading['{{ $type }}']" class="mt-2">
                                        <div class="flex items-center gap-2 p-2.5 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                                            <div class="w-6 h-6 rounded-full bg-amber-500 flex items-center justify-center shrink-0">
                                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-[10px] font-semibold text-amber-700 dark:text-amber-400">Scan unavailable</p>
                                                <p class="text-[9px] text-amber-600 dark:text-amber-500">Not verified</p>
                                            </div>
                                            <input type="file" id="doc-retry2-{{ $type }}" accept=".pdf,.jpg,.jpeg,.png" class="hidden" @change="uploadDocument($event, '{{ $type }}')">
                                            <button @click="$el.closest('.bg-white, .dark\\:bg-gray-800').querySelector('#doc-retry2-{{ $type }}').click()" class="text-[10px] font-semibold text-amber-600 hover:text-amber-800 px-1.5 py-0.5 bg-amber-50 dark:bg-amber-900/20 rounded">Retry</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 flex items-start gap-2 p-3 bg-gray-50 dark:bg-gray-750 rounded-lg border border-gray-200 dark:border-gray-600">
                        <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <div>
                            <p class="text-[10px] font-medium text-gray-600 dark:text-gray-400">Security scanning is performed automatically</p>
                            <p class="text-[9px] text-gray-400 dark:text-gray-500">Files checked for executable content, extension spoofing, and malware.</p>
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

                            <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-700 mb-3">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-6 h-6 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center shrink-0">
                                        <svg class="w-3 h-3 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-semibold text-gray-800 dark:text-white">Test Email</p>
                                        <p class="text-[9px] text-gray-400 dark:text-gray-500">Preview how the email will look before sending</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="email" x-model="testEmail" placeholder="Enter test email address"
                                           class="flex-1 min-w-0 px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                    <button @click="sendTestEmail()" :disabled="sendingTestEmail || !testEmail"
                                        class="inline-flex items-center gap-1.5 px-3 py-2 text-[11px] font-semibold rounded-lg transition-all duration-200 disabled:opacity-50"
                                        :class="sendingTestEmail ? 'bg-amber-100 text-amber-700' : 'bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400 dark:hover:bg-amber-900/30'">
                                        <svg x-show="!sendingTestEmail" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        <svg x-show="sendingTestEmail" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                        <span x-text="sendingTestEmail ? 'Sending...' : 'Send Test'"></span>
                                    </button>
                                </div>
                                <p x-show="testEmailSent" x-transition class="mt-2 text-[10px] text-emerald-600 dark:text-emerald-400 font-medium" x-text="testEmailSent"></p>
                                <p x-show="testEmailError" x-transition class="mt-2 text-[10px] text-red-600 dark:text-red-400 font-medium" x-text="testEmailError"></p>
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
        ]) : 'null' !!},
        documents: {},
        uploading: { id_copy: false, kra_pin: false, logbook: false },
        polling: { id_copy: false, kra_pin: false, logbook: false },
        issuing: false,
        adminNotes: '',
        searchTimeout: null,
        stickerId: null,
        testEmail: '',
        sendingTestEmail: false,
        testEmailSent: '',
        testEmailError: '',

        get step() {
            if (!this.selectedQuotation) return 1;
            if (!this.canIssue) return 2;
            return 3;
        },

        get uploadedCount() {
            return ['id_copy', 'kra_pin', 'logbook'].filter(t => this.documents[t]?.scan_status === 'clean' || this.documents[t]?.scan_status === 'pending' || this.documents[t]?.scan_status === 'scanning').length;
        },

        init() {
            if (this.selectedQuotation) {
                this.selectedQuotation.breakdown = this.selectedQuotation.breakdown || {};
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
            this.documents = {};
            this.stickerId = q.sticker_id || null;
            if (q.existing_documents && q.existing_documents.length > 0) {
                q.existing_documents.forEach(doc => {
                    this.documents[doc.document_type] = { scan_status: doc.scan_status, filename: doc.filename };
                    if (doc.scan_status === 'pending' || doc.scan_status === 'scanning') {
                        this.pollScanStatus(doc.document_type);
                    }
                });
            }
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
            if (!this.selectedQuotation) return false;
            return ['id_copy', 'kra_pin', 'logbook'].every(t => this.documents[t]?.scan_status === 'clean');
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

        async sendTestEmail() {
            if (!this.testEmail || !this.selectedQuotation) return;
            this.sendingTestEmail = true;
            this.testEmailSent = '';
            this.testEmailError = '';

            try {
                const response = await fetch('{{ route("stickers.test-email") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ quotation_id: this.selectedQuotation.id, test_email: this.testEmail }),
                });

                const data = await response.json();

                if (data.success) {
                    this.testEmailSent = data.message;
                } else {
                    this.testEmailError = data.error || 'Failed to send test email.';
                }
            } catch (e) {
                this.testEmailError = 'An error occurred while sending test email.';
            } finally {
                this.sendingTestEmail = false;
            }
        },
    }
}
</script>
@endpush
</x-app-layout>
