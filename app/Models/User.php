<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserRole;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'password',
        'role',
        'avatar_url',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isTeacher(): bool
    {
        return $this->role === UserRole::Teacher;
    }

    public function isParent(): bool
    {
        return $this->role === UserRole::Parent;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->is_active) {
            return false;
        }

        return match ($panel->getId()) {
            'admin' => $this->isAdmin(),
            'teacher' => $this->isTeacher(),
            'parent' => $this->isParent(),
            default => false,
        };
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'parent_id');
    }

    public function homeroomClassrooms(): HasMany
    {
        return $this->hasMany(Classroom::class, 'homeroom_teacher_id');
    }

    public function recordedAttendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'recorded_by');
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'parent_id');
    }

    public function reviewedLeaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'reviewed_by');
    }

    public function tahfidzRecords(): HasMany
    {
        return $this->hasMany(TahfidzRecord::class, 'teacher_id');
    }

    public function academicGrades(): HasMany
    {
        return $this->hasMany(AcademicGrade::class, 'teacher_id');
    }

    public function verifiedTuitionPayments(): HasMany
    {
        return $this->hasMany(TuitionPayment::class, 'verified_by');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'author_id');
    }
}
