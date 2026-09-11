<x-app-layout>
<div class="space-y-6 pt-10 fade-in" x-data="updateStickerApp()">

    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Stickers</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Update Sticker</h1>
                <p class="mt-0.5 text-xs text-slate-400">Mark a sticker as completed and generate the sticker PDF</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Search Sticker</h2>
                            <p class="text-[10px] text-gray-400 mt-0.5">Search by quotation number, registration, chassis number, engine number, or owner name</p>
                        </div>
                    </div>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-3">
                        <input type="text" x-model="searchQuery" @keyup.enter="searchSticker()" placeholder="e.g. quotation number, registration, chassis/engine no., or owner name"
                               class="flex-1 min-w-0 px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <button @click="searchSticker()" :disabled="searching || !searchQuery.trim()"
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-all duration-200 disabled:opacity-50 shadow-sm">
                            <svg x-show="!searching" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <svg x-show="searching" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            Search
                        </button>
                    </div>
                    <p x-show="searchError" x-transition class="mt-2 text-[11px] text-red-600 dark:text-red-400" x-text="searchError"></p>
                </div>
            </div>

            <div x-show="searchResults.length > 0 && !foundSticker" x-transition x-cloak class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Select the Matching Sticker</h2>
                    <p class="text-[10px] text-gray-400 mt-0.5" x-text="searchResults.length + ' match' + (searchResults.length === 1 ? '' : 'es') + ' found'"></p>
                </div>
                <div class="p-5 space-y-2">
                    <template x-for="r in searchResults" :key="r.id">
                        <div @click="foundSticker = r; underwriterStickerNo = ''"
                             class="p-3 rounded-xl border border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-600 bg-gray-50 dark:bg-gray-700/50 hover:bg-blue-50 dark:hover:bg-blue-900/10 cursor-pointer transition-all duration-200">
                            <div class="flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-[11px] font-bold text-gray-800 dark:text-white" x-text="(r.quotation_number || 'No quotation number') + ' — ' + r.customer_name"></p>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-400" x-text="(r.vehicle_registration || 'No reg.') + ' · ' + r.car_make + ' ' + r.car_model + ' · ' + r.company"></p>
                                </div>
                                <span class="text-[8px] font-semibold px-1.5 py-0.5 rounded-full shrink-0"
                                      :class="r.is_completed ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'"
                                      x-text="r.is_completed ? 'COMPLETED' : 'ISSUED'"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div x-show="foundSticker" x-transition x-cloak class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Sticker Details</h2>
                    </div>
                    <div class="flex items-center gap-2">
                        <button x-show="searchResults.length > 1" type="button" @click="foundSticker = null" class="text-[10px] font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400">&larr; Back to results</button>
                        <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full"
                              :class="foundSticker?.is_completed ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : (foundSticker?.status === 'issued' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400')"
                              x-text="foundSticker?.is_completed ? 'Completed' : (foundSticker?.status === 'issued' ? 'Issued - Ready to Complete' : foundSticker?.status)"></span>
                    </div>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <div>
                            <p class="text-[9px] font-semibold uppercase tracking-wider text-gray-400 mb-0.5">Quotation Number</p>
                            <p class="text-sm font-bold text-gray-800 dark:text-white" x-text="foundSticker?.quotation_number || 'N/A'"></p>
                        </div>
                        <div>
                            <p class="text-[9px] font-semibold uppercase tracking-wider text-gray-400 mb-0.5">Registration</p>
                            <p class="text-sm font-bold text-gray-800 dark:text-white" x-text="foundSticker?.vehicle_registration || 'N/A'"></p>
                        </div>
                        <div x-show="foundSticker?.underwriter_sticker_no">
                            <p class="text-[9px] font-semibold uppercase tracking-wider text-gray-400 mb-0.5">Sticker Number</p>
                            <p class="text-sm font-bold text-gray-800 dark:text-white" x-text="foundSticker?.underwriter_sticker_no"></p>
                        </div>
                        <div>
                            <p class="text-[9px] font-semibold uppercase tracking-wider text-gray-400 mb-0.5">Customer</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="foundSticker?.customer_name"></p>
                        </div>
                        <div>
                            <p class="text-[9px] font-semibold uppercase tracking-wider text-gray-400 mb-0.5">Insurance Company</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="foundSticker?.company"></p>
                        </div>
                        <div>
                            <p class="text-[9px] font-semibold uppercase tracking-wider text-gray-400 mb-0.5">Vehicle</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="foundSticker?.car_make + ' ' + foundSticker?.car_model + ' (' + foundSticker?.car_year + ')'"></p>
                        </div>
                        <div>
                            <p class="text-[9px] font-semibold uppercase tracking-wider text-gray-400 mb-0.5">Chassis / Engine No.</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="(foundSticker?.chassis_number || 'N/A') + ' / ' + (foundSticker?.engine_number || 'N/A')"></p>
                        </div>
                        <div>
                            <p class="text-[9px] font-semibold uppercase tracking-wider text-gray-400 mb-0.5">Premium</p>
                            <p class="text-sm font-bold text-gray-800 dark:text-white">KES <span x-text="Number(foundSticker?.premium || 0).toLocaleString()"></span></p>
                        </div>
                        <div>
                            <p class="text-[9px] font-semibold uppercase tracking-wider text-gray-400 mb-0.5">Cover Type</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="foundSticker?.cover_type || 'N/A'"></p>
                        </div>
                        <div>
                            <p class="text-[9px] font-semibold uppercase tracking-wider text-gray-400 mb-0.5">Issued By</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="foundSticker?.issued_by"></p>
                        </div>
                        <div x-show="foundSticker?.completed_by">
                            <p class="text-[9px] font-semibold uppercase tracking-wider text-gray-400 mb-0.5">Completed By</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="foundSticker?.completed_by"></p>
                        </div>
                    </div>
                    <p class="mt-3 text-[9px] text-gray-300 dark:text-gray-600">Internal reference: <span x-text="foundSticker?.sticker_number"></span></p>

                    <div class="mt-5 border-t border-gray-100 dark:border-gray-700 pt-4">
                        <div x-show="foundSticker?.is_completed" class="bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-700 rounded-xl p-4">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div>
                                    <p class="text-xs font-bold text-emerald-700 dark:text-emerald-400">Already Completed</p>
                                    <p class="text-[10px] text-emerald-600 dark:text-emerald-500">This sticker was completed by <span x-text="foundSticker?.completed_by" class="font-semibold"></span> on <span x-text="foundSticker?.completed_at" class="font-semibold"></span>. It cannot be overridden.</p>
                                </div>
                            </div>
                        </div>
                        <div x-show="!foundSticker?.is_completed && foundSticker?.status === 'issued'" class="space-y-3">
                            <div class="bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-700 rounded-xl p-3">
                                <label class="block text-[10px] font-semibold text-amber-800 dark:text-amber-400 mb-1">Sticker Number (from the underwriter's reply attachment) <span class="text-red-500">*</span></label>
                                <input type="text" x-model="underwriterStickerNo" placeholder="Enter the sticker number from the underwriter's document"
                                       class="w-full px-3 py-2 text-sm border border-amber-300 dark:border-amber-700 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                <p class="text-[10px] text-amber-700/80 dark:text-amber-500/80 mt-1">Download the underwriter's attachment and copy the sticker number from it before marking as completed.</p>
                            </div>
                            <button @click="markCompleted()" :disabled="completing || !underwriterStickerNo.trim()"
                                class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white text-xs font-bold rounded-lg transition-all duration-200 disabled:opacity-50 shadow-md hover:shadow-lg">
                                <svg x-show="!completing" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <svg x-show="completing" class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                <span x-text="completing ? 'Completing...' : 'Mark as Completed'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden sticky top-4">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-amber-50 to-transparent dark:from-amber-900/10">
                    <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Pending Stickers</h2>
                    <p class="text-[10px] text-gray-400 mt-0.5">Stickers awaiting completion</p>
                </div>
                <div class="p-5">
                    @if($stickers->count())
                        <div class="space-y-2">
                            @foreach($stickers as $sticker)
                                @php
                                    $__payload = [
                                        'id' => $sticker->id,
                                        'sticker_number' => $sticker->sticker_number,
                                        'underwriter_sticker_no' => $sticker->underwriter_sticker_no,
                                        'quotation_number' => $sticker->quotation?->quotation_number,
                                        'customer_name' => $sticker->customer_name,
                                        'vehicle_registration' => $sticker->vehicle_registration,
                                        'chassis_number' => $sticker->chassis_number,
                                        'engine_number' => $sticker->engine_number,
                                        'car_make' => $sticker->car_make,
                                        'car_model' => $sticker->car_model,
                                        'car_year' => $sticker->car_year,
                                        'premium' => $sticker->premium,
                                        'cover_type' => $sticker->cover_type,
                                        'status' => $sticker->status,
                                        'company' => $sticker->company->name ?? 'N/A',
                                        'issued_by' => $sticker->issuedBy?->name ?? 'N/A',
                                        'completed_by' => null,
                                        'completed_at' => null,
                                        'is_completed' => false,
                                    ];
                                @endphp
                                <div @click="foundSticker = {{ Illuminate\Support\Js::from($__payload) }}; searchResults = []; underwriterStickerNo = ''"
                                     class="p-3 rounded-xl border border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-600 bg-gray-50 dark:bg-gray-700/50 hover:bg-blue-50 dark:hover:bg-blue-900/10 cursor-pointer transition-all duration-200">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="min-w-0">
                                            <p class="text-[11px] font-bold text-gray-800 dark:text-white">{{ $sticker->quotation?->quotation_number ?: $sticker->vehicle_registration ?: 'No reference' }}</p>
                                            <p class="text-[10px] text-gray-500 dark:text-gray-400">{{ $sticker->customer_name }}</p>
                                        </div>
                                        <span class="text-[8px] font-semibold px-1.5 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 shrink-0">ISSUED</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6">
                            <svg class="w-8 h-8 mx-auto text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-xs text-gray-400 dark:text-gray-500">No pending stickers</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateStickerApp() {
    return {
        searchQuery: '',
        searching: false,
        searchError: '',
        searchResults: [],
        foundSticker: null,
        completing: false,
        underwriterStickerNo: '',

        async searchSticker() {
            if (!this.searchQuery.trim()) return;
            this.searching = true;
            this.searchError = '';
            this.searchResults = [];
            this.foundSticker = null;
            this.underwriterStickerNo = '';

            try {
                const response = await fetch('{{ route("admin.stickers.search") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ search: this.searchQuery.trim() }),
                });

                const data = await response.json();
                if (data.success) {
                    if (data.stickers.length === 1) {
                        this.foundSticker = data.stickers[0];
                    } else {
                        this.searchResults = data.stickers;
                    }
                } else {
                    this.searchError = data.error || 'Sticker not found.';
                }
            } catch (e) {
                this.searchError = 'An error occurred during search.';
            } finally {
                this.searching = false;
            }
        },

        async markCompleted() {
            if (!this.foundSticker || !this.underwriterStickerNo.trim()) return;
            this.completing = true;

            try {
                const response = await fetch('{{ route("admin.stickers.update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ sticker_id: this.foundSticker.id, underwriter_sticker_no: this.underwriterStickerNo.trim() }),
                });

                const data = await response.json();
                if (data.success) {
                    this.foundSticker.is_completed = true;
                    this.foundSticker.status = 'completed';
                    this.foundSticker.underwriter_sticker_no = this.underwriterStickerNo.trim();
                    this.foundSticker.completed_by = '{{ auth()->user()->name }}';
                    this.foundSticker.completed_at = new Date().toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                    if (typeof showToastSuccess === 'function') showToastSuccess(data.message);
                    else alert(data.message);
                } else {
                    this.searchError = data.error || 'Failed to complete sticker.';
                    if (typeof showToastError === 'function') showToastError(data.error);
                    else alert(data.error);
                }
            } catch (e) {
                this.searchError = 'An error occurred.';
            } finally {
                this.completing = false;
            }
        },
    }
}
</script>
@endpush
</x-app-layout>
