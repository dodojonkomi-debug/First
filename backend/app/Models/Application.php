<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    protected $fillable = [
        'application_code', 'user_id', 'school_id',
        'child_first_name', 'child_last_name', 'child_middle_name',
        'child_birth_date', 'child_gender', 'grade',
        'parent_first_name', 'parent_last_name', 'parent_id_number',
        'parent_phone', 'parent_email',
        'residence_region_id', 'residence_district_id', 'residence_address',
        'status', 'rejection_reason', 'reviewed_at', 'reviewed_by',
        'has_birth_certificate', 'has_parent_id', 'has_medical_form',
        'has_vaccination_card', 'has_residence_certificate',
    ];

    protected $casts = [
        'child_birth_date' => 'date',
        'reviewed_at' => 'datetime',
        'has_birth_certificate' => 'boolean',
        'has_parent_id' => 'boolean',
        'has_medical_form' => 'boolean',
        'has_vaccination_card' => 'boolean',
        'has_residence_certificate' => 'boolean',
    ];

    // Генерацияи коди ариза: {сол}-{моҳ}-{рӯз}-{соат}-MTMU__-{XXXX}
    public static function generateCode(): string
    {
        $now = now();
        $random = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        return sprintf(
            '%s-%s-%s-%s-MTMU__-%s',
            $now->format('Y'),
            $now->format('m'),
            $now->format('d'),
            $now->format('H'),
            $random
        );
    }

    // Муносибатҳо
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function residenceRegion(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'residence_region_id');
    }

    public function residenceDistrict(): BelongsTo
    {
        return $this->belongsTo(District::class, 'residence_district_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    // Ҳуҷҷатҳо пурра ҳастанд?
    public function isDocumentsComplete(): bool
    {
        return $this->has_birth_certificate
            && $this->has_parent_id
            && $this->has_medical_form
            && $this->has_vaccination_card
            && $this->has_residence_certificate;
    }
}
