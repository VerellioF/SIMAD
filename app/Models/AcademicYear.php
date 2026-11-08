<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Semester;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'semester',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'semester' => Semester::class,
            'is_active' => 'boolean',
        ];
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }

    public function academicGrades(): HasMany
    {
        return $this->hasMany(AcademicGrade::class);
    }

    public function tuitionBills(): HasMany
    {
        return $this->hasMany(TuitionBill::class);
    }
}
