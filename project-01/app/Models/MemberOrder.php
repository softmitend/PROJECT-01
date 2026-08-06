<?php

namespace App\Models;

use Database\Factories\MemberOrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class MemberOrder extends Model
{
    /** @use HasFactory<MemberOrderFactory> */
    use HasFactory;

    protected $fillable = [
        'order_code',
        'member_id',
        'batch_id',
        'override_status_id',
        'total_amount',
        'payment_status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function overrideStatus(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'override_status_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories(): MorphMany
    {
        return $this->morphMany(StatusHistory::class, 'trackable')->latest();
    }

    public function getEffectiveStatusAttribute(): ?OrderStatus
    {
        return $this->overrideStatus ?: $this->batch?->effective_status;
    }
}
