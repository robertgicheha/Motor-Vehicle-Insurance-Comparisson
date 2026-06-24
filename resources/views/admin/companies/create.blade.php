<x-app-layout>
    <div class="max-w-5xl mx-auto pt-20 px-4 py-8 fade-in">

        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-micblue dark:bg-slate-700 text-white shadow-sm">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m4.5-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Insurer Directory</p>
                    <h1 class="text-2xl font-bold text-micblue dark:text-white">{{ isset($company) ? 'Edit Company' : 'Add Company' }}</h1>
                </div>
            </div>
            <a href="{{ route('admin.companies.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-micblue">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                Back to directory
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Form -->
            <form action="{{ isset($company) ? route('admin.companies.update', $company) : route('admin.companies.store') }}"
                  method="POST" enctype="multipart/form-data"
                  class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-700">
                @csrf
                @if(isset($company)) @method('PUT') @endif

                <!-- Section: Details -->
                <div class="p-6 space-y-5">
                    <h2 class="text-sm font-semibold text-micblue dark:text-white">Company details</h2>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company name</label>
                        <input id="company-name" type="text" name="name" value="{{ old('name', $company->name ?? '') }}" required
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-micblue focus:border-micblue">
                        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                        <textarea name="description" rows="3"
                                  class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-micblue focus:border-micblue">{{ old('description', $company->description ?? '') }}</textarea>
                    </div>
                </div>

                <!-- Section: Logo -->
                <div class="p-6 space-y-3">
                    <h2 class="text-sm font-semibold text-micblue dark:text-white">Logo</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">PNG or SVG, transparent background recommended. Used in the directory and on rate comparison tables.</p>

                    <label for="logo-input" class="flex items-center gap-4 cursor-pointer group">
                        <div id="logo-drop" class="h-16 w-16 shrink-0 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center overflow-hidden bg-gray-50 dark:bg-gray-900 group-hover:border-micblue transition">
                            @if(isset($company) && $company->logo_path)
                                <img id="logo-preview-img" src="{{ Storage::url($company->logo_path) }}" class="h-full w-full object-cover">
                            @else
                                <span id="logo-placeholder" class="text-lg font-semibold text-gray-400">—</span>
                            @endif
                        </div>
                        <span class="text-sm font-medium text-micblue dark:text-micblue group-hover:underline">Upload logo</span>
                        <input id="logo-input" type="file" name="logo" accept="image/png,image/jpeg,image/svg+xml" class="hidden">
                    </label>
                    @error('logo')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                @if(isset($company))
                <!-- Section: Status -->
                <div class="p-6">
                    <h2 class="text-sm font-semibold text-micblue dark:text-white mb-3">Status</h2>
                    <label class="flex items-center justify-between cursor-pointer">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Visible for quotations</span>
                        <span class="relative inline-flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ $company->is_active ? 'checked' : '' }}
                                   class="peer sr-only" id="is_active">
                            <span class="h-6 w-11 rounded-full bg-gray-300 dark:bg-gray-600 peer-checked:bg-emerald-600 transition-colors"></span>
                            <span class="absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition-transform peer-checked:translate-x-5"></span>
                        </span>
                    </label>
                </div>
                @endif

                <!-- Actions -->
                <div class="p-6 flex items-center justify-end gap-3 bg-gray-50 dark:bg-gray-900/40 rounded-b-2xl">
                    <a href="{{ route('admin.companies.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">Cancel</a>
                    <button type="submit" class="px-6 py-2.5 bg-micblue hover:bg-micblue text-white rounded-lg transition font-medium text-sm">
                        {{ isset($company) ? 'Save changes' : 'Add company' }}
                    </button>
                </div>
            </form>

            <!-- Live preview -->
            <div class="lg:col-span-1">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Directory preview</p>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center gap-3">
                    <div id="preview-avatar" class="h-11 w-11 rounded-lg bg-micblue text-white flex items-center justify-center font-semibold text-sm overflow-hidden shrink-0">
                        <span id="preview-initials">{{ isset($company) ? strtoupper(substr($company->name,0,2)) : '—' }}</span>
                    </div>
                    <div class="min-w-0">
                        <p id="preview-name" class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $company->name ?? 'Company name' }}</p>
                        <span id="preview-status" class="inline-flex items-center gap-1 text-xs {{ (isset($company) && $company->is_active) ? 'text-emerald-600' : 'text-gray-400' }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ (isset($company) && $company->is_active) ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                            {{ (isset($company) && $company->is_active) ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-3">This is how the company will appear in the insurer directory and on rate comparison tables.</p>
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