<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    {{-- Hero --}}
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Branding</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Compare All Card</h1>
                <p class="mt-0.5 text-xs text-slate-400">Manage the logo shown on the "Compare All" card on the homepage and quotation page.</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.companies.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium border border-white/20 text-white/80 rounded-lg hover:bg-white/10 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Companies
                </a>
            </div>
        </div>
    </div>

    {{-- Current logo card --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Current Logo</h2>
            <p class="text-[10px] text-gray-400 mt-0.5">This logo appears on the "Compare All" card.</p>
        </div>
        <div class="p-5 flex items-center gap-6">
            <div class="h-20 w-20 rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-600 flex items-center justify-center overflow-hidden bg-gray-50 dark:bg-gray-900 shrink-0">
                @php $logoUrl = $logo ? (str_starts_with($logo, 'images/') ? asset($logo) : asset('storage/' . $logo)) : asset('images/benchmarking.png'); @endphp
                <img src="{{ $logoUrl }}" alt="Compare All" class="h-full w-full object-contain" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                <span style="display:none;font-size:24px">📊</span>
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400">
                <p>Choose a logo from the media library below to replace this image.</p>
                <p class="mt-1">If no custom logo is set, the default <strong>benchmarking.png</strong> from the public images folder is used.</p>
            </div>
        </div>
    </div>

    {{-- Upload form --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <form action="{{ route('admin.compare-all.update') }}" method="POST">
            @csrf
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Select Logo</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">Pick an image from the media library, or upload a new one from your computer there.</p>
            </div>
            <div class="p-5 space-y-5">
                <div>
                    <div class="flex items-center gap-4">
                        <div id="logo-drop" class="h-14 w-14 shrink-0 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center overflow-hidden bg-gray-50 dark:bg-gray-900 group-hover:border-blue-500 transition">
                            @if($logo)
                                <img id="logo-preview-img" src="{{ $logoUrl }}" alt="Compare All" class="h-full w-full object-cover">
                            @else
                                <span id="logo-placeholder" class="text-lg font-semibold text-gray-400">—</span>
                            @endif
                        </div>
                        <div class="flex flex-col gap-1">
                            <button type="button" onclick="openCompareAllPicker()"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Choose from Media Library
                            </button>
                            <span class="text-[10px] text-gray-400">Upload a new image from the media library if not there yet.</span>
                        </div>
                        <input type="hidden" name="logo" id="compare-all-logo" value="{{ $logo ?? '' }}">
                    </div>
                    @error('logo')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-end gap-2">
                @if($logo)
                <a href="{{ route('admin.compare-all.destroy') }}"
                   onclick="event.preventDefault();if(confirm('Remove the custom logo and revert to default?'))document.getElementById('remove-form').submit();"
                   class="px-4 py-2 text-xs font-medium border border-rose-200 dark:border-rose-800 text-rose-600 dark:text-rose-400 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-900/20 transition">Remove Logo</a>
                @endif
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition">
                    Save Logo
                </button>
            </div>
        </form>
    </div>

    <x-media-picker />

    @if($logo)
    <form id="remove-form" action="{{ route('admin.compare-all.destroy') }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
    @endif

    @push('scripts')
    <script>
        function openCompareAllPicker() {
            const logoInput = document.getElementById('compare-all-logo');
            window.openMediaPicker({
                selectedPath: logoInput.value,
                onSelect: function(item) {
                    if (!item.path) return;
                    logoInput.value = item.path;
                    const drop = document.getElementById('logo-drop');
                    drop.innerHTML = `<img src="${item.url}" alt="Compare All" class="h-full w-full object-cover">`;
                }
            });
        }
    </script>
    @endpush

</x-app-layout>
