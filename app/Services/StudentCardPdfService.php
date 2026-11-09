<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Classroom;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdfWrapper;

class StudentCardPdfService
{
    /**
     * Generate single Student Identity Card (KTS).
     */
    public function generateStudentCard(Student $student): DomPdfWrapper
    {
        $school = $this->getSchoolData();

        return Pdf::loadView('pdf.student-card', compact('student', 'school'))
            ->setPaper([0, 0, 242.64, 153.07], 'landscape') // Standard CR80 ID Card dimensions (85.6mm x 54mm in pt)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);
    }

    /**
     * Generate Exam Card (Kartu Peserta Ujian) for a single student.
     */
    public function generateExamCard(Student $student, string $examTitle = 'Asesmen Sumatif Akhir Semester (ASAS)'): DomPdfWrapper
    {
        $school = $this->getSchoolData();
        $students = collect([$student]);

        return Pdf::loadView('pdf.exam-card', compact('students', 'school', 'examTitle'))
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);
    }

    /**
     * Generate bulk Exam Cards for all students in a classroom.
     */
    public function generateClassExamCards(Classroom $classroom, string $examTitle = 'Asesmen Sumatif Akhir Semester (ASAS)'): DomPdfWrapper
    {
        $school = $this->getSchoolData();
        $students = $classroom->students()->where('status', 'active')->orderBy('full_name')->get();

        return Pdf::loadView('pdf.exam-card', compact('students', 'school', 'examTitle', 'classroom'))
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);
    }

    protected function getSchoolData(): array
    {
        return [
            'name' => config('simad.school_name', 'MI Plus Raudlatul Muta\'allimin'),
            'address' => config('simad.school_address', 'Kalipucung, Sanankulon, Blitar'),
            'nsm' => config('simad.nsm', '111235050000'),
            'npsn' => config('simad.npsn', '60700000'),
            'principal' => 'K.H. Nur Hadi, M.Pd.I.',
            'principal_nip' => '197508152005011002',
        ];
    }
}
