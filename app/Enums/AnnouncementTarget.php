<?php

declare(strict_types=1);

namespace App\Enums;

enum AnnouncementTarget: string
{
    case Public = 'public';
    case Parent = 'parent';
    case All = 'all';

    public function label(): string
    {
        return match ($this) {
            self::Public => 'Masyarakat Umum',
            self::Parent => 'Wali Murid Santri',
            self::All => 'Semua Pengguna',
        };
    }
}
