<x-auth-layout title="Sign Up">
    <p class="mb-8 text-sm text-center text-gray-500">
        Create your account by entering the form below.
    </p>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf
        
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700">First Name <span class="text-red-500">*</span></label>
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <input id="first_name" type="text" name="first_name" required placeholder="Geneva" class="block w-full py-2 pl-10 border border-gray-300 rounded-md focus:ring-[#0066B3] focus:border-[#0066B3] sm:text-sm">
                </div>
            </div>
            <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name <span class="text-red-500">*</span></label>
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <input id="last_name" type="text" name="last_name" required placeholder="Kamau" class="block w-full py-2 pl-10 border border-gray-300 rounded-md focus:ring-[#0066B3] focus:border-[#0066B3] sm:text-sm">
                </div>
            </div>
        </div>

        <div>
            <label for="mobile" class="block text-sm font-medium text-gray-700">Mobile Number <span class="text-red-500">*</span></label>
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                </div>
                <input id="mobile" type="tel" name="mobile" required placeholder="0712 345 678" class="block w-full py-2 pl-10 border border-gray-300 rounded-md focus:ring-[#0066B3] focus:border-[#0066B3] sm:text-sm">
            </div>
        </div>

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
            <p class="mt-2 text-xs text-gray-400">Use 8+ characters with letters, numbers & symbols.</p>
        </div>

        <div class="flex items-center mt-4">
                <input id="terms" name="terms" type="checkbox" required class="w-4 h-4 border-gray-300 rounded focus:ring-[#0066B3]" style="color:var(--blue)">
            <label for="terms" class="block ml-2 text-sm text-gray-900">Agree the Terms & Policy</label>
        </div>

        <button type="submit" class="flex justify-center w-full px-4 py-2 mt-4 text-sm font-medium text-white transition border border-transparent rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0066B3]" style="background:var(--blue)" onmouseover="this.style.background='var(--blue-dark)'" onmouseout="this.style.background='var(--blue)'">
            Create Account
        </button>
    </form>

    <div class="relative my-6">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white text-gray-400">or sign up with</span>
        </div>
    </div>

    <a href="{{ route('auth.google') }}" class="flex items-center justify-center gap-3 w-full px-4 py-2.5 text-sm font-medium text-gray-600 border border-gray-200 rounded-md hover:bg-gray-50 transition">
        <svg class="w-5 h-5" viewBox="0 0 24 24">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
        </svg>
        Sign up with Google
    </a>

    <div class="mt-6 text-sm text-center">
        <span class="text-gray-500">Already have an account?</span>
        <a href="{{ route('login') }}" class="font-medium" style="color:var(--blue)">Login</a>
    </div>
</x-auth-layout>