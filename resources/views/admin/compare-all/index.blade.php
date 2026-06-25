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
                @php $logoUrl = $logo ? asset('storage/' . $logo) : asset('images/benchmarking.png'); @endphp
                <img src="{{ $logoUrl }}" alt="Compare All" class="h-full w-full object-contain" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                <span style="display:none;font-size:24px">📊</span>
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400">
                <p>Upload a new logo below to replace this image.</p>
                <p class="mt-1">If no custom logo is uploaded, the default <strong>benchmarking.png</strong> from the public images folder is used.</p>
            </div>
        </div>
    </div>

    {{-- Upload form --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <form action="{{ route('admin.compare-all.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Upload New Logo</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">PNG or SVG, transparent background recommended.</p>
            </div>
            <div class="p-5 space-y-5">
                <div>
                    <label for="logo-input" class="flex items-center gap-4 cursor-pointer group">
                        <div id="logo-drop" class="h-14 w-14 shrink-0 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center overflow-hidden bg-gray-50 dark:bg-gray-900 group-hover:border-blue-500 transition">
                            <span id="logo-placeholder" class="text-lg font-semibold text-gray-400">—</span>
                        </div>
                        <span class="text-xs font-medium text-blue-600 dark:text-blue-400 group-hover:underline">Choose file</span>
                        <input id="logo-input" type="file" name="logo" accept="image/png,image/jpeg,image/svg+xml" class="hidden">
                    </label>
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
                    Upload Logo
                </button>
            </div>
        </form>
    </div>

</div>

@if($logo)
<form id="remove-form" action="{{ route('admin.compare-all.destroy') }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endif

<script>
    const logoInput = document.getElementById('logo-input');
    logoInput?.addEventListener('change', () => {
        const file = logoInput.files[0];
        if (!file) return;
        const drop = document.getElementById('logo-drop');
        const reader = new FileReader();
        reader.onload = (e) => {
            drop.innerHTML = `<img src="${e.target.result}" class="h-full w-full object-cover">`;
        };
        reader.readAsDataURL(file);
    });
</script>
</x-app-layout>
