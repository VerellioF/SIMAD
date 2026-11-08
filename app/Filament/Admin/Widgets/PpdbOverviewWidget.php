<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Enums\PpdbStatus;
use App\Models\PpdbRegistration;
use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PpdbOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalPpdb = PpdbRegistration::count();
        $pendingPpdb = PpdbRegistration::where('status', PpdbStatus::Pending)->count();
        $verifiedPpdb = PpdbRegistration::where('status', PpdbStatus::Verified)->count();
        $activeStudents = Student::count();

        return [
            Stat::make('Total Pendaftar PPDB', (string) $totalPpdb)
                ->description('Total berkas pendaftar baru')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            Stat::make('Menunggu Verifikasi', (string) $pendingPpdb)
                ->description('Perlu tindakan verifikasi TU')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingPpdb > 0 ? 'warning' : 'gray'),

            Stat::make('Berkas Terverifikasi', (string) $verifiedPpdb)
                ->description('Dokumen valid dan disetujui')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Total Santri Aktif', (string) $activeStudents)
                ->description('Terdaftar di buku induk madrasah')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info'),
        ];
    }
}
