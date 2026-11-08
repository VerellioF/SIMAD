<?php

declare(strict_types=1);

namespace App\Enums;

enum GradeType: string
{
    case Harian = 'harian';
    case Tugas = 'tugas';
    case Pts = 'pts';
    case Pas = 'pas';

    public function label(): string
    {
        return match ($this) {
            self::Harian => 'Ulangan Harian',
            self::Tugas => 'Tugas Mandiri / PR',
            self::Pts => 'Penilaian Tengah Semester (PTS)',
            self::Pas => 'Penilaian Akhir Semester (PAS)',
        };
    }
}
