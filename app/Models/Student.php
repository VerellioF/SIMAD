<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StudentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'classroom_id',
        'nis',
        'nisn',
        'full_name',
        'nickname',
        'gender',
        'birth_place',
        'birth_date',
        'address',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'status' => StudentStatus::class,
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function tahfidzRecords(): HasMany
    {
        return $this->hasMany(TahfidzRecord::class);
    }

    public function academicGrades(): HasMany
    {
        return $this->hasMany(AcademicGrade::class);
    }

    public function tuitionBills(): HasMany
    {
        return $this->hasMany(TuitionBill::class);
    }

    public function extracurricularEnrollments(): HasMany
    {
        return $this->hasMany(ExtracurricularEnrollment::class);
    }

    public function dailyWorshipRecords(): HasMany
    {
        return $this->hasMany(DailyWorshipRecord::class);
    }
}
