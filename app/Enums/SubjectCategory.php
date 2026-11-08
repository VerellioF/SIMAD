<?php

declare(strict_types=1);

namespace App\Enums;

enum SubjectCategory: string
{
    case Agama = 'agama';
    case Umum = 'umum';
    case MuatanLokal = 'muatan_lokal';

    public function label(): string
    {
        return match ($this) {
            self::Agama => 'Pendidikan Agama Islam',
            self::Umum => 'Mata Pelajaran Umum',
            self::MuatanLokal => 'Muatan Lokal / Khas Madrasah',
        };
    }
}
