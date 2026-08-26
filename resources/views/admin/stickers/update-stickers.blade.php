<x-app-layout>
<div class="space-y-6 pt-10 fade-in" x-data="updateStickerApp()">

    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Certificates</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Update Sticker</h1>
                <p class="mt-0.5 text-xs text-slate-400">Mark a sticker as completed and generate the certificate PDF</p>
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
                            <p class="text-[10px] text-gray-400 mt-0.5">Enter the sticker number to find and update</p>
                        </div>
                    </div>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-3">
                        <input type="text" x-model="searchQuery" @keyup.enter="searchSticker()" placeholder="e.g. STK202608260001"
                               class="flex-1 px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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

            <div x-show="foundSticker" x-transition x-cloak class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Sticker Details</h2>
                    </div>
                    <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full"
                          :class="foundSticker?.is_completed ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : (foundSticker?.status === 'issued' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400')"
                          x-text="foundSticker?.is_completed ? 'Completed' : (foundSticker?.status === 'issued' ? 'Issued - Ready to Complete' : foundSticker?.status)"></span>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <div>
                            <p class="text-[9px] font-semibold uppercase tracking-wider text-gray-400 mb-0.5">Sticker Number</p>
                            <p class="text-sm font-bold text-gray-800 dark:text-white" x-text="foundSticker?.sticker_number"></p>
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
                            <p class="text-[9px] font-semibold uppercase tracking-wider text-gray-400 mb-0.5">Registration</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="foundSticker?.vehicle_registration || 'N/A'"></p>
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
                            <button @click="markCompleted()" :disabled="completing"
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
                    <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Pending Certificates</h2>
                    <p class="text-[10px] text-gray-400 mt-0.5">Stickers awaiting completion</p>
                </div>
                <div class="p-5">
                    @if($stickers->count())
                        <div class="space-y-2">
                            @foreach($stickers as $sticker)
                                <div @click="$dispatch('select-sticker', '{{ $sticker->sticker_number }}')"
                                     class="p-3 rounded-xl border border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-600 bg-gray-50 dark:bg-gray-700/50 hover:bg-blue-50 dark:hover:bg-blue-900/10 cursor-pointer transition-all duration-200">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="min-w-0">
                                            <p class="text-[11px] font-bold text-gray-800 dark:text-white" x-text="'{{ $sticker->sticker_number }}'"></p>
                                            <p class="text-[10px] text-gray-500 dark:text-gray-400" x-text="'{{ $sticker->customer_name }}'"></p>
                                        </div>
                                        <span class="text-[8px] font-semibold px-1.5 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 shrink-0">ISSUED</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6">
                            <svg class="w-8 h-8 mx-auto text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-xs text-gray-400 dark:text-gray-500">No pending certificates</p>
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
        foundSticker: null,
        completing: false,

        init() {
            this.$on('select-sticker', (stickerNumber) => {
                this.searchQuery = stickerNumber;
                this.searchSticker();
            });
        },

        async searchSticker() {
            if (!this.searchQuery.trim()) return;
            this.searching = true;
            this.searchError = '';
            this.foundSticker = null;

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
                    this.foundSticker = data.sticker;
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
            if (!this.foundSticker) return;
            this.completing = true;

            try {
                const response = await fetch('{{ route("admin.stickers.update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ sticker_id: this.foundSticker.id }),
                });

                const data = await response.json();
                if (data.success) {
                    this.foundSticker.is_completed = true;
                    this.foundSticker.status = 'completed';
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
