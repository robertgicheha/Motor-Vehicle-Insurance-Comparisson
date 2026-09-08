<x-auth-layout title="Account Pending Verification">
    <div class="text-center">
        <div class="mx-auto flex items-center justify-center w-16 h-16 rounded-full bg-amber-50">
            <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z"/>
            </svg>
        </div>

        <h1 class="mt-5 text-lg font-bold text-gray-900">Account Pending Verification</h1>

        <p class="mt-3 text-sm text-gray-500 leading-relaxed">
            Your account has been created but is pending verification. Kindly remain patient as we take time to verify your account.
        </p>

        <p class="mt-3 text-sm text-gray-500 leading-relaxed">
            You will receive an email as soon as your account has been verified — after that you'll be able to log in, generate quotations, and request stickers.
        </p>

        <a href="{{ route('login') }}"
           class="mt-8 inline-flex items-center justify-center gap-2 w-full py-2.5 rounded-md text-sm font-semibold text-white transition"
           style="background:#0066B3">
            Return to Login
        </a>
    </div>
</x-auth-layout>
