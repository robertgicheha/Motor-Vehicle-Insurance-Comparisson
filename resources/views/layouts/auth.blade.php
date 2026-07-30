<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Authentication' }} | Motor Vehicle Insurance Portal</title>
    <!-- Standard favicon (browsers + bookmark bar) -->
    <link rel="icon" type="image/x-icon"       href="{{ asset('Favicon.png') }}">
    <link rel="icon" type="image/png" sizes="16x16"   href="{{ asset('Favicon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32"   href="{{ asset('Favicon.png') }}">
    <link rel="icon" type="image/png" sizes="96x96"   href="{{ asset('Favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-montserrat antialiased text-gray-900 bg-white">
    <div class="flex min-h-screen">
        <div class="flex flex-col justify-center w-full lg:w-[40%] px-8 sm:px-16 xl:px-24">
            <div class="mb-10 text-center">
                <a href="{{ route('home') }}" class="flex flex-col items-center justify-center gap-3">
                    <img src="{{ asset('images/MarshMIC.png') }}" alt="MIC Global Risks" class="mx-auto mb-3" style="height:56px;width:auto">
                    <span>Motor Vehicle Insurance Portal</span>
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
                 class="absolute inset-0 object-cover w-full h-full opacity-40 mix-blend-overlay">
        </div>
    </div>
</body>
</html>
