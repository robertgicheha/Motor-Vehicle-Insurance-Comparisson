<x-auth-layout title="Sign In">
    <p class="mb-8 text-sm text-center text-gray-500">
        Let's get you signed in. Enter your email and password to continue.
    </p>

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
            <label for="email" class="block text-sm font-medium text-gray-700">Email address <span class="text-red-500">*</span></label>
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <input id="email" type="email" name="email" required placeholder="you@example.com" class="block w-full py-2 pl-10 border border-gray-300 rounded-md focus:ring-[#0066B3] focus:border-[#0066B3] sm:text-sm">
            </div>
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password <span class="text-red-500">*</span></label>
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <input id="password" type="password" name="password" required placeholder="••••••••" class="block w-full py-2 pl-10 border border-gray-300 rounded-md focus:ring-[#0066B3] focus:border-[#0066B3] sm:text-sm">
            </div>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input id="remember" name="remember" type="checkbox" class="w-4 h-4 border-gray-300 rounded focus:ring-[#0066B3]" style="color:var(--blue)">
                <label for="remember" class="block ml-2 text-sm text-gray-900">Keep me signed in</label>
            </div>
            <div class="text-sm">
                <a href="{{ route('password.request') }}" class="text-gray-500 hover:text-[#0066B3]">Forgot Password?</a>
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

    <div class="relative my-6">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white text-gray-400">or continue with</span>
        </div>
    </div>

    <a href="{{ route('auth.google') }}" class="flex items-center justify-center gap-3 w-full px-4 py-2.5 text-sm font-medium text-gray-600 border border-gray-200 rounded-md hover:bg-gray-50 transition">
        <svg class="w-5 h-5" viewBox="0 0 24 24">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
        </svg>
        Sign in with Google
    </a>

    <div class="mt-6 text-sm text-center">
        <span class="text-gray-500">New here?</span>
        <a href="{{ route('register') }}" class="font-medium" style="color:var(--button-blue)">Create an account</a>
    </div>

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