<?php

namespace App\Models;

use Database\Factories\MemberFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Member extends Model
{
    /** @use HasFactory<MemberFactory> */
    use HasFactory;

    protected $fillable = [
        'member_code',
        'display_name',
        'username',
        'line_user_id',
        'avatar_url',
        'email',
        'phone',
        'address',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(MemberOrder::class);
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    /**
     * Scope untuk member yang eligible untuk order baru:
     * harus memiliki line_user_id (LINE-connected) DAN is_active = true
     */
    public function scopeEligibleForNewOrder(Builder $query): Builder
    {
        return $query->whereNotNull('line_user_id')
            ->where('is_active', true);
    }

    /**
     * Scope untuk member legacy (belum LINE-connected)
     */
    public function scopeLegacy(Builder $query): Builder
    {
        return $query->whereNull('line_user_id');
    }

    /**
     * Cek apakah member eligible untuk order baru
     */
    public function isEligibleForNewOrder(): bool
    {
        return $this->line_user_id !== null && $this->is_active;
    }

    /**
     * Label status eligibility untuk tampilan admin
     */
    public function getEligibilityStatusLabelAttribute(): string
    {
        if (! $this->is_active) {
            return 'Akun nonaktif — Tidak dapat digunakan untuk order baru';
        }

        if ($this->line_user_id === null) {
            return 'LINE belum terhubung — Tidak dapat digunakan untuk order baru';
        }

        return 'LINE Connected — Eligible';
    }

    public function setUsernameAttribute(?string $value): void
    {
        $this->attributes['username'] = $value ? mb_strtolower(trim($value)) : null;
    }

    public function setEmailAttribute(?string $value): void
    {
        $this->attributes['email'] = $value ? mb_strtolower(trim($value)) : null;
    }
}
