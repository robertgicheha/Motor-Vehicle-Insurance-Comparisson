<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'logo', 'is_active', 'company_type', 'min_sum_insured_psv', 'min_sum_insured_private', 'underwriter_email', 'claims_email', 'sticker_notification_emails'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'min_sum_insured_psv' => 'decimal:2',
            'min_sum_insured_private' => 'decimal:2',
        ];
    }

    public function rates()
    {
        return $this->hasMany(Rate::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function stickers()
    {
        return $this->hasMany(Sticker::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getInitials(): string
    {
        if ($this->slug) {
            return strtoupper(substr($this->slug, 0, 2));
        }
        $name = strtoupper(preg_replace('/\b(INSURANCE|COMPANY|LTD|LIMITED|PLC|CORP|INC|GROUP|&|\-)\s*/', '', $this->name));
        $parts = array_values(array_filter(explode(' ', $name)));
        if (count($parts) <= 1) {
            return substr($parts[0] ?? 'IN', 0, 2);
        }
        $initials = '';
        foreach ($parts as $part) {
            if (strlen($part) >= 2) {
                $initials .= $part[0];
            }
        }
        return substr($initials ?: 'IN', 0, 2);
    }

    public static function hardcodedLogoMap(): array
    {
        return [
            'GA' => 'images/GA Insurance.png',
            'SANLAM' => 'images/Sanlam-Allianz.png',
            'DEFINITE' => 'images/definite.png',
            'BRITAM' => 'images/britam.png',
        ];
    }

    public function logoUrl(): string
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        $map = static::hardcodedLogoMap();
        if (isset($map[$this->slug])) {
            return asset($map[$this->slug]);
        }
        return '';
    }
}
