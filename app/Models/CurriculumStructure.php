<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CurriculumStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'name',
        'code',
        'phase',
        'curriculum_type',
        'hours_per_week',
        'competency_goals',
        'syllabus_summary',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'hours_per_week' => 'integer',
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function phaseLabel(): string
    {
        return match ($this->phase) {
            'fase_a' => 'Fase A (Kelas 1 - 2)',
            'fase_b' => 'Fase B (Kelas 3 - 4)',
            'fase_c' => 'Fase C (Kelas 5 - 6)',
            default => 'Semua Fase',
        };
    }

    public function curriculumTypeLabel(): string
    {
        return match ($this->curriculum_type) {
            'kemenag_pai' => 'Khas Kemenag (PAI & Bahasa Arab)',
            'nasional_merdeka' => 'Kurikulum Nasional / Merdeka',
            'muatan_lokal_pesantren' => 'Muatan Lokal & Kepesantrenan Plus',
            default => 'Umum',
        };
    }
}
