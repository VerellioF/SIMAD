<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentConductRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'character_rule_id',
        'recorded_by',
        'type',
        'points',
        'title',
        'description',
        'incident_date',
        'follow_up_action',
    ];

    protected $casts = [
        'points' => 'integer',
        'incident_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function characterRule(): BelongsTo
    {
        return $this->belongsTo(CharacterRule::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
