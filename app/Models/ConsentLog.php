<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsentLog extends Model
{
    protected $fillable = [
        'sticker_id',
        'consent_given',
        'consent_version',
        'consent_text',
        'clause_reference',
        'ip_address',
        'user_agent',
        'consented_at',
    ];

    protected function casts(): array
    {
        return [
            'consent_given' => 'boolean',
            'consented_at' => 'datetime',
        ];
    }

    public function sticker()
    {
        return $this->belongsTo(Sticker::class);
    }
}
