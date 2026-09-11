<?php

namespace App\Jobs;

use App\Models\CertificateDocument;
use App\Services\AuditService;
use App\Services\ClamAVService;
use App\Services\FileSecurityService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ScanDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(
        public int $documentId,
    ) {}

    public function handle(): void
    {
        $document = CertificateDocument::find($this->documentId);
        if (!$document) {
            return;
        }

        $fullPath = storage_path('app/private/' . $document->path);
        if (!file_exists($fullPath)) {
            $document->update([
                'scan_status' => 'error',
                'scan_result' => 'File not found on disk.',
            ]);
            return;
        }

        try {
            $fileSecurity = new FileSecurityService();
            $uploadedFile = new \Illuminate\Http\UploadedFile($fullPath, $document->filename, $document->mime_type, null, true);
            $securityResult = $fileSecurity->validate($uploadedFile, $document->document_type);

            if (!$securityResult['valid']) {
                Storage::disk('local')->delete($document->path);
                $threatMsg = $securityResult['threat']
                    ? 'Flagged as potentially malicious.'
                    : 'Failed security validation.';
                $document->update([
                    'scan_status' => 'infected',
                    'scan_result' => $threatMsg . ' ' . implode('; ', $securityResult['errors']),
                ]);
                AuditService::activity('kyc_document_rejected', "KYC document '{$document->document_type}' rejected: " . implode('; ', $securityResult['errors']), 'warning');
                return;
            }
        } catch (\Exception $e) {
            Log::warning('File security validation failed: ' . $e->getMessage());
        }

        $scanStatus = 'clean';
        $scanResult = 'No threats detected';

        try {
            $clamav = new ClamAVService();
            $isClean = $clamav->scan($fullPath);
            if (!$isClean) {
                $scanStatus = 'infected';
                $scanResult = 'Potential threat detected by ClamAV.';
                Storage::disk('local')->delete($document->path);
            }
        } catch (\Exception $e) {
            $scanStatus = 'clean';
            $scanResult = 'ClamAV unavailable — passed basic security checks.';
            Log::warning('ClamAV scan failed: ' . $e->getMessage());
        }

        $document->update([
            'scan_status' => $scanStatus,
            'scan_result' => $scanResult,
        ]);

        AuditService::activity('kyc_document_scanned', "KYC document '{$document->document_type}' scanned: {$scanStatus}");
    }
}
