<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Enums\LeaveReasonType;
use App\Enums\LeaveStatus;
use App\Enums\UserRole;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\CharacterRule;
use App\Models\Classroom;
use App\Models\ClassSchedule;
use App\Models\LeaveRequest;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Models\WhatsAppLog;
use App\Services\WhatsAppNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $teacher;
    protected User $parent;
    protected Student $student;
    protected Classroom $classroom;
    protected AcademicYear $academicYear;
    protected Subject $subject;
    protected ClassSchedule $schedule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->academicYear = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'ganjil',
            'is_active' => true,
            'start_date' => '2026-07-15',
            'end_date' => '2026-12-31',
        ]);

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin,
            'email' => 'admin_wa@simad.id',
            'name' => 'Admin TU',
            'phone_number' => '081299990000',
        ]);

        $this->teacher = User::factory()->create([
            'role' => UserRole::Teacher,
            'email' => 'teacher_wa@simad.id',
            'name' => 'Bpk. Ahmad Fauzi, S.Pd.I',
            'phone_number' => '081299990001',
        ]);

        $this->parent = User::factory()->create([
            'role' => UserRole::Parent,
            'email' => 'parent_wa@simad.id',
            'name' => 'Bpk. Hendra Gunawan',
            'phone_number' => '081234567890',
        ]);

        $this->classroom = Classroom::create([
            'name' => '1-A (Abu Bakar)',
            'grade_level' => 1,
            'capacity' => 28,
            'academic_year_id' => $this->academicYear->id,
            'homeroom_teacher_id' => $this->teacher->id,
        ]);

        $this->student = Student::create([
            'parent_id' => $this->parent->id,
            'classroom_id' => $this->classroom->id,
            'nis' => '2026099',
            'nisn' => '0098765432',
            'full_name' => 'Muhammad Bilal Al-Fatih',
            'nickname' => 'Bilal',
            'gender' => 'L',
            'birth_place' => 'Blitar',
            'birth_date' => '2019-05-15',
            'address' => 'Jl. Anggrek No. 12, Sanankulon, Blitar',
            'status' => 'active',
        ]);

        $this->subject = Subject::create([
            'name' => 'Fikih Ibadah',
            'code' => 'PAI-FQ',
            'category' => 'agama',
        ]);

        $this->schedule = ClassSchedule::create([
            'classroom_id' => $this->classroom->id,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'academic_year_id' => $this->academicYear->id,
            'day_of_week' => 'senin',
            'period_order' => 1,
            'start_time' => '07:30',
            'end_time' => '08:45',
        ]);
    }

    public function test_service_formats_phone_numbers_correctly(): void
    {
        $service = new WhatsAppNotificationService();

        $this->assertEquals('6281234567890', $service->formatPhoneNumber('081234567890'));
        $this->assertEquals('6281234567890', $service->formatPhoneNumber('+6281234567890'));
        $this->assertEquals('6281234567890', $service->formatPhoneNumber('6281234567890'));
        $this->assertEquals('6281234567890', $service->formatPhoneNumber('0812-3456-7890'));
        $this->assertNull($service->formatPhoneNumber(null));
        $this->assertNull($service->formatPhoneNumber(''));
    }

    public function test_service_generates_valid_whatsapp_url(): void
    {
        $service = new WhatsAppNotificationService();
        $url = $service->generateClickToChatUrl('081234567890', 'Halo Bapak/Ibu');

        $this->assertStringStartsWith('https://wa.me/6281234567890?text=', $url);
        $this->assertStringContainsString('Halo', $url);
    }

    public function test_service_dispatches_via_gateway_when_configured(): void
    {
        config([
            'simad.whatsapp.enabled' => true,
            'simad.whatsapp.auto_send' => true,
            'simad.whatsapp.token' => 'dummy-secret-token',
            'simad.whatsapp.endpoint' => 'https://api.fonnte.com/send',
            'simad.whatsapp.provider' => 'fonnte',
        ]);

        Http::fake([
            'https://api.fonnte.com/send' => Http::response(['status' => true, 'target' => ['6281234567890']], 200),
        ]);

        $service = new WhatsAppNotificationService();
        $res = $service->sendMessage('081234567890', 'Test Auto Send Message', $this->student, 'test', $this->teacher->id);

        $this->assertTrue($res['success']);
        $this->assertEquals('sent', $res['status']);

        $this->assertDatabaseHas('whatsapp_logs', [
            'student_id' => $this->student->id,
            'recipient_phone' => '6281234567890',
            'status' => 'sent',
            'type' => 'test',
        ]);

        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'dummy-secret-token') &&
                   $request['target'] === '6281234567890';
        });
    }

    public function test_attendance_submission_with_alpa_logs_whatsapp_alert(): void
    {
        $response = $this->actingAs($this->teacher)->post(route('teacher.attendance.store'), [
            'date' => '2026-07-20',
            'classroom_id' => $this->classroom->id,
            'subject_id' => $this->subject->id,
            'class_schedule_id' => $this->schedule->id,
            'attendances' => [
                [
                    'student_id' => $this->student->id,
                    'status' => 'alpa',
                    'notes' => 'Tanpa kabar sejak pagi',
                ],
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('wa_alerts');

        $this->assertDatabaseHas('whatsapp_logs', [
            'student_id' => $this->student->id,
            'type' => 'attendance',
            'recipient_phone' => '6281234567890',
        ]);

        $log = WhatsAppLog::where('student_id', $this->student->id)->latest()->first();
        $this->assertNotNull($log);
        $this->assertStringContainsString('Tidak Hadir / Alpa', $log->message);
        $this->assertStringContainsString($this->student->full_name, $log->message);
    }

    public function test_teacher_conduct_record_triggers_whatsapp_alert(): void
    {
        $rule = CharacterRule::create([
            'name' => 'Terlambat masuk kelas',
            'type' => 'pelanggaran',
            'category' => 'kedisiplinan',
            'points' => 5,
            'description' => 'Terlambat lebih dari 10 menit.',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->teacher)->post(route('teacher.conduct.store'), [
            'student_id' => $this->student->id,
            'character_rule_id' => $rule->id,
            'type' => 'pelanggaran',
            'points' => 5,
            'title' => 'Terlambat masuk kelas Fikih',
            'description' => 'Ananda terlambat 15 menit tanpa surat pengantar.',
            'incident_date' => '2026-07-20',
            'follow_up_action' => 'Dinasehati dan diajak sholat dhuha bersama.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('wa_direct_url');

        $this->assertDatabaseHas('whatsapp_logs', [
            'student_id' => $this->student->id,
            'type' => 'conduct',
            'recipient_phone' => '6281234567890',
        ]);

        $log = WhatsAppLog::where('type', 'conduct')->first();
        $this->assertNotNull($log);
        $this->assertStringContainsString('Terlambat masuk kelas Fikih', $log->message);
        $this->assertStringContainsString('-5 poin', $log->message);
    }

    public function test_teacher_leave_review_triggers_whatsapp_alert(): void
    {
        $leave = LeaveRequest::create([
            'student_id' => $this->student->id,
            'parent_id' => $this->parent->id,
            'reason_type' => LeaveReasonType::Sakit,
            'start_date' => '2026-07-21',
            'end_date' => '2026-07-22',
            'description' => 'Ananda sedang flu dan radang tenggorokan.',
            'status' => LeaveStatus::Pending,
        ]);

        $response = $this->actingAs($this->teacher)->put(route('teacher.leave.update', $leave), [
            'status' => 'approved',
            'review_notes' => 'Syafakallah ananda Bilal, semoga lekas sehat.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('wa_direct_url');

        $this->assertDatabaseHas('whatsapp_logs', [
            'student_id' => $this->student->id,
            'type' => 'leave',
            'recipient_phone' => '6281234567890',
        ]);
    }

    public function test_admin_can_view_whatsapp_hub_and_send_broadcast(): void
    {
        $resView = $this->actingAs($this->admin)->get(route('admin.whatsapp.index'));
        $resView->assertOk();
        $resView->assertSeeText('Notifikasi WhatsApp ke Wali Murid');
        $resView->assertSeeText('Status Integrasi');

        // Test sending single custom message
        $resPost = $this->actingAs($this->admin)->post(route('admin.whatsapp.send'), [
            'target_type' => 'single',
            'student_id' => $this->student->id,
            'message' => 'Pengumuman pengambilan rapor semester ganjil hari Sabtu pukul 08:00 WIB.',
        ]);

        $resPost->assertRedirect();
        $this->assertDatabaseHas('whatsapp_logs', [
            'student_id' => $this->student->id,
            'type' => 'general',
            'recipient_phone' => '6281234567890',
        ]);
    }

    public function test_admin_can_run_gateway_test(): void
    {
        $res = $this->actingAs($this->admin)->post(route('admin.whatsapp.test'), [
            'test_phone' => '081299887766',
        ]);

        $res->assertRedirect();
        $this->assertDatabaseHas('whatsapp_logs', [
            'type' => 'test',
            'recipient_phone' => '6281299887766',
        ]);
    }
}
