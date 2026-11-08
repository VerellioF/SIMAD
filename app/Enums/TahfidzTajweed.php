<?php

declare(strict_types=1);

namespace App\Enums;

enum TahfidzTajweed: string
{
    case Mumtaz = 'mumtaz';
    case Jayyid = 'jayyid';
    case Maqbul = 'maqbul';

    public function label(): string
    {
        return match ($this) {
            self::Mumtaz => 'Mumtaz (Sangat Baik / Tepat Makhraj)',
            self::Jayyid => 'Jayyid (Baik)',
            self::Maqbul => 'Maqbul (Cukup)',
        };
    }
}
