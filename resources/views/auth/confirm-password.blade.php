<x-auth-layout title="Confirm Password">
    <div class="flex justify-center mb-4">
        <div class="p-3 bg-indigo-50 rounded-full">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
        </div>
    </div>

    <p class="mb-8 text-sm text-center text-gray-500">
        This is a secure area of the application.<br>Please confirm your password before continuing.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
        @csrf
        
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password <span class="text-red-500">*</span></label>
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" class="block w-full py-2 pl-10 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            
            @if (Route::has('password.request'))
                <div class="flex justify-end mt-2">
                    <a href="{{ route('password.request') }}" class="text-xs text-indigo-600 hover:text-indigo-500">
                        Forgot your password?
                    </a>
                </div>
            @endif
        </div>

        <button type="submit" class="flex justify-center w-full px-4 py-2 text-sm font-medium text-white transition bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Confirm Password
        </button>
    </form>

    <div class="mt-6 text-sm text-center">
        <span class="text-gray-500">Changed your mind?</span>
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
            Go back
        </a>
    </div>
</x-auth-layout>