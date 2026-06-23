<x-auth-layout title="Two-Factor Verification">
    <div class="text-center mb-8">
        <p class="text-sm text-gray-500">
            We've emailed you a 6-digit verification code<br>we sent to
        </p>
        <p class="mt-2 text-2xl font-bold tracking-widest text-gray-800">******6789</p>
    </div>

    <form method="POST" action="/two-factor-challenge" class="space-y-6">
        @csrf
        
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">Enter your 6-digit code<span class="text-red-500">*</span></label>
            <div class="flex gap-2 justify-between">
                @for ($i = 0; $i < 6; $i++)
                    <input type="text" maxlength="1" name="code[]" class="w-12 h-12 text-xl text-center border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                @endfor
            </div>
        </div>

        <button type="submit" class="flex justify-center w-full px-4 py-2 text-sm font-medium text-white transition bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700">
            Confirm
        </button>
    </form>

    <div class="mt-6 text-sm text-center text-gray-500">
        Don't have a code? <a href="#" class="text-indigo-600 hover:underline border-b border-indigo-600 pb-0.5">Resend</a> or <a href="#" class="text-indigo-600 hover:underline border-b border-indigo-600 pb-0.5">Call Us</a>
    </div>
    
    <div class="mt-4 text-sm text-center">
        <span class="text-gray-500">Return to</span>
        <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">Sign in</a>
    </div>
</x-auth-layout>