<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'logo', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
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

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
