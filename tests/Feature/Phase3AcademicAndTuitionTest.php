<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\GradeType;
use App\Enums\PaymentMethod;
use App\Enums\StudentStatus;
use App\Enums\SubjectCategory;
use App\Enums\TahfidzFluency;
use App\Enums\TahfidzTajweed;
use App\Enums\TuitionStatus;
use App\Enums\UserRole;
use App\Models\AcademicGrade;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TahfidzRecord;
use App\Models\TuitionBill;
use App\Models\TuitionPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Phase3AcademicAndTuitionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $teacher;
    protected User $parent;
    protected AcademicYear $academicYear;
    protected Classroom $classroom;
    protected Student $student;
    protected Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->academicYear = AcademicYear::create([
            'name' => '2026/2027',
            'start_date' => '2026-07-15',
            'end_date' => '2027-06-20',
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'name' => 'Staf Tata Usaha',
            'email' => 'admin@simad.id',
            'phone_number' => '081200000001',
            'role' => UserRole::Admin,
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->teacher = User::create([
            'name' => 'Ustadzah Fatimah Azzahra',
            'email' => 'guru@simad.id',
            'phone_number' => '081200000002',
            'role' => UserRole::Teacher,
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->parent = User::create([
            'name' => 'Bapak Bambang Wijaya',
            'email' => 'wali@simad.id',
            'phone_number' => '081200000003',
            'role' => UserRole::Parent,
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->classroom = Classroom::create([
            'name' => 'Kelas 1-A (Abu Bakar)',
            'grade_level' => 1,
            'academic_year_id' => $this->academicYear->id,
            'homeroom_teacher_id' => $this->teacher->id,
            'capacity' => 25,
        ]);

        $this->student = Student::create([
            'nis' => '262701002',
            'nisn' => '0123456790',
            'full_name' => 'Aisyah Putri Wijaya',
            'nickname' => 'Aisyah',
            'gender' => 'P',
            'birth_place' => 'Blitar',
            'birth_date' => '2019-08-14',
            'address' => 'Jl. Kenanga No. 45, Kalipucung, Blitar',
            'parent_id' => $this->parent->id,
            'classroom_id' => $this->classroom->id,
            'status' => StudentStatus::Active,
        ]);

        $this->subject = Subject::create([
            'code' => 'PAI-01',
            'name' => 'Akidah Akhlak',
            'category' => 'agama',
        ]);
    }

    public function test_teacher_can_view_tahfidz_journal_and_record_progress(): void
    {
        $viewResponse = $this->actingAs($this->teacher)->get(route('teacher.tahfidz.index'));
        $viewResponse->assertOk();
        $viewResponse->assertSeeText("Mutaba'ah Tahfidz Qur'an");
        $viewResponse->assertSee('Aisyah Putri Wijaya');

        $payload = [
            'student_id' => $this->student->id,
            'date' => now()->toDateString(),
            'surah_name' => 'An-Naba',
            'verse_range' => 'Ayat 1-15',
            'fluency_grade' => 'lancar',
            'tajweed_grade' => 'mumtaz',
            'notes' => 'Makharijul huruf sangat fasih dan tartil.',
        ];

        $postResponse = $this->actingAs($this->teacher)
            ->post(route('teacher.tahfidz.store'), $payload);

        $postResponse->assertRedirect(route('teacher.tahfidz.index'));
        $postResponse->assertSessionHas('success');

        $this->assertDatabaseHas('tahfidz_records', [
            'student_id' => $this->student->id,
            'teacher_id' => $this->teacher->id,
            'surah_name' => 'An-Naba',
            'verse_range' => 'Ayat 1-15',
            'fluency_grade' => 'lancar',
            'tajweed_grade' => 'mumtaz',
        ]);
    }

    public function test_parent_can_view_tahfidz_records_for_child(): void
    {
        TahfidzRecord::create([
            'student_id' => $this->student->id,
            'teacher_id' => $this->teacher->id,
            'date' => now()->toDateString(),
            'surah_name' => 'An-Naziat',
            'verse_range' => 'Ayat 1-20',
            'fluency_grade' => TahfidzFluency::Lancar,
            'tajweed_grade' => TahfidzTajweed::Mumtaz,
            'notes' => 'Sangat bagus, pertahankan hafalannya.',
        ]);

        $response = $this->actingAs($this->parent)->get(route('parent.tahfidz.index'));
        $response->assertOk();
        $response->assertSeeText("Mutaba'ah Tahfidz Al-Qur'an");
        $response->assertSee('An-Naziat');
        $response->assertSee('Ayat 1-20');
    }

    public function test_teacher_can_view_grade_input_and_record_academic_grades(): void
    {
        $viewResponse = $this->actingAs($this->teacher)->get(route('teacher.grades.index'));
        $viewResponse->assertOk();
        $viewResponse->assertSeeText('Buku Penilaian Akademik');
        $viewResponse->assertSee('Akidah Akhlak');

        $payload = [
            'subject_id' => $this->subject->id,
            'grade_type' => 'harian',
            'grades' => [
                [
                    'student_id' => $this->student->id,
                    'score' => 95,
                    'notes' => 'Pemahaman rukun iman sangat baik',
                ],
            ],
        ];

        $postResponse = $this->actingAs($this->teacher)
            ->post(route('teacher.grades.store'), $payload);

        $postResponse->assertRedirect();
        $postResponse->assertSessionHas('success');

        $this->assertDatabaseHas('academic_grades', [
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'classroom_id' => $this->classroom->id,
            'academic_year_id' => $this->academicYear->id,
            'grade_type' => 'harian',
            'score' => 95,
        ]);
    }

    public function test_parent_can_view_digital_raport_for_child(): void
    {
        AcademicGrade::create([
            'student_id' => $this->student->id,
            'classroom_id' => $this->classroom->id,
            'subject_id' => $this->subject->id,
            'academic_year_id' => $this->academicYear->id,
            'teacher_id' => $this->teacher->id,
            'grade_type' => GradeType::Harian,
            'score' => 92,
            'notes' => 'Prestasi memuaskan',
        ]);

        $response = $this->actingAs($this->parent)->get(route('parent.grades.index'));
        $response->assertOk();
        $response->assertSeeText('Lembar Hasil Belajar (Raport)');
        $response->assertSee('Akidah Akhlak');
        $response->assertSee('92');
    }

    public function test_admin_can_generate_monthly_tuition_bills(): void
    {
        $payload = [
            'month' => 8,
            'year' => 2026,
            'amount' => 150000,
            'bill_title' => 'SPP Bulan Agustus 2026',
            'due_date' => '2026-08-10',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.tuition.generate'), $payload);

        $response->assertRedirect(route('admin.tuition.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tuition_bills', [
            'student_id' => $this->student->id,
            'bill_title' => 'SPP Bulan Agustus 2026',
            'month' => 8,
            'year' => 2026,
            'amount' => 150000,
            'status' => 'unpaid',
        ]);
    }

    public function test_parent_can_submit_tuition_payment_transfer_confirmation(): void
    {
        $bill = TuitionBill::create([
            'student_id' => $this->student->id,
            'academic_year_id' => $this->academicYear->id,
            'bill_title' => 'SPP Bulan September 2026',
            'month' => 9,
            'year' => 2026,
            'amount' => 150000,
            'status' => TuitionStatus::Unpaid,
            'due_date' => '2026-09-10',
        ]);

        $file = UploadedFile::fake()->create('bukti_transfer.jpg', 300, 'image/jpeg');

        $response = $this->actingAs($this->parent)
            ->post(route('parent.tuition.confirm', $bill), [
                'bank_sender_name' => 'Bambang Wijaya - Bank Syariah Indonesia',
                'payment_date' => now()->toDateString(),
                'proof_attachment' => $file,
            ]);

        $response->assertRedirect(route('parent.tuition.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tuition_bills', [
            'id' => $bill->id,
            'status' => 'pending_verification',
        ]);

        $this->assertDatabaseHas('tuition_payments', [
            'tuition_bill_id' => $bill->id,
            'bank_sender_name' => 'Bambang Wijaya - Bank Syariah Indonesia',
            'payment_method' => 'transfer_bank',
        ]);
    }

    public function test_admin_can_verify_and_approve_tuition_payment(): void
    {
        $bill = TuitionBill::create([
            'student_id' => $this->student->id,
            'academic_year_id' => $this->academicYear->id,
            'bill_title' => 'SPP Bulan Oktober 2026',
            'month' => 10,
            'year' => 2026,
            'amount' => 150000,
            'status' => TuitionStatus::PendingVerification,
            'due_date' => '2026-10-10',
        ]);

        TuitionPayment::create([
            'tuition_bill_id' => $bill->id,
            'receipt_number' => 'PENDING-' . $bill->id,
            'amount_paid' => 150000,
            'payment_method' => PaymentMethod::TransferBank,
            'bank_sender_name' => 'Bambang Wijaya',
            'payment_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.tuition.verify', $bill), [
                'action' => 'approve',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tuition_bills', [
            'id' => $bill->id,
            'status' => 'paid',
        ]);

        $this->assertDatabaseHas('tuition_payments', [
            'tuition_bill_id' => $bill->id,
            'verified_by' => $this->admin->id,
        ]);
    }

    public function test_admin_can_record_cash_tuition_payment(): void
    {
        $bill = TuitionBill::create([
            'student_id' => $this->student->id,
            'academic_year_id' => $this->academicYear->id,
            'bill_title' => 'SPP Bulan November 2026',
            'month' => 11,
            'year' => 2026,
            'amount' => 150000,
            'status' => TuitionStatus::Unpaid,
            'due_date' => '2026-11-10',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.tuition.cash', $bill));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tuition_bills', [
            'id' => $bill->id,
            'status' => 'paid',
        ]);

        $this->assertDatabaseHas('tuition_payments', [
            'tuition_bill_id' => $bill->id,
            'payment_method' => 'tunai_madrasah',
            'verified_by' => $this->admin->id,
        ]);
    }

    public function test_parent_can_view_digital_tuition_receipt_for_paid_bill(): void
    {
        $bill = TuitionBill::create([
            'student_id' => $this->student->id,
            'academic_year_id' => $this->academicYear->id,
            'bill_title' => 'SPP Bulan Desember 2026',
            'month' => 12,
            'year' => 2026,
            'amount' => 150000,
            'status' => TuitionStatus::Paid,
            'due_date' => '2026-12-10',
        ]);

        TuitionPayment::create([
            'tuition_bill_id' => $bill->id,
            'receipt_number' => 'KWT-202612-0001',
            'amount_paid' => 150000,
            'payment_method' => PaymentMethod::TransferBank,
            'bank_sender_name' => 'Bambang Wijaya',
            'payment_date' => now()->toDateString(),
            'verified_by' => $this->admin->id,
            'verified_at' => now(),
        ]);

        $response = $this->actingAs($this->parent)->get(route('parent.tuition.receipt', $bill));

        $response->assertOk();
        $response->assertSeeText("MI PLUS RAUDLATUL MUTA'ALLIMIN");
        $response->assertSeeText('Bukti Pembayaran Sah (Lunas)');
        $response->assertSee('KWT-202612-0001');
        $response->assertSee('Aisyah Putri Wijaya');
        $response->assertSee('Rp 150.000');
    }
}
