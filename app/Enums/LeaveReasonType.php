<?php

declare(strict_types=1);

namespace App\Enums;

enum LeaveReasonType: string
{
    case Sakit = 'sakit';
    case Izin = 'izin';

    public function label(): string
    {
        return match ($this) {
            self::Sakit => 'Sakit (Surat Dokter / Keterangan)',
            self::Izin => 'Izin Kepentingan Keluarga',
        };
    }
}
