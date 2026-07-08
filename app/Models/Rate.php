<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    protected $fillable = ['company_id', 'category', 'item_name', 'value', 'value_type', 'min_value', 'max_value', 'percentage', 'min_premium', 'notes', 'vehicle_type', 'is_pvt_inclusive'];

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

    public function getDisplayValueAttribute(): string
    {
        if ($this->notes && !in_array($this->value_type, ['percentage', 'percentage_min'])) {
            return $this->notes;
        }

        $fmtPct = fn($v) => rtrim(rtrim(number_format((float)$v, 4), '0'), '.');

        return match ($this->value_type) {
            'percentage' => $fmtPct($this->percentage) . '%',
            'percentage_min' => $fmtPct($this->percentage) . '% min KES ' . number_format((float)($this->min_value ?? 0)),
            'fixed' => $this->value ? 'KES ' . number_format((float)$this->value) : "\u2014",
            'text' => $this->notes ?? "\u2014",
            default => "\u2014",
        };
    }
}
