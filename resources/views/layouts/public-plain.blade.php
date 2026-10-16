<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LOLC Microfinance Bank')</title>
    <meta name="description" content="@yield('description', 'LOLC Microfinance Bank — motor insurance quotations, comparisons, claims, and sticker issuance.')">
    <link rel="icon" type="image/x-icon" href="{{ asset('Favicon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('Favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
      .page-hero{position:relative;min-height:280px;display:flex;align-items:center;overflow:hidden;background:linear-gradient(105deg,rgba(0,30,80,.85),rgba(0,60,130,.6)),url('{{ asset('images/hero/vehicle.jpg') }}') center/cover}
      .page-hero-content{position:relative;z-index:2;padding:60px 48px;max-width:700px}
      .page-hero-eyebrow{color:#93c5fd;font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:.12em;margin-bottom:8px}
      .page-hero h1{color:#fff;font-size:32px;font-weight:700;line-height:1.2;margin-bottom:12px}
      .page-hero p{color:rgba(255,255,255,.8);font-size:16px;line-height:1.6;margin-bottom:24px}
      .page-hero .btn-hero{display:inline-flex;align-items:center;gap:8px;background:#ED1D25;color:#fff;padding:12px 28px;border-radius:30px;font-weight:600;font-size:14px;text-decoration:none;transition:background .2s}
      .page-hero .btn-hero:hover{background:#B6161C}
      @media(max-width:768px){.page-hero-content{padding:40px 24px}}
    </style>
    @stack('styles')
</head>
<body class="font-montserrat antialiased bg-gray-50 text-[#1a1a2e] min-h-screen flex flex-col">

    @include('partials.public-nav')

    @include('partials.page-hero')

    @yield('content')

    @include('partials.public-footer')

    @stack('scripts')
</body>
</html>
