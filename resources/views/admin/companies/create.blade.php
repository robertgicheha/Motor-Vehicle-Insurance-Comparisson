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
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Company Type <span class="text-rose-500">*</span></label>
                    <select name="company_type" required
                            class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="both" {{ old('company_type', $company->company_type ?? '') === 'both' ? 'selected' : '' }}>Both Private &amp; PSV</option>
                        <option value="private" {{ old('company_type', $company->company_type ?? '') === 'private' ? 'selected' : '' }}>Private Only</option>
                        <option value="psv" {{ old('company_type', $company->company_type ?? '') === 'psv' ? 'selected' : '' }}>PSV Only</option>
                    </select>
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
                        <input type="hidden" name="logo_cropped" id="logoCropped">
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

{{-- ─── CROP MODAL ────────────────────────────────────────────────────── --}}
<div id="logoCropModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/60" x-cloak>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Crop Logo</h3>
            <button onclick="closeLogoCrop()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-5">
            <div class="w-full max-h-80 overflow-hidden">
                <img id="logoCropImage" src="" alt="Crop" class="max-w-full">
            </div>
        </div>
        <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-2">
            <button onclick="closeLogoCrop()" class="px-4 py-2 text-xs font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">Cancel</button>
            <button onclick="applyLogoCrop()" class="px-4 py-2 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Apply Crop</button>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.js"></script>
<script>
let logCropper = null;
const logoInput = document.getElementById('logo-input');
const logoCropModal = document.getElementById('logoCropModal');
const logoCropImage = document.getElementById('logoCropImage');
const logoCropped = document.getElementById('logoCropped');

logoInput?.addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        logoCropImage.src = e.target.result;
        logoCropModal.classList.remove('hidden');
        if (logCropper) logCropper.destroy();
        logCropper = new Cropper(logoCropImage, {
            aspectRatio: 1,
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 1,
            cropBoxResizable: true,
            cropBoxMovable: true,
        });
    };
    reader.readAsDataURL(file);
});

function closeLogoCrop() {
    logoCropModal.classList.add('hidden');
    if (logCropper) { logCropper.destroy(); logCropper = null; }
    logoInput.value = '';
}

function applyLogoCrop() {
    if (!logCropper) return;
    const canvas = logCropper.getCroppedCanvas({ width: 400, height: 400 });
    canvas.toBlob(function(blob) {
        const file = new File([blob], 'logo.png', { type: 'image/png' });
        const container = new DataTransfer();
        container.items.add(file);
        logoInput.files = container.files;
        logoCropped.value = '1';
        const url = URL.createObjectURL(blob);
        document.getElementById('logo-drop').innerHTML = `<img src="${url}" class="h-full w-full object-cover">`;
        document.getElementById('preview-avatar').innerHTML = `<img src="${url}" class="h-full w-full object-cover">`;
        closeLogoCrop();
    }, 'image/png', 0.9);
}

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
</script>
</x-app-layout>
