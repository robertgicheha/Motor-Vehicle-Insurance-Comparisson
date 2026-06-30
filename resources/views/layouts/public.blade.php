<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Motor Insurance Quotation') | MIC Global Risks</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('Favicon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('Favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-montserrat antialiased bg-gray-50 text-[#1a1a2e] min-h-screen flex flex-col">

    @include('partials.public-nav')

    @include('partials.public-hero')

    <main class="flex-1 max-w-[1100px] mx-auto w-full px-10 py-9 pb-16">
        @yield('content')
    </main>

    @include('partials.public-footer')

    @stack('scripts')
</body>
</html>
