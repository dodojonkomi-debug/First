<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    protected $fillable = [
        'district_id', 'name', 'code', 'address', 'phone',
        'capacity_class_0', 'capacity_class_1', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity_class_0' => 'integer',
        'capacity_class_1' => 'integer',
    ];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function region()
    {
        return $this->district->region();
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // Миқдори аризаҳои тасдиқшуда барои синф
    public function approvedCountForGrade(string $grade): int
    {
        return $this->applications()
            ->where('grade', $grade)
            ->where('status', 'approved')
            ->count();
    }

    // Ҷойи холӣ дорад?
    public function hasCapacity(string $grade): bool
    {
        $capacity = $grade === '0' ? $this->capacity_class_0 : $this->capacity_class_1;
        return $this->approvedCountForGrade($grade) < $capacity;
    }
}
