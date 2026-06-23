<x-auth-layout title="Sign Up">
    <p class="mb-8 text-sm text-center text-gray-500">
        Create your account by entering the form below.
    </p>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf
        
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Full Name <span class="text-red-500">*</span></label>
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <input id="name" type="text" name="name" required placeholder="Geneva K." class="block w-full py-2 pl-10 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email address <span class="text-red-500">*</span></label>
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <input id="email" type="email" name="email" required placeholder="you@example.com" class="block w-full py-2 pl-10 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password <span class="text-red-500">*</span></label>
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <input id="password" type="password" name="password" required placeholder="••••••••" class="block w-full py-2 pl-10 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <p class="mt-2 text-xs text-gray-400">Use 8+ characters with letters, numbers & symbols.</p>
        </div>

        <div class="flex items-center mt-4">
            <input id="terms" name="terms" type="checkbox" required class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
            <label for="terms" class="block ml-2 text-sm text-gray-900">Agree the Terms & Policy</label>
        </div>

        <button type="submit" class="flex justify-center w-full px-4 py-2 mt-4 text-sm font-medium text-white transition bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700">
            Create Account
        </button>
    </form>

    <div class="mt-6 text-sm text-center">
        <span class="text-gray-500">Already have an account?</span>
        <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">Login</a>
    </div>
</x-auth-layout>