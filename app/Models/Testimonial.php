<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Testimonial extends Model
{
    protected $fillable = [
        'member_id',
        'order_item_id',
        'rating',
        'content',
        'photo_path',
        'photo_disk',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo_path) {
            return null;
        }

        if (Str::startsWith($this->photo_path, ['https://', 'http://'])) {
            return $this->photo_path;
        }

        $disk = $this->photo_disk ?: 'public';

        return $disk === 'public'
            ? '/storage/'.ltrim($this->photo_path, '/')
            : Storage::disk($disk)->url($this->photo_path);
    }
}
