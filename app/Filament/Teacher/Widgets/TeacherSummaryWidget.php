<?php

declare(strict_types=1);

namespace App\Filament\Teacher\Widgets;

use App\Models\Classroom;
use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class TeacherSummaryWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = Auth::user();

        $classrooms = Classroom::where('homeroom_teacher_id', $user?->id)->get();
        $classroomCount = $classrooms->count();
        $studentCount = Student::whereIn('classroom_id', $classrooms->pluck('id'))->count();

        return [
            Stat::make('Kelas Binaan', (string) $classroomCount)
                ->description($classrooms->pluck('name')->join(', ') ?: 'Belum ditugaskan sebagai wali kelas')
                ->descriptionIcon('heroicon-m-home-modern')
                ->color('primary'),

            Stat::make('Santri Binaan', (string) $studentCount)
                ->description('Jumlah santri di kelas binaan')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),

            Stat::make('Jadwal Hari Ini', now()->translatedFormat('l, d F Y'))
                ->description('Semangat mendidik generasi Qur\'ani')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success'),
        ];
    }
}
