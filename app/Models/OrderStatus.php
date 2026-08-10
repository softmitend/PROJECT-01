<?php

namespace App\Models;

use Database\Factories\OrderStatusFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderStatus extends Model
{
    /** @use HasFactory<OrderStatusFactory> */
    use HasFactory;

    public const TYPES = ['process', 'success', 'failed', 'cancelled'];

    public const SCOPES = ['batch', 'member_order', 'order_item', 'payment', 'all'];

    protected $fillable = [
        'name',
        'code',
        'description',
        'color',
        'sequence',
        'status_type',
        'scope',
        'is_initial',
        'is_final',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sequence' => 'integer',
            'is_initial' => 'boolean',
            'is_final' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class, 'current_status_id');
    }

    public function memberOrders(): HasMany
    {
        return $this->hasMany(MemberOrder::class, 'override_status_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'override_status_id');
    }

    public function paymentMemberOrders(): HasMany
    {
        return $this->hasMany(MemberOrder::class, 'payment_status_id');
    }

    public function oldHistories(): HasMany
    {
        return $this->hasMany(StatusHistory::class, 'old_status_id');
    }

    public function newHistories(): HasMany
    {
        return $this->hasMany(StatusHistory::class, 'new_status_id');
    }

    public function scopeActiveFor($query, string $targetScope)
    {
        return $query->where('is_active', true)
            ->whereIn('scope', [$targetScope, 'all'])
            ->orderBy('sequence')
            ->orderBy('name');
    }

    public function isUsableFor(string $targetScope): bool
    {
        return $this->is_active && in_array($this->scope, [$targetScope, 'all'], true);
    }

    public function usedCount(): int
    {
        return $this->batches()->count()
            + $this->memberOrders()->count()
            + $this->orderItems()->count()
            + $this->paymentMemberOrders()->count()
            + $this->oldHistories()->count()
            + $this->newHistories()->count();
    }
}
