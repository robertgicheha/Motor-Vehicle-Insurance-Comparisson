<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches) }" x-init="darkMode ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark')">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .gradient-bg { background: linear-gradient(135deg, #1e40af 0%, #7c3aed 50%, #db2777 100%); }
        .float { animation: float 6s ease-in-out infinite; }
        @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-20px); } }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen gradient-bg">
        <nav class="flex items-center justify-between px-6 py-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <span class="text-white font-bold text-lg">InsureDash</span>
            </div>
            <div class="flex items-center gap-4">
                <button @click="darkMode = !darkMode" class="p-2 rounded-lg text-white/80 hover:text-white hover:bg-white/10">
                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-5 py-2 bg-white text-gray-900 rounded-lg hover:bg-gray-100 transition font-medium">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="px-5 py-2 text-white/90 hover:text-white font-medium">Sign In</a>
                        <a href="{{ route('register') }}" class="px-5 py-2 bg-white text-gray-900 rounded-lg hover:bg-gray-100 transition font-medium">Get Started</a>
                    @endauth
                @endif
            </div>
        </nav>

        <main class="max-w-6xl mx-auto px-6 py-16 md:py-24">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <h1 class="text-4xl md:text-6xl font-bold text-white leading-tight">
                        Compare Motor Vehicle Insurance Premiums
                    </h1>
                    <p class="text-lg text-white/80 leading-relaxed">
                        Get instant comprehensive insurance quotes from Kenya's leading insurers. 
                        Compare GA Insurance and Sanlam Allianz premiums side by side.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        @auth
                            <a href="{{ route('quotations.create') }}" class="px-8 py-3 bg-white text-gray-900 rounded-xl hover:bg-gray-100 transition font-semibold shadow-lg">
                                Generate Quotation
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="px-8 py-3 bg-white text-gray-900 rounded-xl hover:bg-gray-100 transition font-semibold shadow-lg">
                                Get Started Free
                            </a>
                            <a href="{{ route('login') }}" class="px-8 py-3 border border-white/30 text-white rounded-xl hover:bg-white/10 transition font-semibold">
                                Sign In
                            </a>
                        @endauth
                    </div>
                </div>
                <div class="hidden lg:block">
                    <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 border border-white/20 float">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between pb-4 border-b border-white/20">
                                <span class="text-white font-semibold">Premium Comparison</span>
                                <span class="text-white/60 text-sm">KES</span>
                            </div>
                            <div class="space-y-3">
                                <div class="flex justify-between text-white"><span>GA Insurance</span><span class="font-bold">7.5%</span></div>
                                <div class="h-2 bg-white/20 rounded-full"><div class="h-2 bg-white rounded-full" style="width:75%"></div></div>
                                <div class="flex justify-between text-white"><span>Sanlam Allianz</span><span class="font-bold">6.5%</span></div>
                                <div class="h-2 bg-white/20 rounded-full"><div class="h-2 bg-white rounded-full" style="width:65%"></div></div>
                            </div>
                            <div class="pt-4 border-t border-white/20">
                                <p class="text-white/60 text-sm">Car Valuation Range</p>
                                <p class="text-white font-bold">KES 800,000 - KES 1,300,000</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-24 grid md:grid-cols-3 gap-6">
                <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                    <div class="w-12 h-12 rounded-lg bg-white/20 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="text-white font-semibold mb-2">Instant Quotes</h3>
                    <p class="text-white/70 text-sm">Get premium calculations instantly for comprehensive motor vehicle insurance.</p>
                </div>
                <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                    <div class="w-12 h-12 rounded-lg bg-white/20 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    </div>
                    <h3 class="text-white font-semibold mb-2">Side by Side</h3>
                    <p class="text-white/70 text-sm">Compare coverage limits, excess amounts, and premium rates across insurers.</p>
                </div>
                <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                    <div class="w-12 h-12 rounded-lg bg-white/20 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h3 class="text-white font-semibold mb-2">Detailed Reports</h3>
                    <p class="text-white/70 text-sm">Generate comprehensive reports with charts and export to CSV or Excel.</p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
