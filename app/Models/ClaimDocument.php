<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimDocument extends Model
{
    protected $fillable = [
        'upload_token',
        'claim_id',
        'document_type',
        'filename',
        'path',
        'mime_type',
        'file_size',
        'scan_status',
        'scan_result',
        'ai_validation_status',
        'ai_validation_notes',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    public const SCAN_STATUS_PENDING = 'pending';
    public const SCAN_STATUS_CLEAN = 'clean';
    public const SCAN_STATUS_INFECTED = 'infected';
    public const SCAN_STATUS_ERROR = 'error';

    public const AI_STATUS_PENDING = 'pending';
    public const AI_STATUS_VALID = 'valid';
    public const AI_STATUS_INVALID = 'invalid';
    public const AI_STATUS_NEEDS_REVIEW = 'needs_review';

    /**
     * The original, fixed document checklist (still used by
     * AIDocumentValidationService). The claim form's own attachments —
     * 'police_abstract' and 'incident_photo' — are a separate, narrower
     * set validated directly in ClaimsController rather than through this
     * list, since incident photos aren't a checklist item with a fixed name.
     */
    public static function documentTypes(): array
    {
        return [
            'claim_form' => 'Claim Form',
            'police_abstract' => 'Police Abstract',
            'drivers_licence' => 'Drivers Licence',
            'log_book' => 'Copy of Log Book',
            'drivers_statement' => 'Drivers Statement',
            'other' => 'Other Documentation',
        ];
    }

    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
