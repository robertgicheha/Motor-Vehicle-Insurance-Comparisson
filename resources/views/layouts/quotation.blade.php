<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
@yield('title')
<title>Motor Insurance Quotation | MIC Global Risks</title>
<meta name="description" content="View and download your motor vehicle insurance quotation from MIC Global Risks, comparing Kenya's leading insurers.">
<link rel="stylesheet" href="{{ asset('css/quotation.css') }}">
@vite(['resources/css/app.css'])
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body>

<x-public-header/>

@yield('content')

<x-auth-modal/>
<x-public-footer/>

@yield('scripts')
<script src="{{ asset('js/quotation.js') }}"></script>
</body>
</html>