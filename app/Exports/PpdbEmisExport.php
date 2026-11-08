<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\PpdbRegistration;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PpdbEmisExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    private int $rowNumber = 0;

    public function collection(): Collection
    {
        return PpdbRegistration::query()->latest('id')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'No. Registrasi',
            'Nama Lengkap Santri',
            'NIK (16 Digit)',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Asal Sekolah (RA/TK)',
            'Nama Orang Tua / Wali',
            'No. WhatsApp',
            'Pekerjaan Orang Tua',
            'Alamat Lengkap',
            'Status Seleksi',
            'Catatan Panitia',
            'Tanggal Daftar',
        ];
    }

    /**
     * @param PpdbRegistration $row
     */
    public function map($row): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $row->registration_number,
            $row->child_name,
            "'{$row->nik}", // prefix quote to prevent scientific notation in Excel
            $row->gender === 'L' ? 'Laki-laki' : 'Perempuan',
            $row->birth_place,
            $row->birth_date->format('Y-m-d'),
            $row->previous_school,
            $row->parent_name,
            $row->parent_whatsapp,
            $row->parent_job ?? '-',
            $row->address,
            $row->status->label(),
            $row->admin_notes ?? '-',
            $row->created_at->format('Y-m-d H:i'),
        ];
    }
}
