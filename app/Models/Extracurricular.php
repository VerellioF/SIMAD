<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Extracurricular extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'category',
        'instructor_name',
        'schedule_day',
        'schedule_time',
        'location',
        'description',
        'image_path',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Extracurricular $ekskul) {
            if (empty($ekskul->slug)) {
                $ekskul->slug = Str::slug($ekskul->name);
            }
        });
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(ExtracurricularEnrollment::class);
    }
}
