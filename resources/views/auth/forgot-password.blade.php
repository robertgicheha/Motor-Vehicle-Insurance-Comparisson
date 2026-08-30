<x-auth-layout title="Forgot Password">
    <p class="mb-8 text-sm text-center text-gray-500">
        Enter your email address and we'll send you a link to reset your password.
    </p>

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6" id="forgotPasswordForm">
        @csrf
        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email address <span class="text-red-500">*</span></label>
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <input id="email" type="email" name="email" required placeholder="you@example.com" class="block w-full py-2 pl-10 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
        </div>

        <div class="flex items-center">
            <input id="terms" name="terms" type="checkbox" required class="w-4 h-4 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500">
            <label for="terms" class="block ml-2 text-sm text-gray-900">Agree the Terms & Policy</label>
        </div>

        <button type="submit" class="flex justify-center w-full px-4 py-2 text-sm font-medium text-white transition bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700">
            Send Request
        </button>
    </form>

    <div class="mt-6 text-sm text-center">
        <span class="text-gray-500">Return to</span>
        <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">Sign in</a>
    </div>

    <script>
    document.getElementById('forgotPasswordForm').addEventListener('submit', function (e) {
        if (typeof grecaptcha === 'undefined' || !window.mvipRecaptchaSiteKey) return;
        e.preventDefault();
        var form = this;
        grecaptcha.ready(function () {
            grecaptcha.execute(window.mvipRecaptchaSiteKey, { action: 'forgot_password' }).then(function (token) {
                document.getElementById('g-recaptcha-response').value = token;
                form.submit();
            });
        });
    });
    </script>
</x-auth-layout>