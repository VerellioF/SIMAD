<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentMethod: string
{
    case TransferBank = 'transfer_bank';
    case TunaiMadrasah = 'tunai_madrasah';
    case Qris = 'qris';
    case VirtualAccount = 'virtual_account';

    public function label(): string
    {
        return match ($this) {
            self::TransferBank => 'Transfer Bank Manual',
            self::TunaiMadrasah => 'Tunai di Kantor Tata Usaha',
            self::Qris => 'QRIS (Otomatis)',
            self::VirtualAccount => 'Virtual Account (VA)',
        };
    }
}
