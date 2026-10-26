<x-app-layout>
<div class="space-y-5 pt-8 fade-in max-w-4xl mx-auto">

    @if(session('success'))
        <div class="rounded-xl border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 px-4 py-3 text-xs text-green-700 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 px-4 py-3 text-xs text-red-700 dark:text-red-300">
            {{ session('error') }}
        </div>
    @endif

    {{-- ─── HERO ─────────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-micblue px-6 py-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">System</p>
                <h1 class="mt-0.5 text-lg font-bold text-white">Email Settings</h1>
                <p class="mt-0.5 text-[11px] text-slate-400">Configure LOLC's Custom SMTP server used for all system emails</p>
            </div>
            <span class="inline-flex items-center gap-1 self-start px-2.5 py-1 rounded-lg bg-amber-500/20 text-amber-200 text-[11px] font-semibold">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Superadmin only
            </span>
        </div>
    </div>

    {{-- ─── SECURITY NOTE ────────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/20 px-4 py-3.5">
        <div class="flex gap-3">
            <svg class="w-4 h-4 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="text-[11px] leading-relaxed text-blue-800 dark:text-blue-200">
                <p class="font-semibold">Credentials are stored securely in the server's <code class="px-1 rounded bg-blue-100 dark:bg-blue-900/40">.env</code> file as environment variables, separate from application code and never in the database.</p>
                <p class="mt-1">They are encrypted using Laravel's built-in environment encryption, and this page is strictly restricted to the <strong>Superadmin</strong> role. Use <strong>Encrypt environment file</strong> below to write the encrypted <code class="px-1 rounded bg-blue-100 dark:bg-blue-900/40">.env.encrypted</code> copy.</p>
            </div>
        </div>
    </div>

    {{-- ─── LOLC CUSTOM SMTP ─────────────────────────────────────────────── --}}
    <form action="{{ route('admin.email-config.update') }}" method="POST" class="space-y-5">
        @csrf
        <input type="hidden" name="provider" value="lolc">

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white">Custom SMTP — LOLC Mail Server</h2>
                    <p class="text-[11px] text-gray-400 mt-0.5">All portal emails are sent through LOLC's SMTP server.</p>
                </div>
                <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300">LOLC</span>
            </div>
            <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1.5">SMTP Host <span class="text-rose-500">*</span></label>
                    <input type="text" name="lolc_host" value="{{ old('lolc_host', $lolc['host']) }}" placeholder="mail.lolc.com" required
                           class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('lolc_host') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1.5">SMTP Port <span class="text-rose-500">*</span></label>
                    <input type="number" name="lolc_port" value="{{ old('lolc_port', $lolc['port']) }}" placeholder="587" min="1" max="65535" required
                           class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('lolc_port') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1.5">Encryption</label>
                    <select name="lolc_encryption" class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="" {{ old('lolc_encryption', $lolc['encryption']) === '' || old('lolc_encryption', $lolc['encryption']) === null ? 'selected' : '' }}>None / auto</option>
                        <option value="tls" {{ old('lolc_encryption', $lolc['encryption']) === 'tls' ? 'selected' : '' }}>TLS (STARTTLS — port 587)</option>
                        <option value="ssl" {{ old('lolc_encryption', $lolc['encryption']) === 'ssl' ? 'selected' : '' }}>SSL (implicit — port 465)</option>
                    </select>
                    @error('lolc_encryption') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1.5">SMTP Username</label>
                    <input type="text" name="lolc_username" value="{{ old('lolc_username', $lolc['username']) }}" autocomplete="off"
                           class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('lolc_username') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1.5">SMTP Password</label>
                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'" name="lolc_password" value="" autocomplete="new-password"
                               placeholder="{{ $lolc['password'] ? '•••••••• (leave blank to keep current)' : 'Enter password' }}"
                               class="w-full px-3.5 py-2.5 pr-10 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" tabindex="-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1">Write-only for security — never displayed once saved.</p>
                    @error('lolc_password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1.5">From Address</label>
                    <input type="email" name="lolc_from_address" value="{{ old('lolc_from_address', $lolc['from_address']) }}" placeholder="no-reply@lolc.com"
                           class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('lolc_from_address') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1.5">From Name</label>
                    <input type="text" name="lolc_from_name" value="{{ old('lolc_from_name', $lolc['from_name']) }}" placeholder="LOLC Microfinance Bank"
                           class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('lolc_from_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                Save Settings
            </button>
            @unless($envWritable)
                <span class="text-[11px] text-amber-600 dark:text-amber-400">The .env file is not writable — changes cannot be saved from here.</span>
            @endunless
        </div>
    </form>

    {{-- ─── ENVIRONMENT ENCRYPTION ───────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-sm font-bold text-gray-900 dark:text-white">Environment Encryption</h2>
            <p class="text-[11px] text-gray-400 mt-0.5">Encrypt the .env file at rest with Laravel's built-in environment encryption</p>
        </div>
        <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="text-[11px] text-gray-600 dark:text-gray-300">
                @if($envEncryptedAt)
                    <span class="inline-flex items-center gap-1.5 font-semibold text-emerald-600 dark:text-emerald-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Encrypted copy present
                    </span>
                    <p class="mt-1 text-gray-400">Last written {{ $envEncryptedAt }}. Keep <code>.env.encrypted</code> safe and never commit it.</p>
                @else
                    <p class="text-gray-400">No encrypted environment copy found yet. Run the action to create <code>.env.encrypted</code>.</p>
                @endif
            </div>
            <form action="{{ route('admin.email-config.encrypt') }}" method="POST" onsubmit="return confirm('Encrypt the .env file to .env.encrypted now?');">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-gray-800 hover:bg-gray-900 dark:bg-gray-700 dark:hover:bg-gray-600 text-white rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Encrypt environment file
                </button>
            </form>
        </div>
    </div>
</div>
</x-app-layout>
