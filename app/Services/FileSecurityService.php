<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class FileSecurityService
{
    private const ALLOWED_MIME_MAP = [
        'pdf'  => ['application/pdf'],
        'jpg'  => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png'  => ['image/png'],
    ];

    private const MAGIC_BYTES = [
        'pdf'  => ['255044462D'],
        'jpg'  => ['FFD8FF'],
        'jpeg' => ['FFD8FF'],
        'png'  => ['89504E47'],
    ];

    public function validate(UploadedFile $file, string $documentType): array
    {
        $errors = [];

        $extension = strtolower($file->getClientOriginalExtension());
        $clientMime = $file->getClientMimeType();

        if (!isset(self::ALLOWED_MIME_MAP[$extension])) {
            $errors[] = "File extension '{$extension}' is not allowed. Accepted: PDF, JPG, PNG.";
        }

        $realMimeType = $this->getRealMimeType($file->getRealPath());
        $allowedMimes = self::ALLOWED_MIME_MAP[$extension] ?? [];
        $isContentValid = in_array($realMimeType, $allowedMimes);

        if ($realMimeType === 'application/x-msdownload' || $realMimeType === 'application/x-executable') {
            $errors[] = 'Executable files are not allowed. This file has been flagged as potentially malicious.';
            return ['valid' => false, 'errors' => $errors, 'threat' => true];
        }

        if ($realMimeType === 'application/x-dosexec' || str_contains($realMimeType, 'script')) {
            $errors[] = 'Script files are not allowed. This file has been flagged as potentially malicious.';
            return ['valid' => false, 'errors' => $errors, 'threat' => true];
        }

        if ($extension !== 'pdf' && !$isContentValid) {
            $errors[] = "File content does not match the '{$extension}' format. The file may be disguised.";
            return ['valid' => false, 'errors' => $errors, 'threat' => true];
        }

        if ($extension === 'pdf') {
            $content = file_get_contents($file->getRealPath());
            if ($content !== false && !$this->isValidPdfContent($content)) {
                $errors[] = 'File does not appear to be a valid PDF document. It may be disguised as a PDF.';
                return ['valid' => false, 'errors' => $errors, 'threat' => true];
            }
        }

        if ($realMimeType !== 'application/pdf' && !in_array($realMimeType, ['image/jpeg', 'image/png'])) {
            if (empty($errors)) {
                $errors[] = "File content type '{$realMimeType}' does not match any allowed document type.";
                return ['valid' => false, 'errors' => $errors, 'threat' => true];
            }
        }

        $magicMatch = $this->validateMagicBytes($file->getRealPath(), $extension);
        if (!$magicMatch && $extension !== 'pdf') {
            $errors[] = "File signature does not match expected '{$extension}' format.";
            return ['valid' => false, 'errors' => $errors, 'threat' => true];
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'threat' => false,
            'real_mime' => $realMimeType,
            'client_mime' => $clientMime,
        ];
    }

    private function getRealMimeType(string $filePath): string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($filePath);

        if ($mimeType === false || $mimeType === 'application/octet-stream') {
            $content = @file_get_contents($filePath);
            if ($content !== false) {
                return $this->detectMimeFromContent($content);
            }
        }

        return $mimeType;
    }

    private function detectMimeFromContent(string $content): string
    {
        $bytes = bin2hex(substr($content, 0, 16));

        if (str_starts_with($bytes, '89504e470d0a1a0a')) {
            return 'image/png';
        }
        if (str_starts_with($bytes, 'ffd8ff')) {
            return 'image/jpeg';
        }
        if (str_starts_with($bytes, '25504446')) {
            return 'application/pdf';
        }

        return 'application/octet-stream';
    }

    private function validateMagicBytes(string $filePath, string $extension): bool
    {
        $expectedSignatures = self::MAGIC_BYTES[$extension] ?? [];
        if (empty($expectedSignatures)) {
            return true;
        }

        $handle = fopen($filePath, 'rb');
        if (!$handle) {
            return false;
        }

        $header = fread($handle, 16);
        fclose($handle);

        if ($header === false) {
            return false;
        }

        $hex = strtoupper(bin2hex($header));

        foreach ($expectedSignatures as $signature) {
            if (str_starts_with($hex, $signature)) {
                return true;
            }
        }

        return false;
    }

    private function isValidPdfContent(string $content): bool
    {
        $hex = strtoupper(bin2hex(substr($content, 0, 16)));
        return str_starts_with($hex, '255044462D');
    }
}
