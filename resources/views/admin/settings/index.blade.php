<x-app-layout>
<div class="space-y-5 pt-8 fade-in">

    {{-- ─── HERO ─────────────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-micblue px-6 py-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Configuration</p>
                <h1 class="mt-0.5 text-lg font-bold text-white">Settings</h1>
                <p class="mt-0.5 text-[11px] text-slate-400">Manage all system settings — general, email, quotations, API, security &amp; more</p>
            </div>
            <div class="flex items-center gap-2 text-[11px] text-slate-400">
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-blue-500/20 text-blue-300">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ $settings->count() }} groups
                </span>
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-green-500/20 text-green-300">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $settings->sum(fn($g) => $g->count()) }} settings
                </span>
            </div>
        </div>

        <div class="mt-5 grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="kpi-card p-3.5">
                <div class="accent-bar"></div>
                <p class="kpi-eyebrow text-[10px]">Environment</p>
                <p class="kpi-value mt-1 text-sm">{{ app()->environment() }}</p>
                <span class="kpi-delta neutral mt-1 inline-block text-[10px]">App environment</span>
            </div>
            <div class="kpi-card p-3.5">
                <div class="accent-bar" style="background:linear-gradient(90deg,#34d399,#6ee7b7)"></div>
                <p class="kpi-eyebrow text-[10px]">Version</p>
                <p class="kpi-value mt-1 text-sm">{{ config('app.version', '1.0') }}</p>
                <span class="kpi-delta up mt-1 inline-block text-[10px]">App version</span>
            </div>
            <div class="kpi-card p-3.5">
                <div class="accent-bar" style="background:linear-gradient(90deg,#818cf8,#a5b4fc)"></div>
                <p class="kpi-eyebrow text-[10px]">Last saved</p>
                <p class="kpi-value mt-1 text-xs">{{ now()->format('d M Y') }}</p>
                <span class="kpi-delta neutral mt-1 inline-block text-[10px]">Today</span>
            </div>
            <div class="kpi-card p-3.5">
                <div class="accent-bar" style="background:linear-gradient(90deg,#fb7185,#fda4af)"></div>
                <p class="kpi-eyebrow text-[10px]">Changes</p>
                <p class="kpi-value mt-1 text-sm">{{ \App\Models\ChangeRequest::where('type', 'setting')->pending()->count() }}</p>
                <span class="kpi-delta neutral mt-1 inline-block text-[10px]">Pending approvals</span>
            </div>
        </div>
    </div>

    @php
    // One card per original settings group, with friendly display names — no merging.
    $groupTitles = [
        'general'       => 'General Settings',
        'quotations'    => 'Quotation Settings',
        'appearance'    => 'Appearance',
        'notifications' => 'Notifications',
        'email'         => 'Mail Settings',
        'api'           => 'API Settings',
        'integrations'  => 'Integrations',
        'security'      => 'Security',
    ];

    $groupDescriptions = [
        'general'       => 'Core site information — name, timezone, currency, and defaults.',
        'quotations'    => 'Rules and defaults used when generating quotations.',
        'appearance'    => 'Branding, logo, favicon, and theme colour.',
        'notifications' => 'Email and system alert preferences.',
        'email'         => 'SMTP and email delivery settings for system notifications and quotations.',
        'api'           => 'API keys, tokens, and rate limits.',
        'integrations'  => 'Third-party service connections.',
        'security'      => 'Password policy, 2FA, and access controls.',
    ];
@endphp

    <div class="space-y-4">
        @foreach($settings as $bucketName => $bucketSettings)
            @continue($bucketSettings->isEmpty())

            {{-- Card: matches profile page's bordered white section --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">

                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf

                    {{-- Card header row: title left, small action button right — mirrors "My Profile" / "Edit" row --}}
                    <div class="flex items-start justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                        <div>
                            <h2 class="text-sm font-bold text-gray-900 dark:text-white">{{ $groupTitles[$bucketName] ?? \Illuminate\Support\Str::title(str_replace('_', ' ', $bucketName)) }}</h2>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">{{ $groupDescriptions[$bucketName] ?? '' }}</p>
                        </div>
                        <button type="submit"
                                class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-semibold text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Save
                        </button>
                    </div>

                    {{-- Field rows: tight label/value layout like profile's First Name / Last Name grid --}}
                    <div class="px-5 py-3 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-3.5">
                        @foreach($bucketSettings as $setting)
                            @php
                                $key = $setting->key;
                                $value = $setting->value;
                                $inputType = 'text';
                                $options = [];

                                if (in_array($value, ['true', 'false']) || str_contains($key, 'enable') || str_contains($key, 'require') || str_contains($key, 'notify_on') || str_contains($key, 'two_factor') || str_contains($key, 'collapsed') || str_contains($key, 'social_login') || str_contains($key, 'public_api') || str_contains($key, 'pvt')) {
                                    $inputType = 'boolean';
                                } elseif (str_contains($key, 'password') && !str_contains($key, 'min_length')) {
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
                                } elseif (is_numeric($value) || str_contains($key, 'count') || str_contains($key, 'limit') || str_contains($key, 'days') || str_contains($key, 'lifetime') || str_contains($key, 'length') || str_contains($key, 'rate') || str_contains($key, 'per_page') || str_contains($key, 'port') || str_contains($key, 'valuation')) {
                                    $inputType = 'number';
                                }
                            @endphp

                            <div>
                                <label class="block text-[10.5px] font-medium text-gray-400 dark:text-gray-500 capitalize mb-1">
                                    {{ str_replace('_', ' ', $key) }}
                                </label>

                                @if($inputType === 'boolean')
                                    <select name="settings[{{ $key }}]"
                                            class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
                                        <option value="true" {{ $value === 'true' ? 'selected' : '' }}>Enabled</option>
                                        <option value="false" {{ $value === 'false' ? 'selected' : '' }}>Disabled</option>
                                    </select>
                                @elseif($inputType === 'select' && count($options) > 0)
                                    <select name="settings[{{ $key }}]"
                                            class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
                                        @foreach($options as $option)
                                            <option value="{{ $option }}" {{ $value === $option ? 'selected' : '' }}>{{ $option }}</option>
                                        @endforeach
                                    </select>
                                @elseif($inputType === 'color')
                                    <div class="flex items-center gap-2">
                                        <input type="color" name="settings[{{ $key }}]" value="{{ $value }}"
                                               class="w-9 h-7 rounded border border-gray-200 dark:border-gray-600 cursor-pointer">
                                        <input type="text" name="settings[{{ $key }}]" value="{{ $value }}"
                                               class="flex-1 px-2.5 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
                                    </div>
                                @elseif($inputType === 'password')
                                    <input type="password" name="settings[{{ $key }}]" value="{{ $value }}"
                                           class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none"
                                           autocomplete="off">
                                @elseif($inputType === 'media')
                                    <div class="flex items-center gap-1.5">
                                        <input type="text" name="settings[{{ $key }}]" value="{{ $value }}"
                                               class="flex-1 px-2.5 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none"
                                               id="setting-{{ $key }}" readonly
                                               onclick="openMediaLibrary('setting-{{ $key }}')" style="cursor:pointer">
                                        <button type="button" onclick="openMediaLibrary('setting-{{ $key }}')"
                                                class="px-2.5 py-1.5 text-[11px] font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition whitespace-nowrap">
                                            Browse
                                        </button>
                                        @if($value)
                                            <a href="{{ str_starts_with($value, 'images/') ? asset($value) : (file_exists(public_path('storage/' . $value)) ? asset('storage/' . $value) : '#') }}"
                                               target="_blank"
                                               class="px-1.5 py-1.5 text-[11px] text-gray-500 hover:text-blue-600 transition">
                                                Preview
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <input type="{{ $inputType }}" name="settings[{{ $key }}]" value="{{ $value }}"
                                           class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="h-3"></div>
                </form>
            </div>
        @endforeach
    </div>
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
                grid.innerHTML = '<div class="col-span-full text-center py-12 text-sm text-gray-400">No images yet.</div>';
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