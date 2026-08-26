@php
    $exception = $exception ?? null;
    $trace = $exception && $exception->getMessage()
        ? [
            'error' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]
        : null;
@endphp

@include('errors.layout', [
    'code' => 400,
    'title' => 'Bad request',
    'message' => 'The server could not understand your request. This usually means the request is malformed or contains invalid data. Please go back and try again.',
    'icon' => '<svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636M12 8v4m0 4h.01"/></svg>',
    'trace' => $trace,
])
