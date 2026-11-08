<?php

declare(strict_types=1);

namespace App\Enums;

enum StudentStatus: string
{
    case Active = 'active';
    case Graduated = 'graduated';
    case Transferred = 'transferred';
    case Inactive = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Aktif Belajar',
            self::Graduated => 'Lulus / Alumni',
            self::Transferred => 'Pindah Sekolah',
            self::Inactive => 'Non-Aktif',
        };
    }
}
