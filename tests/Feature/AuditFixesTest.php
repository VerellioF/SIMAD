<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\PpdbStatus;
use App\Enums\UserRole;
use App\Models\AcademicGrade;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\ContactMessage;
use App\Models\LeaveRequest;
use App\Models\PaymentTransaction;
use App\Models\PpdbRegistration;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TuitionBill;
use App\Models\TuitionPayment;
use App\Models\User;
use App\Services\PaymentGatewayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditFixesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $teacher1;
    protected User $teacher2;
    protected Classroom $class1;
    protected Classroom $class2;
    protected Student $student1;
    protected AcademicYear $year;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin,
            'phone_number' => '081299990001',
        ]);

        $this->year = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        $this->teacher1 = User::factory()->create([
            'role' => UserRole::Teacher,
            'name' => 'Ustadz Ahmad',
            'phone_number' => '081299990002',
        ]);

        $this->teacher2 = User::factory()->create([
            'role' => UserRole::Teacher,
            'name' => 'Ustadzah Fatimah',
            'phone_number' => '081299990003',
        ]);

        $this->class1 = Classroom::create([
            'academic_year_id' => $this->year->id,
            'name' => 'Kelas 1-A',
            'grade_level' => 1,
            'homeroom_teacher_id' => $this->teacher1->id,
        ]);

        $this->class2 = Classroom::create([
            'academic_year_id' => $this->year->id,
            'name' => 'Kelas 2-B',
            'grade_level' => 2,
            'homeroom_teacher_id' => $this->teacher2->id,
        ]);

        $parent = User::factory()->create([
            'role' => UserRole::Parent,
            'phone_number' => '081299990004',
        ]);

        $this->student1 = Student::create([
            'parent_id' => $parent->id,
            'classroom_id' => $this->class1->id,
            'full_name' => 'Ali Zainal',
            'nis' => '2601001',
            'nisn' => '350500000001',
            'gender' => 'L',
            'birth_place' => 'Blitar',
            'birth_date' => '2019-01-01',
            'address' => 'Kalipucung, Blitar',
            'status' => 'active',
        ]);
    }

    public function test_payment_checkout_renders_ppdb_transaction_without_route_error(): void
    {
        $reg = PpdbRegistration::create([
            'registration_number' => 'PPDB-2026-0001',
            'child_name' => 'Aisyah Putri',
            'nik' => '3505012345670001',
            'gender' => 'P',
            'birth_place' => 'Blitar',
            'birth_date' => '2019-05-15',
            'previous_school' => 'TK Al-Huda',
            'parent_name' => 'Bapak Rudi',
            'parent_whatsapp' => '081234567890',
            'address' => 'Kalipucung, Blitar',
            'file_birth_certificate' => 'berkas/akta.pdf',
            'file_family_card' => 'berkas/kk.pdf',
            'status' => PpdbStatus::Pending,
            'payment_status' => 'unpaid',
            'registration_fee' => 50000,
        ]);

        $gateway = app(PaymentGatewayService::class);
        $tx = $gateway->createForPpdbRegistration($reg);

        $response = $this->get(route('payment.checkout', $tx->reference));
        $response->assertOk();
        $response->assertSee('Aisyah Putri');
    }

    public function test_record_cash_payment_updates_existing_payment_record(): void
    {
        $bill = TuitionBill::create([
            'student_id' => $this->student1->id,
            'academic_year_id' => $this->year->id,
            'bill_title' => 'SPP Juli',
            'month' => 7,
            'year' => 2026,
            'amount' => 100000,
            'status' => 'unpaid',
            'due_date' => '2026-07-10',
        ]);

        // Prior pending payment
        TuitionPayment::create([
            'tuition_bill_id' => $bill->id,
            'receipt_number' => 'PENDING-TEST-1',
            'amount_paid' => 100000,
            'payment_method' => 'transfer_bank',
            'payment_date' => '2026-07-05',
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.tuition.cash', $bill));

        $this->assertEquals(1, TuitionPayment::where('tuition_bill_id', $bill->id)->count());
        $payment = TuitionPayment::where('tuition_bill_id', $bill->id)->first();
        $this->assertEquals('tunai_madrasah', $payment->payment_method?->value ?? $payment->payment_method);
        $this->assertEquals($this->admin->id, $payment->verified_by);
    }

    public function test_admin_teacher_phone_uniqueness_validation(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.teachers.store'), [
            'name' => 'Guru Baru',
            'email' => 'gurubaru@miplus.sch.id',
            'phone_number' => $this->teacher1->phone_number, // duplicate!
            'password' => 'secret123',
        ]);

        $response->assertSessionHasErrors('phone_number');
    }

    public function test_contact_form_stores_message_in_database(): void
    {
        $response = $this->post(route('public.contact.send'), [
            'name' => 'Siti Nurhaliza',
            'phone' => '081234567899',
            'category' => 'ppdb',
            'message' => 'Kapan pendaftaran gelombang kedua dibuka?',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Siti Nurhaliza',
            'phone' => '081234567899',
            'category' => 'ppdb',
        ]);
    }

    public function test_non_homeroom_teacher_cannot_approve_other_class_leave_request(): void
    {
        $leave = LeaveRequest::create([
            'student_id' => $this->student1->id,
            'parent_id' => $this->student1->parent_id,
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-02',
            'reason_type' => 'sakit',
            'description' => 'Demam',
            'status' => 'pending',
        ]);

        // Teacher 2 is homeroom of Class 2, while Student 1 is in Class 1
        $response = $this->actingAs($this->teacher2)->post(route('teacher.leave.update', $leave), [
            'status' => 'approved',
        ]);

        $response->assertForbidden();

        // Teacher 1 is homeroom of Class 1 -> allowed
        $response2 = $this->actingAs($this->teacher1)->post(route('teacher.leave.update', $leave), [
            'status' => 'approved',
        ]);

        $response2->assertSessionHas('success');
    }

    public function test_ppdb_check_status_masks_nik(): void
    {
        $reg = PpdbRegistration::create([
            'registration_number' => 'PPDB-2026-0002',
            'child_name' => 'Zaidan Al-Farisi',
            'nik' => '3505012345678901',
            'gender' => 'L',
            'birth_place' => 'Blitar',
            'birth_date' => '2019-03-20',
            'previous_school' => 'RA Perwanida',
            'parent_name' => 'Bapak Zaid',
            'parent_whatsapp' => '081233445566',
            'address' => 'Blitar',
            'file_birth_certificate' => 'berkas/akta.pdf',
            'file_family_card' => 'berkas/kk.pdf',
            'status' => PpdbStatus::Pending,
            'payment_status' => 'unpaid',
            'registration_fee' => 50000,
        ]);

        $response = $this->get(route('ppdb.check_status', ['q' => 'PPDB-2026-0002']));
        $response->assertOk();
        $response->assertSee('350501******8901');
        $response->assertDontSee('3505012345678901');
    }

    public function test_admin_can_enroll_accepted_ppdb_into_active_student(): void
    {
        $reg = PpdbRegistration::create([
            'registration_number' => 'PPDB-2026-0003',
            'child_name' => 'Khadijah Zahra',
            'nik' => '3505019999990001',
            'gender' => 'P',
            'birth_place' => 'Blitar',
            'birth_date' => '2019-07-07',
            'previous_school' => 'TK Dharma Wanita',
            'parent_name' => 'Ibu Aminah',
            'parent_whatsapp' => '081999888777',
            'address' => 'Sanankulon, Blitar',
            'file_birth_certificate' => 'berkas/akta.pdf',
            'file_family_card' => 'berkas/kk.pdf',
            'status' => PpdbStatus::Accepted,
            'payment_status' => 'paid',
            'registration_fee' => 50000,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.ppdb.enroll', $reg), [
            'classroom_id' => $this->class1->id,
            'nis' => '2601999',
        ]);

        $response->assertRedirect(route('admin.students.index', ['classroom_id' => $this->class1->id]));

        $this->assertDatabaseHas('students', [
            'full_name' => 'Khadijah Zahra',
            'nisn' => '3505019999990001',
            'classroom_id' => $this->class1->id,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('users', [
            'phone_number' => '081999888777',
            'role' => UserRole::Parent,
        ]);
    }

    public function test_parent_can_login_with_alternate_phone_number_format(): void
    {
        // Stored as 628... in DB
        $parent = User::factory()->create([
            'phone_number' => '6281233344455',
            'password' => bcrypt('secret123'),
            'role' => UserRole::Parent,
            'is_active' => true,
        ]);

        // Login with 08... format
        $response = $this->post(route('login.post'), [
            'login' => '081233344455',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('parent.dashboard'));
        $this->assertAuthenticatedAs($parent);
    }

    public function test_multiple_tuition_bills_can_be_generated_in_same_month_with_different_titles(): void
    {
        $this->actingAs($this->admin)->post(route('admin.tuition.generate'), [
            'month' => 8,
            'year' => 2026,
            'amount' => 150000,
            'bill_title' => 'SPP Bulan Agustus',
            'due_date' => '2026-08-10',
        ]);

        $this->actingAs($this->admin)->post(route('admin.tuition.generate'), [
            'month' => 8,
            'year' => 2026,
            'amount' => 50000,
            'bill_title' => 'Infaq Gedung Agustus',
            'due_date' => '2026-08-10',
        ]);

        $this->assertEquals(1, TuitionBill::where('student_id', $this->student1->id)->where('bill_title', 'SPP Bulan Agustus')->count());
        $this->assertEquals(1, TuitionBill::where('student_id', $this->student1->id)->where('bill_title', 'Infaq Gedung Agustus')->count());
    }

    public function test_classroom_homeroom_teacher_validation_rejects_non_teacher(): void
    {
        $parent = User::factory()->create([
            'role' => UserRole::Parent,
            'phone_number' => '089999111222',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.classrooms.store'), [
            'name' => 'Kelas Baru',
            'grade_level' => 3,
            'homeroom_teacher_id' => $parent->id,
        ]);

        $response->assertSessionHasErrors('homeroom_teacher_id');
    }

    public function test_unauthorized_teacher_cannot_record_attendance_for_other_classroom(): void
    {
        // Teacher 2 is not homeroom and has no schedule in class 1
        $response = $this->actingAs($this->teacher2)->post(route('teacher.attendance.store'), [
            'date' => now()->toDateString(),
            'classroom_id' => $this->class1->id,
            'attendances' => [
                [
                    'student_id' => $this->student1->id,
                    'status' => 'hadir',
                ],
            ],
        ]);

        $response->assertForbidden();
    }

    public function test_unauthorized_teacher_cannot_record_grades_for_other_classroom(): void
    {
        $subject = Subject::create([
            'name' => 'Bahasa Arab',
            'code' => 'ARB',
            'category' => 'agama',
        ]);

        // Teacher 2 is not homeroom and has no schedule in class 1
        $response = $this->actingAs($this->teacher2)->post(route('teacher.grades.store'), [
            'classroom_id' => $this->class1->id,
            'subject_id' => $subject->id,
            'grade_type' => 'harian',
            'grades' => [
                [
                    'student_id' => $this->student1->id,
                    'score' => 90,
                ],
            ],
        ]);

        $response->assertForbidden();
    }

    public function test_article_content_escapes_malicious_script_tags(): void
    {
        $article = \App\Models\Article::create([
            'author_id' => $this->admin->id,
            'title' => 'Uji Keamanan XSS',
            'slug' => 'uji-keamanan-xss',
            'content' => '<script>alert("XSS")</script>Paragraf warta aman.',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get(route('public.news.article', $article->slug));
        $response->assertOk();
        $response->assertDontSee('<script>alert("XSS")</script>', false);
        $response->assertSee('&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;', false);
    }

    public function test_article_thumbnail_rejects_svg_upload(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $fakeSvg = \Illuminate\Http\UploadedFile::fake()->create('malicious.svg', 10, 'image/svg+xml');

        $response = $this->actingAs($this->admin)->post(route('admin.articles.store'), [
            'title' => 'Judul Artikel',
            'content' => 'Isi berita artikel',
            'is_published' => 1,
            'thumbnail' => $fakeSvg,
        ]);

        $response->assertSessionHasErrors('thumbnail');
    }

    public function test_gallery_image_rejects_svg_upload(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $fakeSvg = \Illuminate\Http\UploadedFile::fake()->create('malicious.svg', 10, 'image/svg+xml');

        $response = $this->actingAs($this->admin)->post(route('admin.galleries.store'), [
            'title' => 'Foto Kegiatan',
            'category' => 'prestasi',
            'image' => $fakeSvg,
        ]);

        $response->assertSessionHasErrors('image');
    }

    public function test_payment_gateway_rejects_unrecognized_provider_webhook(): void
    {
        config(['simad.payment.provider' => 'unrecognized_fake_gateway']);
        $service = app(PaymentGatewayService::class);

        $request = \Illuminate\Http\Request::create('/payment/webhook', 'POST', [
            'reference' => 'SPP-999-FAKE',
            'status' => 'paid',
        ]);

        $this->assertFalse($service->verifyWebhookSignature($request));
    }

    public function test_security_headers_are_present_on_web_responses(): void
    {
        $response = $this->get(route('home'));
        $response->assertOk();
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }
}
