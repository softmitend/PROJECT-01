<?php

namespace App\Models;

use Database\Factories\BatchFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        'catalog_image_path',
        'catalog_image_disk',
        'qris_image_path',
        'ordering_deadline',
        'is_catalog_visible',
        'started_at',
        'completed_at',
        'is_archived',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'ordering_deadline' => 'datetime',
            'is_catalog_visible' => 'boolean',
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
        return $this->belongsToMany(Product::class)
            ->withPivot(['dp_price', 'full_price', 'sort_order', 'is_available'])
            ->orderByPivot('sort_order');
    }

    public function getCatalogIsOpenAttribute(): bool
    {
        return $this->is_catalog_visible
            && ! $this->is_archived
            && (! $this->ordering_deadline || $this->ordering_deadline->isFuture());
    }

    public function getCatalogImageUrlAttribute(): ?string
    {
        if (! $this->catalog_image_path) {
            return null;
        }

        if (Str::startsWith($this->catalog_image_path, ['https://', 'http://'])) {
            return $this->catalog_image_path;
        }

        $disk = $this->catalog_image_disk ?: 'public';

        return $disk === 'public'
            ? '/storage/'.ltrim($this->catalog_image_path, '/')
            : Storage::disk($disk)->url($this->catalog_image_path);
    }

    public function getQrisImageUrlAttribute(): ?string
    {
        return $this->qris_image_path
            ? '/storage/'.ltrim($this->qris_image_path, '/')
            : null;
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
