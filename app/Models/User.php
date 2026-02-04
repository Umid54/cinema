<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Mass assignable
     */
    protected $fillable = [
        'name',
        'email',
        'password',

        // Premium / Trial
        'is_premium',
        'premium_until',
        'trial_used',
        'trial_started_at',

        // Security
        'is_banned',
        'register_ip',
        'last_ip',
    ];

    /**
     * Hidden
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts (Laravel 12)
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',

            'is_premium'        => 'boolean',
            'trial_used'        => 'boolean',
            'is_banned'         => 'boolean',

            'premium_until'     => 'datetime',
            'trial_started_at'  => 'datetime',
        ];
    }

    /**
     * Virtual attributes
     */
    protected $appends = [
        'account_status',
        'is_premium_active',
        'is_trial',
    ];

    /* =====================================================
       ROLES
    ===================================================== */

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()
            ->where('name', $role)
            ->exists();
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /* =====================================================
       FAVORITES
    ===================================================== */

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function hasFavorited(Model $model): bool
    {
        return $this->favorites()
            ->where('favoritable_id', $model->id)
            ->where('favoritable_type', get_class($model))
            ->exists();
    }

    /* =====================================================
       PREMIUM / TRIAL LOGIC
    ===================================================== */

    /**
     * Активен ли Premium (платный или lifetime)
     */
    public function getIsPremiumActiveAttribute(): bool
    {
        if (!$this->is_premium) {
            return false;
        }

        // lifetime premium
        if ($this->premium_until === null) {
            return true;
        }

        return now()->lessThan($this->premium_until);
    }

    /**
     * Активен ли Trial
     * ✔ строго 24 часа
     * ✔ НЕ считается trial, если есть Premium дальше trial
     */
    public function getIsTrialAttribute(): bool
    {
        if (!$this->trial_used || !$this->trial_started_at) {
            return false;
        }

        $trialEndsAt = $this->trial_started_at
            ->copy()
            ->addHours(24);

        // Если есть premium и он дальше trial — это уже PREMIUM
        if (
            $this->premium_until &&
            $this->premium_until->greaterThan($trialEndsAt)
        ) {
            return false;
        }

        return now()->lessThan($trialEndsAt);
    }

    /**
     * Единый статус
     * PREMIUM | TRIAL | FREE
     */
    public function premiumStatus(): string
    {
        if ($this->is_trial) {
            return 'TRIAL';
        }

        if ($this->is_premium_active) {
            return 'PREMIUM';
        }

        return 'FREE';
    }

    /**
     * Авто-нормализация Premium
     */
    public function normalizePremium(): void
    {
        if (
            $this->is_premium &&
            $this->premium_until &&
            now()->greaterThan($this->premium_until)
        ) {
            $this->forceFill([
                'is_premium'       => false,
                'premium_until'    => null,
                'trial_started_at' => null,
            ])->save();
        }
    }

    /* =====================================================
       QUALITY LIMITS (UI)
    ===================================================== */

    /**
     * Разрешённые качества видео (ТОЛЬКО UI)
     */
    public function allowedQualities(): array
    {
        if ($this->is_premium_active) {
            return [360, 480, 720, 1080];
        }

        if ($this->is_trial) {
            return [360, 480, 720];
        }

        return [360];
    }

    /* =====================================================
       ACCOUNT STATUS (UI)
    ===================================================== */

    /**
     * BANNED / PREMIUM / TRIAL / FREE
     */
    public function getAccountStatusAttribute(): string
    {
        if ($this->is_banned) {
            return 'BANNED';
        }

        return $this->premiumStatus();
    }
}
