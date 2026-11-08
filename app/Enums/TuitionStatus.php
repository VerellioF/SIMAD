<?php

declare(strict_types=1);

namespace App\Enums;

enum TuitionStatus: string
{
    case Unpaid = 'unpaid';
    case PendingVerification = 'pending_verification';
    case Paid = 'paid';

    public function label(): string
    {
        return match ($this) {
            self::Unpaid => 'Belum Dibayar',
            self::PendingVerification => 'Menunggu Verifikasi Admin',
            self::Paid => 'Lunas',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Unpaid => 'danger',
            self::PendingVerification => 'warning',
            self::Paid => 'success',
        };
    }
}
