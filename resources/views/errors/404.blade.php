@php
    $exception = $exception ?? null;
    $requestedUrl = request()->path();
    $trace = [
        'error' => 'Route / ' . $requestedUrl . ' could not be found',
        'file' => 'routes/web.php',
        'line' => 'Check that the route is registered and that no wildcard route ({id}) shadows it.',
    ];
@endphp

@include('errors.layout', [
    'code' => 404,
    'title' => 'Page not found',
    'message' => 'The page you are looking for does not exist, has been moved, or you do not have permission to view it. Double-check the address or head back to the dashboard.',
    'icon' => '<svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35M8.5 8.5l5 5M13.5 8.5l-5 5"/></svg>',
    'trace' => $trace,
])
