<x-auth-layout title="Reset Password">
    <p class="mb-6 text-sm text-center text-gray-500">
        Reset your password if you've forgotten it, or if your password has expired. Confirm your identity with your
        username and current password, then choose a new strong one.
    </p>

    @if ($errors->any())
        <div class="mb-4 p-3 rounded-md bg-red-50 border border-red-200">
            @foreach ($errors->all() as $error)
                <p class="text-sm text-red-600">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.reset.submit') }}" class="space-y-5" id="resetForm" autocomplete="off">
        @csrf

        <div>
            <label for="username" class="block text-sm font-medium text-gray-700">Username <span class="text-red-500">*</span></label>
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus
                       placeholder="user1_westlands"
                       class="block w-full py-2 pl-10 border border-gray-300 rounded-md focus:ring-[#0066B3] focus:border-[#0066B3] sm:text-sm">
            </div>
        </div>

        <div>
            <label for="current_password" class="block text-sm font-medium text-gray-700">Current password <span class="text-red-500">*</span></label>
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <input id="current_password" type="password" name="current_password" required autocomplete="current-password"
                       placeholder="Your current or expired password"
                       class="block w-full py-2 pl-10 border border-gray-300 rounded-md focus:ring-[#0066B3] focus:border-[#0066B3] sm:text-sm">
            </div>
        </div>

        <div>
            <label for="newPassword" class="block text-sm font-medium text-gray-700">New password <span class="text-red-500">*</span></label>
            <div class="relative mt-1">
                <input id="newPassword" type="password" name="password" required minlength="12" autocomplete="new-password"
                       class="block w-full py-2 px-3 border border-gray-300 rounded-md focus:ring-[#0066B3] focus:border-[#0066B3] sm:text-sm">
            </div>

            {{-- Password strength meter --}}
            <div class="mt-2" id="pwStrengthWrap">
                <div class="h-1.5 w-full rounded-full bg-gray-200 overflow-hidden">
                    <div id="pwStrengthBar" class="h-full w-0 rounded-full transition-all duration-300" style="background:#ef4444"></div>
                </div>
                <div class="flex items-center justify-between mt-1">
                    <p id="pwStrengthLabel" class="text-[10px] font-medium text-gray-400">Password strength</p>
                    <p class="text-[10px] text-gray-400">Min 12 chars</p>
                </div>
                <ul class="mt-2 grid grid-cols-2 gap-1 text-[10px] text-gray-400" id="pwChecklist">
                    <li data-rule="length" class="flex items-center gap-1"><span class="pw-dot">&#9675;</span> 12+ characters</li>
                    <li data-rule="lower" class="flex items-center gap-1"><span class="pw-dot">&#9675;</span> Lowercase letter</li>
                    <li data-rule="upper" class="flex items-center gap-1"><span class="pw-dot">&#9675;</span> Uppercase letter</li>
                    <li data-rule="number" class="flex items-center gap-1"><span class="pw-dot">&#9675;</span> Number</li>
                    <li data-rule="symbol" class="flex items-center gap-1"><span class="pw-dot">&#9675;</span> Symbol</li>
                </ul>
            </div>
        </div>

        <div>
            <label for="confirmPassword" class="block text-sm font-medium text-gray-700">Confirm new password <span class="text-red-500">*</span></label>
            <div class="relative mt-1">
                <input id="confirmPassword" type="password" name="password_confirmation" required autocomplete="new-password"
                       class="block w-full py-2 px-3 border border-gray-300 rounded-md focus:ring-[#0066B3] focus:border-[#0066B3] sm:text-sm">
            </div>
            <p id="pwMatchMsg" class="text-[10px] mt-1 hidden"></p>
        </div>

        <button type="submit" id="pwSubmitBtn"
                class="flex justify-center items-center gap-2 w-full px-4 py-2 text-sm font-medium text-white transition border border-transparent rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0066B3] disabled:opacity-50 disabled:cursor-not-allowed"
                style="background:var(--button-blue)">
            Reset password
        </button>
    </form>

    <p class="mt-6 text-xs text-center text-gray-400">
        <a href="{{ route('login') }}" class="text-[#263B8B] hover:underline">Back to sign in</a>
        <span class="mx-1">&middot;</span>
        Completely forgotten your password? Ask your administrator to reset it.
    </p>

    <script>
    (function () {
        const pw = document.getElementById('newPassword');
        const confirm = document.getElementById('confirmPassword');
        const bar = document.getElementById('pwStrengthBar');
        const label = document.getElementById('pwStrengthLabel');
        const submit = document.getElementById('pwSubmitBtn');
        const matchMsg = document.getElementById('pwMatchMsg');
        if (!pw || !bar) return;

        const rules = {
            length: v => v.length >= 12,
            lower: v => /[a-z]/.test(v),
            upper: v => /[A-Z]/.test(v),
            number: v => /[0-9]/.test(v),
            symbol: v => /[^A-Za-z0-9]/.test(v),
        };

        const levels = [
            { pct: 0, color: '#ef4444', text: 'Very weak' },
            { pct: 20, color: '#ef4444', text: 'Very weak' },
            { pct: 40, color: '#f59e0b', text: 'Weak' },
            { pct: 60, color: '#eab308', text: 'Fair' },
            { pct: 80, color: '#22c55e', text: 'Strong' },
            { pct: 100, color: '#16a34a', text: 'Very strong' },
        ];

        function evaluate() {
            const v = pw.value;
            let passed = 0;
            document.querySelectorAll('#pwChecklist li').forEach(li => {
                const ok = rules[li.dataset.rule](v);
                if (ok) passed++;
                li.querySelector('.pw-dot').textContent = ok ? '\u25CF' : '\u25CB';
                li.classList.toggle('text-green-500', ok);
                li.classList.toggle('text-gray-400', !ok);
            });

            const level = levels[passed];
            bar.style.width = level.pct + '%';
            bar.style.background = level.color;
            label.textContent = v ? level.text : 'Password strength';
            label.style.color = v ? level.color : '';

            updateSubmit();
            return passed === 5;
        }

        function matches() {
            return confirm && confirm.value !== '' && pw.value === confirm.value;
        }

        function updateSubmit() {
            if (submit) submit.disabled = !(evaluateStrong() && matches());
        }

        function evaluateStrong() {
            const v = pw.value;
            return v.length >= 12 && /[a-z]/.test(v) && /[A-Z]/.test(v) && /[0-9]/.test(v) && /[^A-Za-z0-9]/.test(v);
        }

        function checkMatch() {
            if (!confirm || !matchMsg) return;
            if (!confirm.value) { matchMsg.classList.add('hidden'); updateSubmit(); return; }
            const match = pw.value === confirm.value;
            matchMsg.classList.remove('hidden');
            matchMsg.textContent = match ? 'Passwords match' : 'Passwords do not match';
            matchMsg.classList.toggle('text-green-500', match);
            matchMsg.classList.toggle('text-red-500', !match);
            updateSubmit();
        }

        pw.addEventListener('input', function () { evaluate(); checkMatch(); });
        confirm && confirm.addEventListener('input', checkMatch);
        evaluate();
    })();
    </script>
</x-auth-layout>
