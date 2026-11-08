<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Teacher = 'teacher';
    case Parent = 'parent';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Staf TU / Admin',
            self::Teacher => 'Guru / Wali Kelas',
            self::Parent => 'Wali Murid',
        };
    }
}
