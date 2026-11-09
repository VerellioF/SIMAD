<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Models\AcademicGrade;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TahfidzRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdfWrapper;

class ReportCardPdfService
{
    public function generate(Student $student, ?AcademicYear $academicYear = null): DomPdfWrapper
    {
        $year = $academicYear ?? AcademicYear::where('is_active', true)->first() ?? AcademicYear::latest('id')->first();

        // Load subjects and grade records
        $subjects = Subject::orderBy('category')->orderBy('id')->get();
        $grades = AcademicGrade::where('student_id', $student->id)
            ->when($year, fn ($q) => $q->where('academic_year_id', $year->id))
            ->get();

        $subjectReport = [];
        foreach ($subjects as $subject) {
            $subjGrades = $grades->where('subject_id', $subject->id);

            $harian = $subjGrades->where('grade_type.value', 'harian')->avg('score')
                ?? $subjGrades->where('grade_type.value', 'tugas')->avg('score');
            $pts = $subjGrades->where('grade_type.value', 'pts')->avg('score');
            $pas = $subjGrades->where('grade_type.value', 'pas')->avg('score');

            // Calculate final grade safely preserving genuine zero scores
            $rawScores = array_filter(
                [$harian, $pts, $pas],
                fn ($val) => $val !== null && $val !== ''
            );
            $scores = array_map('floatval', $rawScores);
            $finalScore = ! empty($scores) ? array_sum($scores) / count($scores) : null;

            $predicate = match (true) {
                $finalScore === null => '-',
                $finalScore >= 88 => 'A',
                $finalScore >= 76 => 'B',
                $finalScore >= 65 => 'C',
                default => 'D',
            };

            $description = match ($predicate) {
                'A' => 'Menunjukkan penguasaan kompetensi materi dengan sangat baik dan mandiri.',
                'B' => 'Menunjukkan pemahaman materi yang baik dan tuntas.',
                'C' => 'Cukup menguasai capaian pembelajaran pokok, perlu sedikit pengulangan.',
                'D' => 'Perlu bimbingan dan pendampingan intensif pada materi pembelajaran.',
                default => 'Belum ada penilaian',
            };

            $subjectReport[$subject->category->value][] = [
                'subject' => $subject,
                'harian' => $harian ? round((float)$harian, 1) : '-',
                'pts' => $pts ? round((float)$pts, 1) : '-',
                'pas' => $pas ? round((float)$pas, 1) : '-',
                'final' => $finalScore ? round((float)$finalScore, 1) : '-',
                'predicate' => $predicate,
                'description' => $description,
            ];
        }

        // Attendance stats
        $attendances = Attendance::where('student_id', $student->id)->get();
        $attendanceStats = [
            'hadir' => $attendances->where('status', AttendanceStatus::Hadir)->count(),
            'sakit' => $attendances->where('status', AttendanceStatus::Sakit)->count(),
            'izin' => $attendances->where('status', AttendanceStatus::Izin)->count(),
            'alpa' => $attendances->where('status', AttendanceStatus::Alpa)->count(),
        ];

        // Tahfidz & Quran achievements
        $tahfidzRecords = TahfidzRecord::where('student_id', $student->id)->latest('date')->take(5)->get();

        // Conduct & Character
        $conductScore = $student->calculateTotalConductScore();
        $conductPredicate = match (true) {
            $conductScore >= 110 => 'A (Sangat Baik / Teladan)',
            $conductScore >= 95 => 'B (Baik / Berakhlak Karimah)',
            $conductScore >= 80 => 'C (Cukup / Perlu Pembinaan)',
            default => 'D (Kurang / Perhatian Khusus)',
        };

        $school = [
            'name' => config('simad.school_name', 'MI Plus Raudlatul Muta\'allimin'),
            'address' => config('simad.school_address', 'Kalipucung, Sanankulon, Blitar'),
            'nsm' => config('simad.nsm', '111235050000'),
            'npsn' => config('simad.npsn', '60700000'),
            'principal' => 'K.H. Nur Hadi, M.Pd.I.',
            'principal_nip' => '197508152005011002',
        ];

        return Pdf::loadView('pdf.report-card', compact(
            'student',
            'year',
            'subjectReport',
            'attendanceStats',
            'tahfidzRecords',
            'conductScore',
            'conductPredicate',
            'school'
        ))->setPaper('a4', 'portrait')
          ->setOption('isHtml5ParserEnabled', true)
          ->setOption('isRemoteEnabled', true);
    }
}
