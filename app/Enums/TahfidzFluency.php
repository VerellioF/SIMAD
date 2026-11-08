<?php

declare(strict_types=1);

namespace App\Enums;

enum TahfidzFluency: string
{
    case Lancar = 'lancar';
    case KurangLancar = 'kurang_lancar';
    case Mengulang = 'mengulang';

    public function label(): string
    {
        return match ($this) {
            self::Lancar => 'Lancar (Mutqin)',
            self::KurangLancar => 'Kurang Lancar (Perlu Muraja\'ah)',
            self::Mengulang => 'Mengulang Setoran',
        };
    }
}
