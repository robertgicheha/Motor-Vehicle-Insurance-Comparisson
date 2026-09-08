<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificateDocument extends Model
{
    protected $fillable = [
        'sticker_id',
        'document_type',
        'document_label',
        'filename',
        'path',
        'mime_type',
        'file_size',
        'scan_status',
        'scan_result',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    public function sticker(): BelongsTo
    {
        return $this->belongsTo(Sticker::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function scanIsClean(): bool
    {
        return $this->scan_status === 'clean';
    }

    public function scanIsInfected(): bool
    {
        return $this->scan_status === 'infected';
    }
}
