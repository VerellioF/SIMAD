<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PpdbStatus;
use App\Models\PpdbRegistration;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PpdbService
{
    public function register(array $data): PpdbRegistration
    {
        return DB::transaction(function () use ($data) {
            $year = date('Y');

            // Generate sequential unique registration number
            $lastRecord = PpdbRegistration::query()
                ->where('registration_number', 'like', "PPDB-{$year}-%")
                ->lockForUpdate()
                ->latest('id')
                ->first();

            $nextNumber = 1;
            if ($lastRecord && preg_match("/PPDB-{$year}-(\d{4})/", $lastRecord->registration_number, $matches)) {
                $nextNumber = ((int) $matches[1]) + 1;
            }

            $registrationNumber = sprintf('PPDB-%s-%04d', $year, $nextNumber);

            // Normalize WhatsApp number
            $whatsapp = $this->normalizeWhatsAppNumber($data['parent_whatsapp']);

            // Store files safely
            $birthCertPath = $this->storeDocument($data['file_birth_certificate'], 'birth_certs');
            $familyCardPath = $this->storeDocument($data['file_family_card'], 'family_cards');
            $photoPath = isset($data['file_photo']) && $data['file_photo'] instanceof UploadedFile
                ? $this->storeDocument($data['file_photo'], 'photos')
                : null;

            return PpdbRegistration::create([
                'registration_number' => $registrationNumber,
                'child_name' => $data['child_name'],
                'nik' => $data['nik'],
                'gender' => $data['gender'],
                'birth_place' => $data['birth_place'],
                'birth_date' => $data['birth_date'],
                'previous_school' => $data['previous_school'],
                'parent_name' => $data['parent_name'],
                'parent_whatsapp' => $whatsapp,
                'parent_job' => $data['parent_job'] ?? null,
                'address' => $data['address'],
                'file_birth_certificate' => $birthCertPath,
                'file_family_card' => $familyCardPath,
                'file_photo' => $photoPath,
                'status' => PpdbStatus::Pending,
            ]);
        });
    }

    public function normalizeWhatsAppNumber(string $number): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $number);

        if (str_starts_with($cleaned, '08')) {
            return '628' . substr($cleaned, 2);
        }

        if (str_starts_with($cleaned, '628')) {
            return $cleaned;
        }

        return $cleaned;
    }

    protected function storeDocument(UploadedFile $file, string $folder): string
    {
        $year = date('Y');
        $extension = $file->getClientOriginalExtension();
        $filename = sprintf('%s_%s.%s', date('Ymd_His'), Str::random(12), $extension);

        return $file->storeAs("ppdb/{$year}/{$folder}", $filename, 'public');
    }
}
