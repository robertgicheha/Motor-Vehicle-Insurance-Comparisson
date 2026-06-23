<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Authentication' }} | Insurance Quotation Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    </head>
<body class="font-sans antialiased text-gray-900 bg-white">
    <div class="flex min-h-screen">
        
        <div class="flex flex-col justify-center w-full lg:w-[40%] px-8 sm:px-16 xl:px-24">
            <div class="mb-10 text-center">
                <a href="/" class="flex items-center justify-center gap-2 text-2xl font-bold text-indigo-600">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                    <span>MotorCover</span>
                </a>
            </div>

            <div class="w-full max-w-md mx-auto">
                {{ $slot }}
            </div>

            <div class="mt-12 text-sm text-center text-gray-500">
                &copy; {{ date('Y') }} MotorCover Portal — GA, Britam & Sanlam Allianz.
            </div>
        </div>

        <div class="hidden lg:block lg:w-[60%] relative bg-slate-800">
            <img src="https://images.unsplash.com/photo-1573164713988-8665fc963095?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" 
                 alt="Team working" 
                 class="absolute inset-0 object-cover w-full h-full opacity-40 mix-blend-overlay">
        </div>
        
    </div>
</body>
</html>