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

    <div class="mt-6 text-sm text-center">
        <span class="text-gray-500">Already have an account?</span>
        <a href="{{ route('login') }}" class="font-medium" style="color:var(--blue)">Login</a>
    </div>
</x-auth-layout>