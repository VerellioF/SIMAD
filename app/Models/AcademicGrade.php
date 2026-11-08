<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GradeType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'classroom_id',
        'subject_id',
        'academic_year_id',
        'teacher_id',
        'grade_type',
        'score',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'grade_type' => GradeType::class,
            'score' => 'decimal:2',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
