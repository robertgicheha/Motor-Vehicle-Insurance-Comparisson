<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StickerEmailLog extends Model
{
    protected $fillable = [
        'sticker_id',
        'type',
        'recipient',
        'sender',
        'subject',
        'body',
        'status',
        'error_message',
        'attachments',
        'metadata',
        'triggered_by',
        'sent_at',
        'delivered_at',
        'opened_at',
    ];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'metadata' => 'array',
            'sent_at' => 'datetime',
            'delivered_at' => 'datetime',
            'opened_at' => 'datetime',
        ];
    }

    public function sticker(): BelongsTo
    {
        return $this->belongsTo(Sticker::class);
    }

    public function triggeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }

    public function isOutbound(): bool
    {
        return $this->type === 'outbound_to_underwriter';
    }

    public function isInbound(): bool
    {
        return $this->type === 'inbound_from_underwriter';
    }

    public function isSent(): bool
    {
        return in_array($this->status, ['sent', 'delivered', 'opened', 'clicked']);
    }

    public function isFailed(): bool
    {
        return in_array($this->status, ['failed', 'bounced']);
    }
}
