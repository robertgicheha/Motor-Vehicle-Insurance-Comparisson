<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteSession extends Model
{
    protected $fillable = ['user_id', 'status', 'data', 'started_at', 'completed_at', 'expires_at'];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeAbandoned($query)
    {
        return $query->where('status', 'started')
            ->where('started_at', '<', now()->subMinutes(30));
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'started')
            ->where('started_at', '>=', now()->subMinutes(30));
    }
}
