<?php

declare(strict_types=1);

namespace App\Enums;

enum PpdbStatus: string
{
    case Pending = 'pending';
    case Verified = 'verified';
    case Accepted = 'accepted';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Verifikasi',
            self::Verified => 'Berkas Terverifikasi',
            self::Accepted => 'Diterima',
            self::Rejected => 'Ditolak / Berkas Tidak Sesuai',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Verified => 'info',
            self::Accepted => 'success',
            self::Rejected => 'danger',
        };
    }
}
