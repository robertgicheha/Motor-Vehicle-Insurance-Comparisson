<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Claim extends Model
{
    protected $fillable = [
        'claim_number',
        'underwriter_claim_number',
        'user_id',
        'on_behalf_of_user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'registration_number',
        'vehicle_details',
        'incident_date',
        'incident_description',
        'claim_type',
        'claim_type_other',
        'police_abstract_reference',
        'documents_confirmed',
        'other_documents',
        'settlement_amount',
        'settlement_notes',
        'admin_notes',
        'reviewed_at',
        'settled_at',
        'declined_at',
        'status',
        'vehicle_assessment_done',
        'police_notified',
    ];

    protected function casts(): array
    {
        return [
            'vehicle_details' => 'array',
            'documents_confirmed' => 'array',
            'other_documents' => 'array',
            'incident_date' => 'date',
            'reviewed_at' => 'datetime',
            'settled_at' => 'datetime',
            'declined_at' => 'datetime',
            'settlement_amount' => 'decimal:2',
            'vehicle_assessment_done' => 'boolean',
        ];
    }

    public const STATUS_REPORTED = 'reported';
    

    public static function statuses(): array
    {
        return [
            self::STATUS_REPORTED => 'Claim is Reported',

        ];
    }

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

    public static function claimTypes(): array
    {
        return [
            'own_damage' => 'Own Damage',
            'tpo' => 'TPO - Third Party Only',
            'windscreen' => 'Windscreen',
            'partial_theft' => 'Partial Theft',
            'others' => 'Others',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function onBehalfOfUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'on_behalf_of_user_id');
    }

    public function documents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ClaimDocument::class);
    }

   
    public static function generateNumber(): string
    {
        $prefix = 'CLM';
        $year = now()->format('Y');
        $month = now()->format('m');
        $day = now()->format('d');
        $seq = static::whereDate('created_at', today())->count() + 1;
        $unique = chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . str_pad($seq, 3, '0', STR_PAD_LEFT);
        return $prefix . '-' . $year . '/' . $day . '/' . $month . '-' . $unique;
    }
}
