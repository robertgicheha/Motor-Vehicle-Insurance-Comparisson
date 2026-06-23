<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sticker extends Model
{
    protected $fillable = [
        'company_id', 'quotation_id', 'sticker_number', 'customer_name',
        'customer_email', 'customer_phone', 'vehicle_registration',
        'car_make', 'car_model', 'car_year', 'car_valuation',
        'premium', 'premium_rate', 'cover_type', 'status',
        'created_via', 'breakdown', 'generated_at',
        'download_count', 'last_downloaded_at',
    ];

    protected function casts(): array
    {
        return [
            'car_valuation' => 'decimal:2',
            'premium' => 'decimal:2',
            'premium_rate' => 'decimal:4',
            'breakdown' => 'array',
            'generated_at' => 'datetime',
            'last_downloaded_at' => 'datetime',
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

    public static function generateNumber(): string
    {
        $prefix = 'STK';
        $date = now()->format('Ymd');
        $last = static::whereDate('created_at', today())->count() + 1;
        return $prefix . $date . str_pad($last, 4, '0', STR_PAD_LEFT);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
