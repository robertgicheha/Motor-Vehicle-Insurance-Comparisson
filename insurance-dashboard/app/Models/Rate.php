<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    protected $fillable = ['company_id', 'category', 'item_name', 'value', 'value_type', 'min_value', 'max_value', 'percentage', 'min_premium'];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_value' => 'decimal:2',
            'max_value' => 'decimal:2',
            'percentage' => 'decimal:4',
            'min_premium' => 'decimal:2',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
