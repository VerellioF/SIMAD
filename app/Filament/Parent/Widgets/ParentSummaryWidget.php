<?php

declare(strict_types=1);

namespace App\Filament\Parent\Widgets;

use App\Models\Student;
use App\Models\TuitionBill;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ParentSummaryWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = Auth::user();

        $students = Student::where('parent_id', $user?->id)->with('classroom')->get();
        $studentNames = $students->pluck('full_name')->join(', ') ?: 'Belum terhubung data santri';
        $studentIds = $students->pluck('id');

        $unpaidBills = TuitionBill::whereIn('student_id', $studentIds)
            ->where('status', 'unpaid')
            ->count();

        return [
            Stat::make('Putra / Putri', (string) $students->count() . ' Santri')
                ->description($studentNames)
                ->descriptionIcon('heroicon-m-heart')
                ->color('primary'),

            Stat::make('Tagihan SPP Belum Lunas', (string) $unpaidBills . ' Tagihan')
                ->description($unpaidBills > 0 ? 'Harap konfirmasi pembayaran ke TU' : 'Alhamdulillah, semua tagihan lunas')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($unpaidBills > 0 ? 'warning' : 'success'),

            Stat::make('Tahun Ajaran Aktif', '2026/2027 Ganjil')
                ->description('Semester Ganjil MI Plus')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info'),
        ];
    }
}
