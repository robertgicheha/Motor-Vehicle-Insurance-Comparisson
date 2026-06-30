<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Configuration</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Settings</h1>
                <p class="mt-0.5 text-xs text-slate-400">Manage all system settings — general, email, quotations, API, security &amp; more</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-3">
            <a href="{{ route('admin.settings.index') }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar"></div>
                <p class="kpi-eyebrow">Groups</p>
                <p class="kpi-value mt-1">{{ $settings->count() }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Setting groups</span>
            </a>
            <a href="{{ route('admin.settings.index') }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#34d399,#6ee7b7)"></div>
                <p class="kpi-eyebrow">Settings</p>
                <p class="kpi-value mt-1">{{ $settings->sum(fn($g) => $g->count()) }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Individual settings</span>
            </a>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#818cf8,#a5b4fc)"></div>
                <p class="kpi-eyebrow">Environment</p>
                <p class="kpi-value mt-1">{{ app()->environment() }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">App environment</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#fb7185,#fda4af)"></div>
                <p class="kpi-eyebrow">Version</p>
                <p class="kpi-value mt-1">{{ config('app.version', '1.0') }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">App version</span>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
        @csrf
        @foreach($settings as $group => $groupSettings)
            @php
                $groupIcons = [
                    'general' => 'cog',
                    'email' => 'mail',
                    'quotations' => 'document',
                    'api' => 'code',
                    'notifications' => 'bell',
                    'appearance' => 'eye',
                    'security' => 'shield',
                    'integrations' => 'link',
                ];
                $icon = $groupIcons[$group] ?? 'cog';
            @endphp
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-3 bg-gray-50 dark:bg-gray-700/40 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($icon === 'cog')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        @elseif($icon === 'mail')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        @elseif($icon === 'document')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        @elseif($icon === 'code')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                        @elseif($icon === 'bell')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        @elseif($icon === 'eye')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        @elseif($icon === 'shield')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        @elseif($icon === 'link')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        @endif
                    </svg>
                    <h3 class="text-xs font-semibold text-gray-800 dark:text-white capitalize">{{ $group }}</h3>
                </div>
                <div class="px-5 py-4 space-y-4">
                    @foreach($groupSettings as $setting)
                        @php
                            $key = $setting->key;
                            $value = $setting->value;
                            $inputType = 'text';
                            $options = [];

                            if (str_contains($key, 'password') && !str_contains($key, 'min_length')) {
                                $inputType = 'password';
                            } elseif (str_contains($key, 'email')) {
                                $inputType = 'email';
                            } elseif (str_contains($key, 'logo') || str_contains($key, 'favicon')) {
                                $inputType = 'media';
                            } elseif (str_contains($key, 'url') || str_contains($key, 'path')) {
                                $inputType = 'url';
                            } elseif (in_array($key, ['timezone', 'date_format', 'currency', 'currency_symbol', 'default_cover_type', 'mail_mailer', 'mail_encryption', 'primary_color'])) {
                                $inputType = 'select';
                                if ($key === 'timezone') $options = ['Africa/Nairobi', 'UTC', 'America/New_York', 'Europe/London', 'Asia/Dubai'];
                                elseif ($key === 'date_format') $options = ['d M Y', 'Y-m-d', 'm/d/Y', 'd/m/Y'];
                                elseif ($key === 'currency') $options = ['KES', 'USD', 'EUR', 'GBP'];
                                elseif ($key === 'currency_symbol') $options = ['KES', '$', '€', '£'];
                                elseif ($key === 'default_cover_type') $options = ['comprehensive', 'third_party', 'third_party_fire_theft'];
                                elseif ($key === 'mail_mailer') $options = ['smtp', 'log', 'sendmail', 'ses', 'postmark', 'mailgun'];
                                elseif ($key === 'mail_encryption') $options = ['tls', 'ssl', 'none'];
                                elseif ($key === 'primary_color') $inputType = 'color';
                            } elseif (in_array($value, ['true', 'false']) || str_contains($key, 'enable') || str_contains($key, 'require') || str_contains($key, 'notify_on') || str_contains($key, 'two_factor') || str_contains($key, 'collapsed') || str_contains($key, 'social_login') || str_contains($key, 'public_api') || str_contains($key, 'pvt')) {
                                $inputType = 'boolean';
                            } elseif (is_numeric($value) || str_contains($key, 'count') || str_contains($key, 'limit') || str_contains($key, 'days') || str_contains($key, 'lifetime') || str_contains($key, 'length') || str_contains($key, 'rate') || str_contains($key, 'per_page') || str_contains($key, 'port') || str_contains($key, 'valuation')) {
                                $inputType = 'number';
                            }
                        @endphp
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1 capitalize">
                                {{ str_replace('_', ' ', $key) }}
                            </label>
                            @if($inputType === 'boolean')
                                <select name="settings[{{ $key }}]"
                                        class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
                                    <option value="true" {{ $value === 'true' ? 'selected' : '' }}>Enabled</option>
                                    <option value="false" {{ $value === 'false' ? 'selected' : '' }}>Disabled</option>
                                </select>
                            @elseif($inputType === 'select' && count($options) > 0)
                                <select name="settings[{{ $key }}]"
                                        class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
                                    @foreach($options as $option)
                                        <option value="{{ $option }}" {{ $value === $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </select>
                            @elseif($inputType === 'color')
                                <div class="flex items-center gap-2">
                                    <input type="color" name="settings[{{ $key }}]" value="{{ $value }}"
                                           class="w-10 h-8 rounded border border-gray-200 dark:border-gray-600 cursor-pointer">
                                    <input type="text" name="settings[{{ $key }}]" value="{{ $value }}"
                                           class="flex-1 px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>
                            @elseif($inputType === 'password')
                                <input type="password" name="settings[{{ $key }}]" value="{{ $value }}"
                                       class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none"
                                       autocomplete="off">
                            @elseif($inputType === 'media')
                                <div class="flex items-center gap-2">
                                    <input type="text" name="settings[{{ $key }}]" value="{{ $value }}"
                                           class="flex-1 px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none"
                                           id="setting-{{ $key }}" readonly
                                           onclick="openMediaLibrary('setting-{{ $key }}')" style="cursor:pointer">
                                    <button type="button" onclick="openMediaLibrary('setting-{{ $key }}')"
                                            class="px-3 py-2 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition whitespace-nowrap">
                                        Browse Media
                                    </button>
                                    @if($value)
                                        <a href="{{ str_starts_with($value, 'images/') ? asset($value) : (file_exists(public_path('storage/' . $value)) ? asset('storage/' . $value) : '#') }}"
                                           target="_blank"
                                           class="px-2 py-2 text-xs text-gray-500 hover:text-blue-600 transition">
                                            Preview
                                        </a>
                                    @endif
                                </div>
                            @else
                                <input type="{{ $inputType }}" name="settings[{{ $key }}]" value="{{ $value }}"
                                       class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="flex justify-end">
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-5 py-2 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Save All Settings
            </button>
        </div>
    </form>
</div>
{{-- Media Picker Modal --}}
<div id="mediaPickerModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeMediaPicker()"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-2xl bg-white dark:bg-gray-900 shadow-2xl flex flex-col">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between shrink-0">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Media Library</p>
                <h2 class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">Select an Image</h2>
            </div>
            <button onclick="closeMediaPicker()" class="p-1.5 text-gray-400 hover:text-gray-700 dark:hover:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-6">
            <div id="mediaPickerGrid" class="grid grid-cols-3 sm:grid-cols-4 gap-4">
                <div class="col-span-full text-center py-12 text-sm text-gray-400">Loading...</div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let mediaPickerCallback = null;

function openMediaLibrary(inputId) {
    mediaPickerCallback = function(url) {
        document.getElementById(inputId).value = url;
    };
    document.getElementById('mediaPickerModal').classList.remove('hidden');
    loadMediaGrid();
}

function closeMediaPicker() {
    document.getElementById('mediaPickerModal').classList.add('hidden');
    mediaPickerCallback = null;
}

function loadMediaGrid() {
    var grid = document.getElementById('mediaPickerGrid');
    grid.innerHTML = '<div class="col-span-full text-center py-12 text-sm text-gray-400">Loading...</div>';
    fetch('{{ route("admin.media.list") }}')
        .then(function(r) { return r.json(); })
        .then(function(items) {
            if (!items || !items.length) {
                grid.innerHTML = '<div class="col-span-full text-center py-12 text-sm text-gray-400">No images yet. <a href="{{ route("admin.media.index") }}" class="text-blue-600 underline" target="_blank">Upload images</a>.</div>';
                return;
            }
            grid.innerHTML = '';
            items.forEach(function(m) {
                var div = document.createElement('div');
                div.className = 'relative bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden cursor-pointer hover:ring-2 hover:ring-blue-500 transition';
                div.innerHTML = '<div class="aspect-square flex items-center justify-center p-2"><img src="' + m.url + '" alt="' + m.filename + '" class="max-w-full max-h-full object-contain"></div><div class="p-2"><p class="text-[10px] text-gray-600 dark:text-gray-400 truncate">' + m.filename + '</p></div>';
                div.onclick = function() {
                    if (mediaPickerCallback) mediaPickerCallback(m.url);
                    closeMediaPicker();
                };
                grid.appendChild(div);
            });
        })
        .catch(function() {
            grid.innerHTML = '<div class="col-span-full text-center py-12 text-sm text-red-400">Failed to load media.</div>';
        });
}
</script>
@endpush

</x-app-layout>
