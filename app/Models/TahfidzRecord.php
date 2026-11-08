<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TahfidzFluency;
use App\Enums\TahfidzTajweed;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TahfidzRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'teacher_id',
        'date',
        'surah_name',
        'verse_range',
        'fluency_grade',
        'tajweed_grade',
        'audio_recording_path',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'fluency_grade' => TahfidzFluency::class,
            'tajweed_grade' => TahfidzTajweed::class,
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
