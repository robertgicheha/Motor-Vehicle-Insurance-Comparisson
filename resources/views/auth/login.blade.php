<x-auth-layout title="Sign In">
    <p class="mb-8 text-sm text-center text-gray-500">
        Let's get you signed in. Enter your username and password to continue.
    </p>

    @if (session('status'))
        <div class="mb-4 p-3 rounded-md bg-emerald-50 border border-emerald-200">
            <p class="text-sm text-emerald-700">{{ session('status') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-3 rounded-md bg-red-50 border border-red-200">
            @foreach ($errors->all() as $error)
                <p class="text-sm text-red-600">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-6" id="loginForm" onsubmit="showLoginSpinner(this)">
        @csrf
        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

        <div>
            <label for="username" class="block text-sm font-medium text-gray-700">Username <span class="text-red-500">*</span></label>
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username" placeholder="user1_westlands" class="block w-full py-2 pl-10 border border-gray-300 rounded-md focus:ring-[#0066B3] focus:border-[#0066B3] sm:text-sm">
            </div>
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password <span class="text-red-500">*</span></label>
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" class="block w-full py-2 pl-10 border border-gray-300 rounded-md focus:ring-[#0066B3] focus:border-[#0066B3] sm:text-sm">
            </div>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input id="remember" name="remember" type="checkbox" class="w-4 h-4 border-gray-300 rounded focus:ring-[#0066B3]" style="color:var(--blue)">
                <label for="remember" class="block ml-2 text-sm text-gray-900">Keep me signed in</label>
            </div>
        </div>

        <button type="submit" id="loginBtn" class="flex justify-center items-center gap-2 w-full px-4 py-2 text-sm font-medium text-white transition border border-transparent rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0066B3]" style="background:var(--button-blue)" onmouseover="this.style.background='var(--blue-dark)'" onmouseout="this.style.background='var(--blue)'">
            <span id="loginBtnText">Sign In</span>
            <span id="loginBtnSpinner" style="display:none" class="inline-flex items-center gap-2">
                <svg class="animate-spin" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity="0.25"/><path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
                Please wait...logging in
            </span>
        </button>
    </form>

    <div class="mt-6 flex flex-col items-center gap-1">
        <a href="{{ route('password.reset') }}" class="text-xs font-medium text-[#263B8B] hover:underline">
            Forgot your password?
        </a>
        <a href="{{ route('password.reset') }}" class="text-xs font-medium text-[#263B8B] hover:underline">
            Password expired? Reset it here
        </a>
    </div>

    <p class="mt-4 text-xs text-center text-gray-400">
        Accounts are issued by your administrator. Completely forgotten your password? Ask them to reset it.
    </p>

    <script>
    function showLoginSpinner(form) {
        document.getElementById('loginBtnText').style.display = 'none';
        document.getElementById('loginBtnSpinner').style.display = 'inline-flex';
        document.getElementById('loginBtn').disabled = true;
    }

    document.getElementById('loginForm').addEventListener('submit', function (e) {
        if (typeof grecaptcha === 'undefined' || !window.mvipRecaptchaSiteKey) return;
        e.preventDefault();
        var form = this;
        grecaptcha.ready(function () {
            grecaptcha.execute(window.mvipRecaptchaSiteKey, { action: 'login' }).then(function (token) {
                document.getElementById('g-recaptcha-response').value = token;
                form.submit();
            });
        });
    });
    </script>
</x-auth-layout>