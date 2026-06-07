<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = [
        'application_id', 'type', 'original_name',
        'file_path', 'mime_type', 'file_size'
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    // Номҳои навъи ҳуҷҷат ба тоҷикӣ
    public static function typeLabels(): array
    {
        return [
            'birth_certificate' => 'Шаҳодатномаи таваллуди кӯдак',
            'parent_id' => 'Шиноснома/ШҲ-и волидайн',
            'medical_form' => 'Маълумотномаи тиббӣ (форма 026)',
            'vaccination_card' => 'Корти эмгузаронӣ',
            'residence_certificate' => 'Маълумотнома аз ҷойи зист',
        ];
    }
}
