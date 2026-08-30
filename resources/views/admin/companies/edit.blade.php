<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    {{-- Hero --}}
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Underwriter Directory</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">{{ isset($company) ? 'Edit Underwriter' : 'Add Underwriter' }}</h1>
                <p class="mt-0.5 text-xs text-slate-400">{{ isset($company) ? 'Update underwriter details below.' : 'Fill in the details to create a new underwriter.' }}</p>
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

    {{-- Form Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <form action="{{ isset($company) ? route('admin.companies.update', $company) : route('admin.companies.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($company)) @method('PUT') @endif

            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Underwriter Details</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">Basic information about the underwriter.</p>
            </div>

            <div class="p-5 space-y-5">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Underwriter Name <span class="text-rose-500">*</span></label>
                    <input id="company-name" type="text" name="name" value="{{ old('name', $company->name ?? '') }}" required
                           class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">{{ old('description', $company->description ?? '') }}</textarea>
                    @error('description') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Underwriter Type <span class="text-rose-500">*</span></label>
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
                            @php $cl = $company->logoUrl(); @endphp
                            @if($cl)
                                <img id="logo-preview-img" src="{{ $cl }}" alt="{{ $company->name }} logo" class="h-full w-full object-cover">
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

            <div class="px-5 py-4 border-t border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Contact Profiles</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">Who to reach at this underwriter for certificate issuance and claims, and where emails are sent.</p>
            </div>

            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Underwriting Profile --}}
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-4 py-2.5 bg-blue-50 dark:bg-blue-900/20 border-b border-gray-200 dark:border-gray-700">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-blue-700 dark:text-blue-400">Underwriting</p>
                    </div>
                    <div class="p-4 space-y-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Name</label>
                            <input type="text" name="underwriter_name" value="{{ old('underwriter_name', $company->underwriter_name ?? '') }}" 
                                   class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            @error('underwriter_name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Email</label>
                            <input type="email" name="underwriter_email" value="{{ old('underwriter_email', $company->underwriter_email ?? '') }}" 
                                   class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            @error('underwriter_email') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Telephone</label>
                            <input type="text" name="underwriter_phone" value="{{ old('underwriter_phone', $company->underwriter_phone ?? '') }}" 
                                   class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            @error('underwriter_phone') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">CC Email</label>
                            <input type="email" name="sticker_notification_emails" value="{{ old('sticker_notification_emails', $company->sticker_notification_emails ?? '') }}"
                                   class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            @error('sticker_notification_emails') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Claims Profile --}}
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-4 py-2.5 bg-emerald-50 dark:bg-emerald-900/20 border-b border-gray-200 dark:border-gray-700">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Claims</p>
                    </div>
                    <div class="p-4 space-y-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Name</label>
                            <input type="text" name="claims_name" value="{{ old('claims_name', $company->claims_name ?? '') }}" 
                                   class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            @error('claims_name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Email</label>
                            <input type="email" name="claims_email" value="{{ old('claims_email', $company->claims_email ?? '') }}" 
                                   class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            @error('claims_email') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Telephone</label>
                            <input type="text" name="claims_phone" value="{{ old('claims_phone', $company->claims_phone ?? '') }}" 
                                   class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            @error('claims_phone') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">CC Email</label>
                            <input type="email" name="cc_claims_email" value="{{ old('cc_claims_email', $company->cc_claims_email ?? '') }}" 
                                   class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            @error('cc_claims_email') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-end gap-2">
                <a href="{{ route('admin.companies.index') }}"
                   class="px-4 py-2 text-xs font-medium border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">Cancel</a>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition">
                    {{ isset($company) ? 'Update' : 'Create' }} Underwriter
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const logoInput = document.getElementById('logo-input');
    logoInput?.addEventListener('change', () => {
        const file = logoInput.files[0];
        if (!file) return;
        const drop = document.getElementById('logo-drop');
        const reader = new FileReader();
        reader.onload = (e) => {
            drop.innerHTML = `<img src="${e.target.result}" alt="Logo preview" class="h-full w-full object-cover">`;
        };
        reader.readAsDataURL(file);
    });
</script>
</x-app-layout>
