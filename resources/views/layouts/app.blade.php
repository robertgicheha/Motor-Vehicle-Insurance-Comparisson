<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('theme') === 'dark', sidebarOpen: window.innerWidth >= 1024, mobileSidebarOpen: false }" x-init="
    darkMode ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark');
    $watch('darkMode', val => {
        document.documentElement.classList.toggle('dark', val);
        localStorage.setItem('theme', val ? 'dark' : 'light');
    });
" x-on:sidebar-expand.window="sidebarOpen = true" x-on:sidebar-close-mobile.window="mobileSidebarOpen = false">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ config('app.name', 'Motor Vehicle Insurance Dashboard') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('Favicon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('Favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @php
        $__recaptchaSiteKey = \App\Models\Setting::getValue('recaptcha_site_key') ?: config('services.recaptcha.site_key');
    @endphp
    @if($__recaptchaSiteKey)
        <script src="https://www.google.com/recaptcha/api.js?render={{ $__recaptchaSiteKey }}"></script>
        <script>window.mvipRecaptchaSiteKey = @json($__recaptchaSiteKey);</script>
    @endif
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-transition { transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1), transform 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
        .sidebar-sub-link { position: relative; }
        .sidebar-sub-link::before { content:''; position:absolute; left:14px; top:50%; width:4px; height:4px; border-radius:50%; background:rgba(255,255,255,0.15); transform:translateY(-50%); transition: all 0.15s; }
        .sidebar-sub-link:hover::before, .sidebar-sub-link.bg-blue-500\/10::before { background:rgba(59,130,246,0.6); width:5px; height:5px; }
        .scrollbar-thin::-webkit-scrollbar { width:4px; }
        .scrollbar-thin::-webkit-scrollbar-track { background:transparent; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background:rgba(255,255,255,0.08); border-radius:4px; }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover { background:rgba(255,255,255,0.15); }
        .fade-in { animation: fadeIn 0.5s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideIn { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }
        .slide-in { animation: slideIn 0.3s ease-out; }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 10px 40px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="font-montserrat antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 overflow-x-hidden">
    <div class="min-h-screen flex overflow-x-hidden">
        <div x-show="mobileSidebarOpen" x-transition:enter="transition-opacity duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="mobileSidebarOpen = false" class="fixed inset-0 bg-black/50 z-30 lg:hidden" x-cloak></div>

        @auth
            @include('layouts.sidebar')
        @endauth

        <div class="flex-1 flex flex-col min-w-0 overflow-x-hidden transition-all duration-300" :class="sidebarOpen ? 'lg:ml-60' : 'lg:ml-[68px]'" style="">
            @auth
                @include('layouts.navigation')
            @endauth

            <main class="flex-1 p-4 md:p-6 lg:p-8 pt-18">
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-lg text-red-700 dark:text-red-300 fade-in">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot }}
            </main>

            <footer class="p-4 md:p-6 text-center text-sm text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-gray-700">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </footer>
        </div>
    </div>

    <x-toast />
    @auth
        <x-quick-search />
    @endauth
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="{{ asset('js/dashboard-charts.js') }}"></script>
    <script src="{{ asset('js/user-dashboard-charts.js') }}" defer></script>

    @stack('scripts')
</body>
</html>
