<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PpdbRegistration;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdfWrapper;

class PpdbPdfService
{
    public function generateProofPdf(PpdbRegistration $registration): DomPdfWrapper
    {
        $school = [
            'name' => config('simad.school_name'),
            'tagline' => config('simad.school_tagline'),
            'address' => config('simad.school_address'),
            'nsm' => config('simad.nsm'),
            'npsn' => config('simad.npsn'),
            'phone' => config('simad.contact_phone'),
        ];

        return Pdf::loadView('pdf.ppdb-proof', compact('registration', 'school'))
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);
    }
}
