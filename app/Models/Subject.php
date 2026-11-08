<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SubjectCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'category',
    ];

    protected function casts(): array
    {
        return [
            'category' => SubjectCategory::class,
        ];
    }

    public function academicGrades(): HasMany
    {
        return $this->hasMany(AcademicGrade::class);
    }
}
