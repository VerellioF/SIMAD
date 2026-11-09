<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Enums\GradeType;
use App\Enums\TahfidzFluency;
use App\Enums\TahfidzTajweed;
use App\Models\AcademicGrade;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TahfidzRecord;
use App\Models\TuitionBill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdvancedSchoolFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $teacher;
    protected User $parent;
    protected User $otherParent;
    protected AcademicYear $year;
    protected Classroom $classroom;
    protected Student $student;
    protected Subject $subjectAgama;
    protected Subject $subjectUmum;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'phone_number' => '081299990001',
        ]);

        $this->teacher = User::factory()->create([
            'role' => 'teacher',
            'name' => 'Bapak Ustadz Ahmad, S.Pd.I.',
            'phone_number' => '081299990002',
        ]);

        $this->parent = User::factory()->create([
            'role' => 'parent',
            'name' => 'Bapak Joko Santoso',
            'phone_number' => '081234567890',
        ]);

        $this->otherParent = User::factory()->create([
            'role' => 'parent',
            'name' => 'Bapak Hendra',
            'phone_number' => '081299998888',
        ]);

        $this->year = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        $this->classroom = Classroom::create([
            'academic_year_id' => $this->year->id,
            'homeroom_teacher_id' => $this->teacher->id,
            'name' => 'Kelas 1-A Ibnu Sina',
            'grade_level' => 1,
        ]);

        $this->student = Student::create([
            'parent_id' => $this->parent->id,
            'classroom_id' => $this->classroom->id,
            'full_name' => 'Muhammad Bilal',
            'nis' => '262701001',
            'nisn' => '0123456789',
            'gender' => 'L',
            'birth_place' => 'Blitar',
            'birth_date' => '2019-05-10',
            'address' => 'Kalipucung, Sanankulon, Blitar',
            'status' => 'active',
        ]);

        $this->subjectAgama = Subject::create([
            'code' => 'QH-01',
            'name' => 'Al-Qur\'an Hadits',
            'category' => 'agama',
        ]);

        $this->subjectUmum = Subject::create([
            'code' => 'MTK-01',
            'name' => 'Matematika',
            'category' => 'umum',
        ]);

        // Add sample grades
        AcademicGrade::create([
            'student_id' => $this->student->id,
            'classroom_id' => $this->classroom->id,
            'subject_id' => $this->subjectAgama->id,
            'academic_year_id' => $this->year->id,
            'teacher_id' => $this->teacher->id,
            'grade_type' => GradeType::Pas,
            'score' => 92,
        ]);

        // Add sample tahfidz
        TahfidzRecord::create([
            'student_id' => $this->student->id,
            'teacher_id' => $this->teacher->id,
            'date' => now()->toDateString(),
            'surah_name' => 'An-Naba\'',
            'verse_range' => '1 - 20',
            'fluency_grade' => TahfidzFluency::Lancar,
            'tajweed_grade' => TahfidzTajweed::Mumtaz,
        ]);

        // Add attendance
        Attendance::create([
            'student_id' => $this->student->id,
            'classroom_id' => $this->classroom->id,
            'date' => now()->toDateString(),
            'status' => AttendanceStatus::Hadir,
        ]);
    }

    /* =========================================================================
     * FITUR 1: CETAK RAPOR DIGITAL SISWA (PDF)
     * ========================================================================= */

    public function test_admin_can_download_student_report_card_pdf(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.students.report_card', $this->student));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_teacher_can_download_student_report_card_pdf(): void
    {
        $response = $this->actingAs($this->teacher)
            ->get(route('teacher.grades.report_card', $this->student));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_parent_can_download_their_child_report_card(): void
    {
        $response = $this->actingAs($this->parent)
            ->get(route('parent.grades.report_card', $this->student));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_parent_cannot_download_unrelated_student_report_card(): void
    {
        $response = $this->actingAs($this->otherParent)
            ->get(route('parent.grades.report_card', $this->student));

        $response->assertForbidden();
    }

    /* =========================================================================
     * FITUR 2: KARTU PELAJAR (KTS) & KARTU UJIAN DIGITAL (PDF)
     * ========================================================================= */

    public function test_admin_can_download_student_identity_card_pdf(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.students.card', $this->student));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_can_download_student_exam_card_pdf(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.students.exam_card', $this->student));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_can_download_classroom_bulk_exam_cards_pdf(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.classrooms.exam_cards', $this->classroom));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_parent_can_download_their_child_student_identity_card(): void
    {
        $response = $this->actingAs($this->parent)
            ->get(route('parent.students.card', $this->student));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    /* =========================================================================
     * FITUR 3: PENGINGAT SPP WHATSAPP DENGAN LINK QRIS
     * ========================================================================= */

    public function test_admin_can_send_tuition_reminder_with_direct_qris_link(): void
    {
        $bill = TuitionBill::create([
            'student_id' => $this->student->id,
            'academic_year_id' => $this->year->id,
            'bill_title' => 'SPP Bulan Agustus 2026',
            'month' => 8,
            'year' => 2026,
            'amount' => 150000,
            'status' => 'unpaid',
            'due_date' => '2026-08-10',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.tuition.reminder', $bill));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check WhatsApp log
        $this->assertDatabaseHas('whatsapp_logs', [
            'type' => 'tuition_reminder',
            'student_id' => $this->student->id,
            'recipient_phone' => '6281234567890',
        ]);

        // Verify message contains QRIS link
        $log = \App\Models\WhatsAppLog::where('type', 'tuition_reminder')->first();
        $this->assertNotNull($log);
        $this->assertStringContainsString('/payment/checkout/', $log->message);
        $this->assertStringContainsString('SPP Bulan Agustus 2026', $log->message);
    }

    public function test_admin_can_broadcast_tuition_reminders_to_unpaid_students(): void
    {
        TuitionBill::create([
            'student_id' => $this->student->id,
            'academic_year_id' => $this->year->id,
            'bill_title' => 'SPP Bulan September 2026',
            'month' => 9,
            'year' => 2026,
            'amount' => 150000,
            'status' => 'unpaid',
            'due_date' => '2026-09-10',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.tuition.broadcast_reminders'), [
                'month' => 9,
                'year' => 2026,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('whatsapp_logs', [
            'type' => 'tuition_reminder',
            'student_id' => $this->student->id,
        ]);
    }
}
