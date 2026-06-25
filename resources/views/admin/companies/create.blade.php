<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    {{-- ─── HERO ─────────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Insurer Directory</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">{{ isset($company) ? 'Edit Company' : 'Add Company' }}</h1>
                <p class="mt-0.5 text-xs text-slate-400">{{ isset($company) ? 'Update company details below.' : 'Fill in the details to add a new insurer.' }}</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.companies.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium border border-white/20 text-white/80 rounded-lg hover:bg-white/10 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ─── FORM CARD ─────────────────────────────────────────────────── --}}
        <form action="{{ isset($company) ? route('admin.companies.update', $company) : route('admin.companies.store') }}"
              method="POST" enctype="multipart/form-data"
              class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            @csrf
            @if(isset($company)) @method('PUT') @endif

            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Company Details</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">Basic information about the insurer.</p>
            </div>

            <div class="p-5 space-y-5">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Company Name <span class="text-rose-500">*</span></label>
                    <input id="company-name" type="text" name="name" value="{{ old('name', $company->name ?? '') }}" required
                           class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    @error('name')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">{{ old('description', $company->description ?? '') }}</textarea>
                </div>

                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Logo</label>
                    <p class="text-[10px] text-gray-400 mb-2">PNG or SVG, transparent background recommended.</p>
                    <label for="logo-input" class="flex items-center gap-4 cursor-pointer group">
                        <div id="logo-drop" class="h-14 w-14 shrink-0 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center overflow-hidden bg-gray-50 dark:bg-gray-900 group-hover:border-blue-500 transition">
                            @if(isset($company) && $company->logoUrl())
                                <img id="logo-preview-img" src="{{ $company->logoUrl() }}" class="h-full w-full object-cover">
                            @else
                                <span id="logo-placeholder" class="text-lg font-semibold text-gray-400">—</span>
                            @endif
                        </div>
                        <span class="text-xs font-medium text-blue-600 dark:text-blue-400 group-hover:underline">Upload logo</span>
                        <input id="logo-input" type="file" name="logo" accept="image/png,image/jpeg,image/svg+xml" class="hidden">
                    </label>
                    @error('logo')<p class="text-xs text-rose-500">{{ $message }}</p>@enderror
                </div>

                @if(isset($company))
                <div>
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="is_active" value="1" {{ $company->is_active ? 'checked' : '' }}
                               class="w-3.5 h-3.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-xs text-gray-700 dark:text-gray-300">Active</span>
                    </label>
                </div>
                @endif
            </div>

            <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-end gap-2">
                <a href="{{ route('admin.companies.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">Cancel</a>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition">
                    {{ isset($company) ? 'Update' : 'Add' }} Company
                </button>
            </div>
        </form>

        {{-- ─── PREVIEW ──────────────────────────────────────────────────── --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Preview</h2>
                    <p class="text-[10px] text-gray-400 mt-0.5">How it appears in directory</p>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-3">
                        <div id="preview-avatar" class="h-10 w-10 rounded-lg bg-micblue text-white flex items-center justify-center font-semibold text-sm overflow-hidden shrink-0">
                            <span id="preview-initials">{{ isset($company) ? strtoupper(substr($company->name,0,2)) : '—' }}</span>
                        </div>
                        <div class="min-w-0">
                            <p id="preview-name" class="text-xs font-semibold text-gray-900 dark:text-white truncate">{{ $company->name ?? 'Company name' }}</p>
                            <span id="preview-status" class="inline-flex items-center gap-1 text-[10px] {{ (isset($company) && $company->is_active) ? 'text-emerald-600' : 'text-gray-400' }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ (isset($company) && $company->is_active) ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                {{ (isset($company) && $company->is_active) ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const nameInput = document.getElementById('company-name');
    const previewName = document.getElementById('preview-name');
    const previewInitials = document.getElementById('preview-initials');
    const previewAvatar = document.getElementById('preview-avatar');

    nameInput?.addEventListener('input', () => {
        const value = nameInput.value.trim();
        previewName.textContent = value || 'Company name';
        const initials = value ? value.split(/\s+/).map(w => w[0]).slice(0, 2).join('').toUpperCase() : '—';
        if (!previewAvatar.querySelector('img')) previewInitials.textContent = initials;
    });

    const logoInput = document.getElementById('logo-input');
    logoInput?.addEventListener('change', () => {
        const file = logoInput.files[0];
        if (!file) return;
        const drop = document.getElementById('logo-drop');
        const reader = new FileReader();
        reader.onload = (e) => {
            drop.innerHTML = `<img src="${e.target.result}" class="h-full w-full object-cover">`;
            previewAvatar.innerHTML = `<img src="${e.target.result}" class="h-full w-full object-cover">`;
        };
        reader.readAsDataURL(file);
    });
</script>
</x-app-layout>
