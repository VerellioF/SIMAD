<?php

declare(strict_types=1);

namespace App\Enums;

enum GalleryCategory: string
{
    case SeniPentas = 'seni_pentas';
    case HadrahRebana = 'hadrah_rebana';
    case Pramuka = 'pramuka';
    case Fasilitas = 'fasilitas';
    case Prestasi = 'prestasi';

    public function label(): string
    {
        return match ($this) {
            self::SeniPentas => 'Pentas Seni Santri',
            self::HadrahRebana => 'Hadrah & Sholawat Rebana',
            self::Pramuka => 'Kepramukaan & Outbound',
            self::Fasilitas => 'Fasilitas Madrasah',
            self::Prestasi => 'Prestasi Santri & Guru',
        };
    }
}
