<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyWorshipRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'date',
        'subuh',
        'dzuhur',
        'ashar',
        'maghrib',
        'isya',
        'dhuha',
        'tadarus_quran',
        'birrul_walidain',
        'parent_notes',
        'teacher_notes',
    ];

    protected $casts = [
        'date' => 'date',
        'subuh' => 'boolean',
        'dzuhur' => 'boolean',
        'ashar' => 'boolean',
        'maghrib' => 'boolean',
        'isya' => 'boolean',
        'dhuha' => 'boolean',
        'tadarus_quran' => 'boolean',
        'birrul_walidain' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
