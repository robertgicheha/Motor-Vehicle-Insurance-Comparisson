<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sticker extends Model
{
    protected $fillable = [
        'company_id', 'quotation_id', 'issued_by', 'sticker_number', 'underwriter_sticker_no', 'customer_name',
        'customer_email', 'customer_phone', 'vehicle_registration',
        'car_make', 'car_model', 'car_year', 'chassis_number', 'engine_number', 'color', 'car_valuation',
        'premium', 'premium_rate', 'cover_type', 'cover_start_date', 'cover_end_date',
        'cover_duration_unit', 'cover_duration_value',
        'client_type', 'logbook_na', 'status',
        'created_via', 'breakdown', 'generated_at', 'completed_by',
        'download_count', 'last_downloaded_at',
        'kyc_documents', 'underwriter_email', 'admin_notes',
        'email_subject', 'email_body', 'email_from_address', 'email_from_name',
        'reply_to_address', 'last_email_status', 'last_email_error', 'email_sent_at',
        'response_received_at', 'response_status', 'response_notes', 'response_document_path',
    ];

    protected function casts(): array
    {
        return [
            'car_valuation' => 'decimal:2',
            'premium' => 'decimal:2',
            'premium_rate' => 'decimal:4',
            'breakdown' => 'array',
            'kyc_documents' => 'array',
            'cover_start_date' => 'date',
            'cover_end_date' => 'date',
            'logbook_na' => 'boolean',
            'generated_at' => 'datetime',
            'last_downloaded_at' => 'datetime',
            'email_sent_at' => 'datetime',
            'response_received_at' => 'datetime',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function certificateDocuments()
    {
        return $this->hasMany(CertificateDocument::class);
    }

    public function emailLogs()
    {
        return $this->hasMany(StickerEmailLog::class);
    }

    public function hasEmailBeenSent(): bool
    {
        return !is_null($this->email_sent_at);
    }

    public function getTurnaroundMinutes(): ?int
    {
        if (!$this->email_sent_at) {
            return null;
        }

        $end = $this->response_received_at ?? now();
        return (int) $this->email_sent_at->diffInMinutes($end);
    }

    public function isTurnaroundBreached(): bool
    {
        $minutes = $this->getTurnaroundMinutes();
        $limit = (int) (config('services.sticker.turnaround_minutes', 30));
        return $minutes !== null && $minutes > $limit;
    }

    public function getTurnaroundStatus(): string
    {
        if ($this->response_status === 'completed' || $this->status === 'completed') {
            return 'completed';
        }

        if (!$this->email_sent_at) {
            return 'pending';
        }

        if ($this->isTurnaroundBreached()) {
            return 'breached';
        }

        return 'pending_response';
    }

    /**
     * The Sticker ID — our own internal identifier (e.g. STK-001), distinct
     * from the underwriter_sticker_no, which is the real number a company
     * assigns and is only known once their reply comes back.
     */
    public static function generateNumber(): string
    {
        $seq = static::count() + 1;
        return 'STK-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'completed');
    }
}
