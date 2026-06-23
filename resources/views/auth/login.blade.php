<x-auth-layout title="Sign In">
    <p class="mb-8 text-sm text-center text-gray-500">
        Let's get you signed in. Enter your email and password to continue.
    </p>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf
        
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

        <button type="submit" class="flex justify-center w-full px-4 py-2 text-sm font-medium text-white transition border border-transparent rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0066B3]" style="background:var(--blue)" onmouseover="this.style.background='var(--blue-dark)'" onmouseout="this.style.background='var(--blue)'">
            Sign In
        </button>
    </form>

    <div class="mt-6 text-sm text-center">
        <span class="text-gray-500">New here?</span>
        <a href="{{ route('register') }}" class="font-medium" style="color:var(--blue)">Create an account</a>
    </div>
</x-auth-layout>