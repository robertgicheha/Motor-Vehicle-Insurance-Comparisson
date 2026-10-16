<x-auth-layout title="Password Reset">
    <div class="text-center mb-6">
        <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-3">
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h2 class="text-lg font-bold text-gray-900">Password reset successful</h2>
        <p class="mt-1 text-sm text-gray-500">Use the password below to sign in. It will expire again in 30 days.</p>
    </div>

    @php
        $resetUsername = session('reset_username');
        $resetPassword = session('reset_password');
    @endphp

    <div class="space-y-4">
        <div>
            <label class="block text-[11px] font-semibold uppercase tracking-widest text-gray-400 mb-1.5">Username</label>
            <code class="block w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-sm font-mono text-gray-800">{{ $resetUsername }}</code>
        </div>

        <div>
            <label class="block text-[11px] font-semibold uppercase tracking-widest text-gray-400 mb-1.5">New password</label>
            <div class="flex items-center gap-2">
                <code id="reset-password" class="min-w-0 flex-1 break-all px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-sm font-mono text-gray-800">{{ $resetPassword }}</code>
                <button type="button" onclick="copyResetPassword()"
                        class="shrink-0 inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold bg-emerald-500 hover:bg-emerald-400 text-white rounded-md transition">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    <span id="copyResetLabel">Copy</span>
                </button>
            </div>
            <p class="mt-2 text-[11px] text-amber-600">Save this password now — for your security it will not be shown again.</p>
        </div>

        <a href="{{ route('login') }}"
           class="flex justify-center items-center w-full px-4 py-2 text-sm font-medium text-white rounded-md shadow-sm"
           style="background:var(--button-blue)">
            Go to sign in
        </a>
    </div>

    <script>
    function copyResetPassword() {
        var el = document.getElementById('reset-password');
        var label = document.getElementById('copyResetLabel');
        if (!el) return;
        var text = el.textContent;
        var done = function () {
            if (label) { label.textContent = 'Copied!'; setTimeout(function () { label.textContent = 'Copy'; }, 2000); }
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done).catch(done);
        } else {
            var ta = document.createElement('textarea');
            ta.value = text;
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy'); } catch (e) {}
            document.body.removeChild(ta);
            done();
        }
    }
    </script>
</x-auth-layout>
