@php
    $exception = $exception ?? null;
    $trace = null;
    if ($exception && $exception->getMessage()) {
        $trace = [
            'error' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'exception' => $exception->getTraceAsString(),
        ];
    }
@endphp

@include('errors.layout', [
    'code' => 503,
    'title' => 'Service unavailable',
    'message' => 'We are currently performing scheduled maintenance to improve the portal. Please check back in a few minutes — your data is safe.',
    'icon' => '<svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>',
    'trace' => $trace,
])
