<?php

namespace App\Models;

use Database\Factories\BatchFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Batch extends Model
{
    /** @use HasFactory<BatchFactory> */
    use HasFactory;

    protected $fillable = [
        'batch_number',
        'batch_name',
        'current_status_id',
        'description',
        'notes',
        'started_at',
        'completed_at',
        'is_archived',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'is_archived' => 'boolean',
        ];
    }

    public function currentStatus(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'current_status_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(MemberOrder::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function statusHistories(): MorphMany
    {
        return $this->morphMany(StatusHistory::class, 'trackable')->latest();
    }

    public function getEffectiveStatusAttribute(): ?OrderStatus
    {
        return $this->currentStatus;
    }

    public function getOrdersLockedAttribute(): bool
    {
        $status = $this->relationLoaded('currentStatus')
            ? $this->currentStatus
            : $this->currentStatus()->first();

        return (bool) $status?->locks_order_editing;
    }

    public function getProgressLockedAttribute(): bool
    {
        $status = $this->relationLoaded('currentStatus')
            ? $this->currentStatus
            : $this->currentStatus()->first();

        return (bool) $status?->is_final;
    }
}
