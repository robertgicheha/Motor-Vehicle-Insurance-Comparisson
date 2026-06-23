<x-auth-layout title="Email Verified">
    <div class="text-center">
        <div class="flex items-center justify-center w-16 h-16 mx-auto mb-6 bg-teal-50 rounded-full">
            <svg class="w-8 h-8 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        
        <h3 class="mb-8 text-xl font-semibold text-gray-800">Well Done! Email verified Successfully</h3>

        <a href="{{ route('dashboard') }}" class="flex justify-center w-full px-4 py-2 text-sm font-medium text-white transition bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700">
            Back to Dashboard
        </a>

        <div class="mt-6 text-sm text-center">
            <span class="text-gray-500">Return to</span>
            <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">Sign in</a>
        </div>
    </div>
</x-auth-layout>