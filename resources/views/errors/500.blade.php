@php
    $exception = $exception ?? null;
    $trace = $exception
        ? [
            'error' => $exception->getMessage() ?: get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'exception' => $exception->getTraceAsString(),
        ]
        : [
            'error' => 'An unexpected error occurred',
            'file' => 'Unknown',
            'line' => 0,
        ];
@endphp

@include('errors.layout', [
    'code' => 500,
    'title' => 'Server error',
    'message' => 'Something went wrong on our end while processing your request. The error has been logged — use the details below to pinpoint the failing file and line, or contact support.',
    'icon' => '<svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>',
    'trace' => $trace,
])
