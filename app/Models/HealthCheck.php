<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthCheck extends Model
{
    protected $fillable = ['type', 'status', 'message', 'details', 'checked_at'];

    protected function casts(): array
    {
        return [
            'details' => 'array',
            'checked_at' => 'datetime',
        ];
    }

    public function scopeLatestByType($query)
    {
        return $query->whereIn('id', function ($q) {
            $q->selectRaw('MAX(id)')
                ->from('health_checks')
                ->groupBy('type');
        });
    }
}
