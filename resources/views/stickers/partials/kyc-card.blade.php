{{--
    One KYC document card — used for every fixed-slot document type across
    both the individual grid (id_copy, kra_pin, logbook) and the corporate
    grid (kra_pin, certificate_of_incorporation). Expects $type (the
    document_type key) and $info ([label, description, svg path]) from the
    including @foreach. The N/A toggle only ever renders for 'logbook' —
    it's a no-op for every other type.
--}}
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
            @if($type === 'logbook')
            <button type="button" @click="toggleLogbookNA()" :disabled="togglingLogbookNA"
                class="text-[9px] font-semibold text-gray-400 hover:text-amber-600 dark:hover:text-amber-400 transition shrink-0 underline decoration-dotted underline-offset-2">
                N/A
            </button>
            @endif
        </div>

        @if($type === 'logbook')<div x-show="!logbookNA">@endif

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
            <input type="file" id="doc-{{ $type }}" accept=".pdf,.jpg,.jpeg" class="hidden" @change="uploadDocument($event, '{{ $type }}')">
            <label for="doc-{{ $type }}"
               class="flex flex-col items-center justify-center w-full py-5 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:border-blue-400 dark:hover:border-blue-500 hover:bg-blue-50/50 dark:hover:bg-blue-900/10 transition-all duration-200">
                <svg class="w-7 h-7 text-gray-300 dark:text-gray-500 mb-1.5 group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                <span class="text-[10px] font-medium text-gray-500 dark:text-gray-400">Drop or click</span>
                <span class="text-[9px] text-gray-400 dark:text-gray-500 mt-0.5">PDF, JPG or JPEG up to 5MB</span>
            </label>
        </div>

        <div x-show="getDocumentStatus('{{ $type }}') === 'clean' && !uploading['{{ $type }}']" class="mt-2">
            <div class="flex items-center gap-2 p-2.5 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                <div class="w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] font-semibold text-emerald-700 dark:text-emerald-400 truncate" x-text="getDocumentFilename('{{ $type }}')"></p>
                    <p class="text-[9px] text-emerald-600 dark:text-emerald-500">Ready to send</p>
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
                <input type="file" id="doc-retry-{{ $type }}" accept=".pdf,.jpg,.jpeg" class="hidden" @change="uploadDocument($event, '{{ $type }}')">
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
                <input type="file" id="doc-retry2-{{ $type }}" accept=".pdf,.jpg,.jpeg" class="hidden" @change="uploadDocument($event, '{{ $type }}')">
                <button @click="$el.closest('.bg-white, .dark\\:bg-gray-800').querySelector('#doc-retry2-{{ $type }}').click()" class="text-[10px] font-semibold text-amber-600 hover:text-amber-800 px-1.5 py-0.5 bg-amber-50 dark:bg-amber-900/20 rounded">Retry</button>
            </div>
        </div>

        @if($type === 'logbook')
        </div>
        <div x-show="logbookNA" x-cloak class="mt-2">
            <div class="flex items-center gap-2 p-2.5 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                <div class="w-6 h-6 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] font-semibold text-amber-700 dark:text-amber-400">Marked as N/A</p>
                    <p class="text-[9px] text-amber-600 dark:text-amber-500">Attach alternate document(s) below</p>
                </div>
                <button @click="toggleLogbookNA()" :disabled="togglingLogbookNA" class="text-[10px] font-semibold text-amber-700 hover:text-amber-900 dark:text-amber-400 px-1.5 py-0.5 bg-amber-100 dark:bg-amber-900/30 rounded">Undo</button>
            </div>
        </div>
        @endif
    </div>
</div>
