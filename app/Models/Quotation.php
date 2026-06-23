<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $fillable = [
        'user_id', 'company_id', 'quotation_number', 'car_make', 'car_model',
        'car_year', 'car_registration', 'car_valuation', 'premium', 'pvt_premium',
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

    public static function generateNumber()
    {
        $prefix = 'Q';
        $date = now()->format('Ymd');
        $last = static::whereDate('created_at', today())->count() + 1;
        return $prefix . $date . str_pad($last, 4, '0', STR_PAD_LEFT);
    }
}
