<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role',
        'region_id', 'district_id', 'school_id', 'is_active'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Нақшаҳо
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isAdminRegion(): bool
    {
        return $this->role === 'admin_region';
    }

    public function isAdminDistrict(): bool
    {
        return $this->role === 'admin_district';
    }

    public function isAdminSchool(): bool
    {
        return $this->role === 'admin_school';
    }

    public function isParent(): bool
    {
        return $this->role === 'parent';
    }

    // Муносибатҳо
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    // Оё метавонад ин аризаро бубинад?
    public function canViewApplication(Application $application): bool
    {
        return match ($this->role) {
            'superadmin' => true,
            'admin_region' => $application->school->district->region_id === $this->region_id,
            'admin_district' => $application->school->district_id === $this->district_id,
            'admin_school' => $application->school_id === $this->school_id,
            'parent' => $application->user_id === $this->id,
            default => false,
        };
    }
}
