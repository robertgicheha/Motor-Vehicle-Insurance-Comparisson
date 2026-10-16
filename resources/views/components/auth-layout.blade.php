<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ?? 'Authentication' }} | Motor Vehicle Insurance Portal</title>
    {{-- <link rel="preconnect" href="https://fonts.bunny.net"> --}}
    {{-- <link href="https://fonts.bunny.net/css?family=Montserrat:300,400,500,600,700,800" rel="stylesheet" /> --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @php
        $__recaptchaSiteKey = \App\Models\Setting::getValue('recaptcha_site_key') ?: config('services.recaptcha.site_key');
    @endphp
    @if($__recaptchaSiteKey)
        <script src="https://www.google.com/recaptcha/api.js?render={{ $__recaptchaSiteKey }}"></script>
        <script>window.mvipRecaptchaSiteKey = @json($__recaptchaSiteKey);</script>
    @endif
</head>
<body class="font-montserrat antialiased text-gray-900 bg-white">
    <div class="flex min-h-screen">
        <div class="flex flex-col justify-center w-full lg:w-[40%] px-8 sm:px-16 xl:px-24">
            <div class="mb-10 text-center">
                <a href="{{ route('home') }}" class="flex flex-col items-center justify-center gap-3">
                    <img src="{{ asset('images/LOLC-Kenya.webp') }}" alt="LOLC Microfinance Bank" class="mx-auto mb-3" style="height:56px;width:auto" width="417" height="67">
                    <span style="color: #015F9E;font-size:20px;font-weight:700">Motor Vehicle Insurance Portal</span>
                </a>
            </div>

            <div class="w-full max-w-md mx-auto">
                {{ $slot }}
            </div>

            <div class="mt-12 text-sm text-center text-gray-500">
                &copy; {{ date('Y') }} Motor Vehicle Insurance Portal.
            </div>
        </div>

        <div class="hidden lg:block lg:w-[60%] relative bg-slate-800">
            <img src="{{ asset('images/hero/vehicle.jpg') }}"
                 alt="Team working"
                 class="absolute inset-0 object-cover w-full h-full opacity-40 mix-blend-overlay" width="1920" height="1280">
        </div>
    </div>
    <x-toast />
</body>
</html>
