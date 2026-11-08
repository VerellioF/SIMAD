<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentMethod: string
{
    case TransferBank = 'transfer_bank';
    case TunaiMadrasah = 'tunai_madrasah';

    public function label(): string
    {
        return match ($this) {
            self::TransferBank => 'Transfer Bank (BSI / Online)',
            self::TunaiMadrasah => 'Tunai di Kantor Tata Usaha',
        };
    }
}
