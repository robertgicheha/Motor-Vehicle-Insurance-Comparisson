<div
    x-data="quickSearch('{{ route('motor.search') }}', '{{ url('/quotations') }}', '{{ url('/stickers') }}')"
    x-on:open-search.document="openModal()"
    x-on:keydown.window="handleShortcut($event)"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-[100] flex items-start justify-center pt-24 px-4"
>
    <div class="fixed inset-0 bg-black/50" @click="closeModal()"></div>

    <div class="relative w-full max-w-xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden"
         x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
        <div class="flex items-center gap-2 px-4 py-3 border-b border-gray-200 dark:border-gray-700">
            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input x-ref="input" type="text" x-model="query" @input="runSearch()"
                   placeholder="Search quotations, stickers, registration numbers..."
                   class="flex-1 min-w-0 bg-transparent border-0 focus:ring-0 text-sm text-gray-900 dark:text-white placeholder-gray-400">
            <kbd class="text-[10px] font-semibold text-gray-400 border border-gray-300 dark:border-gray-600 rounded px-1.5 py-0.5">Esc</kbd>
        </div>

        <div class="max-h-96 overflow-y-auto">
            <template x-if="loading">
                <div class="px-4 py-6 text-center text-sm text-gray-400">Searching...</div>
            </template>

            <template x-if="!loading && query.trim().length < 2">
                <div class="px-4 py-6 text-center text-sm text-gray-400">Type at least 2 characters to search.</div>
            </template>

            <template x-if="!loading && query.trim().length >= 2 && results.quotations.length === 0 && results.stickers.length === 0">
                <div class="px-4 py-6 text-center text-sm text-gray-400">No results found.</div>
            </template>

            <template x-if="results.quotations.length > 0">
                <div class="py-2">
                    <p class="px-4 pb-1 text-[10px] font-bold uppercase tracking-wider text-gray-400">Quotations</p>
                    <template x-for="q in results.quotations" :key="'q-' + q.id">
                        <a :href="quotationUrl + '/' + q.id"
                           class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700/50 text-sm">
                            <span class="font-medium text-gray-900 dark:text-white shrink-0" x-text="q.quotation_number"></span>
                            <span class="text-gray-400 truncate" x-text="[q.car_registration, q.car_make, q.car_model].filter(Boolean).join(' · ')"></span>
                        </a>
                    </template>
                </div>
            </template>

            <template x-if="results.stickers.length > 0">
                <div class="py-2 border-t border-gray-100 dark:border-gray-700">
                    <p class="px-4 pb-1 text-[10px] font-bold uppercase tracking-wider text-gray-400">Stickers</p>
                    <template x-for="s in results.stickers" :key="'s-' + s.id">
                        <a :href="stickerUrl + '/' + s.id"
                           class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700/50 text-sm">
                            <span class="font-medium text-gray-900 dark:text-white shrink-0" x-text="s.sticker_number"></span>
                            <span class="text-gray-400 truncate" x-text="[s.customer_name, s.car_make, s.car_model].filter(Boolean).join(' · ')"></span>
                        </a>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function quickSearch(searchUrl, quotationUrl, stickerUrl) {
    return {
        open: false,
        query: '',
        loading: false,
        results: { quotations: [], stickers: [] },
        searchTimeout: null,
        quotationUrl: quotationUrl,
        stickerUrl: stickerUrl,
        openModal() {
            this.open = true;
            this.query = '';
            this.results = { quotations: [], stickers: [] };
            this.$nextTick(() => this.$refs.input && this.$refs.input.focus());
        },
        closeModal() {
            this.open = false;
        },
        handleShortcut(event) {
            if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
                event.preventDefault();
                this.open ? this.closeModal() : this.openModal();
            } else if (event.key === 'Escape' && this.open) {
                this.closeModal();
            }
        },
        runSearch() {
            clearTimeout(this.searchTimeout);
            var q = this.query.trim();
            if (q.length < 2) {
                this.results = { quotations: [], stickers: [] };
                return;
            }
            this.searchTimeout = setTimeout(() => {
                this.loading = true;
                fetch(searchUrl + '?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(data => { this.results = { quotations: data.quotations || [], stickers: data.stickers || [] }; })
                    .catch(() => { this.results = { quotations: [], stickers: [] }; })
                    .finally(() => { this.loading = false; });
            }, 250);
        }
    };
}
</script>
