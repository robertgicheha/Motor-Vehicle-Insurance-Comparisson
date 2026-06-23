<x-auth-layout title="New Password">
    <p class="mb-8 text-sm text-center text-gray-500">
        We've emailed you a 6-digit verification code.<br>Please enter it below to confirm your Email Address.
    </p>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">Enter your 6-digit code<span class="text-red-500">*</span></label>
            <div class="flex gap-2 justify-between">
                @for ($i = 0; $i < 6; $i++)
                    <input type="text" maxlength="1" class="w-12 h-12 text-xl text-center border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                @endfor
            </div>
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">New Password <span class="text-red-500">*</span></label>
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <input id="password" type="password" name="password" required placeholder="••••••••" class="block w-full py-2 pl-10 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <p class="mt-2 text-xs text-gray-400">Use 8+ characters with letters, numbers & symbols.</p>
        </div>

        <div class="flex items-center">
            <input id="terms" name="terms" type="checkbox" required class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" checked>
            <label for="terms" class="block ml-2 text-sm text-gray-900">Agree the Terms & Policy</label>
        </div>

        <button type="submit" class="flex justify-center w-full px-4 py-2 text-sm font-medium text-white transition bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700">
            Update Password
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