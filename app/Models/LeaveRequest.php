<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LeaveReasonType;
use App\Enums\LeaveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'parent_id',
        'reviewed_by',
        'start_date',
        'end_date',
        'reason_type',
        'description',
        'attachment_path',
        'status',
        'review_notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'reason_type' => LeaveReasonType::class,
            'status' => LeaveStatus::class,
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
