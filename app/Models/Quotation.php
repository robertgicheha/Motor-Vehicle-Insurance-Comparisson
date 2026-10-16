<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_id', 'quotation_number', 'car_make', 'car_model',
        'car_year', 'car_registration', 'chassis_number', 'engine_number', 'body_type', 'color',
        'car_valuation', 'vehicle_type', 'num_passengers', 'premium', 'pvt_premium',
        'total_premium', 'excess_own_damage', 'excess_theft', 'status', 'breakdown'
    ];

    protected function casts(): array
    {
        return [
            'car_valuation' => 'decimal:2',
            'premium' => 'decimal:2',
            'pvt_premium' => 'decimal:2',
            'total_premium' => 'decimal:2',
            'excess_own_damage' => 'decimal:2',
            'excess_theft' => 'decimal:2',
            'breakdown' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function stickers()
    {
        return $this->hasMany(Sticker::class);
    }

    /**
     * A short, non-personal description of the insured vehicle. Owner
     * personal data is never stored, so the vehicle is the record's
     * human-readable identity everywhere a "customer" used to be shown.
     */
    public function getVehicleDescriptionAttribute(): string
    {
        $description = trim(collect([$this->car_make, $this->car_model])->filter()->implode(' '));
        $registration = $this->car_registration;

        if ($description !== '' && $registration) {
            return $description . ' (' . $registration . ')';
        }

        return $description !== '' ? $description : ($registration ?: 'Vehicle');
    }

    public function getCustomerNameAttribute(): string
    {
        return $this->vehicle_description;
    }

    public function getCustomerSalutationAttribute(): ?string
    {
        return null;
    }

    public function getCustomerFirstNameAttribute(): ?string
    {
        return null;
    }

    public function getCustomerLastNameAttribute(): ?string
    {
        return null;
    }

    public function getCustomerEmailAttribute(): ?string
    {
        return null;
    }

    public function getCustomerPhoneAttribute(): ?string
    {
        return null;
    }

    public static function generateNumber(?Company $company = null): string
    {
        $prefix = $company ? $company->getInitials() : 'IN';
        $year = now()->format('Y');
        $month = now()->format('m');
        $day = now()->format('d');
        $seq = static::where('company_id', $company?->id)
            ->whereDate('created_at', today())
            ->count() + 1;
        $unique = chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . str_pad($seq, 3, '0', STR_PAD_LEFT);
        return $prefix . '-' . $year . '/' . $day . '/' . $month . '-' . $unique;
    }
}
