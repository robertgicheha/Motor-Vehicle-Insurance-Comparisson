<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'salutation', 'first_name', 'middle_name', 'last_name', 'phone', 'email',
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
     * Customer display name — prefers the quotation's own snapshot (covers
     * guest quotations with no user_id, and "on behalf of" typed-in names
     * that may differ from the linked user's current profile), falling
     * back to the linked user for older rows saved before the snapshot
     * columns existed.
     */
    public function getCustomerNameAttribute(): string
    {
        $snapshot = collect([$this->salutation, $this->first_name, $this->middle_name, $this->last_name])
            ->filter()
            ->implode(' ');

        return $snapshot ?: ($this->user?->name ?? 'Valued Customer');
    }

    public function getCustomerSalutationAttribute(): ?string
    {
        return $this->salutation;
    }

    public function getCustomerFirstNameAttribute(): ?string
    {
        return $this->first_name ?: $this->user?->first_name;
    }

    public function getCustomerLastNameAttribute(): ?string
    {
        return $this->last_name ?: $this->user?->last_name;
    }

    public function getCustomerEmailAttribute(): ?string
    {
        return $this->email ?: $this->user?->email;
    }

    public function getCustomerPhoneAttribute(): ?string
    {
        return $this->phone ?: $this->user?->mobile;
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
