<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Enums\LeaveReasonType;
use App\Enums\LeaveStatus;
use App\Enums\StudentStatus;
use App\Enums\UserRole;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\LeaveRequest;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase2AttendanceAndLeaveTest extends TestCase
{
    use RefreshDatabase;

    protected User $teacher;
    protected User $parent;
    protected AcademicYear $academicYear;
    protected Classroom $classroom;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->academicYear = AcademicYear::create([
            'name' => '2026/2027',
            'start_date' => '2026-07-15',
            'end_date' => '2027-06-20',
            'is_active' => true,
        ]);

        $this->teacher = User::create([
            'name' => 'Ustadz Ahmad Al-Hafidz',
            'email' => 'ahmad@simad.id',
            'phone_number' => '081234567890',
            'role' => UserRole::Teacher,
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->parent = User::create([
            'name' => 'Bapak Hendra Pratama',
            'email' => 'hendra@simad.id',
            'phone_number' => '081234567891',
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
            'nis' => '262701001',
            'nisn' => '0123456789',
            'full_name' => 'Muhammad Bilal Al-Ghifari',
            'nickname' => 'Bilal',
            'gender' => 'L',
            'birth_place' => 'Jakarta',
            'birth_date' => '2019-05-12',
            'address' => 'Jl. Pesantren No. 12, Blitar',
            'parent_id' => $this->parent->id,
            'classroom_id' => $this->classroom->id,
            'status' => StudentStatus::Active,
        ]);
    }

    public function test_teacher_can_view_daily_attendance_page(): void
    {
        $response = $this->actingAs($this->teacher)->get(route('teacher.attendance.index'));

        $response->assertOk();
        $response->assertSee('Presensi Harian Santri');
        $response->assertSee('Kelas 1-A (Abu Bakar)');
        $response->assertSee('Muhammad Bilal Al-Ghifari');
    }

    public function test_teacher_can_record_attendance_for_students(): void
    {
        $payload = [
            'date' => now()->toDateString(),
            'attendances' => [
                [
                    'student_id' => $this->student->id,
                    'status' => 'hadir',
                    'notes' => 'Tepat waktu',
                ],
            ],
        ];

        $response = $this->actingAs($this->teacher)
            ->post(route('teacher.attendance.store'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'student_id' => $this->student->id,
            'classroom_id' => $this->classroom->id,
            'status' => 'hadir',
            'notes' => 'Tepat waktu',
        ]);
    }

    public function test_parent_can_view_attendance_recap_for_child(): void
    {
        Attendance::create([
            'student_id' => $this->student->id,
            'classroom_id' => $this->classroom->id,
            'date' => now()->toDateString(),
            'status' => AttendanceStatus::Hadir,
            'notes' => 'Hadir penuh',
            'recorded_by' => $this->teacher->id,
        ]);

        $response = $this->actingAs($this->parent)->get(route('parent.attendance.index'));

        $response->assertOk();
        $response->assertSeeText('Presensi & Kehadiran Santri');
        $response->assertSee('Muhammad Bilal Al-Ghifari');
        $response->assertSee('Hadir');
    }

    public function test_parent_can_submit_leave_request(): void
    {
        $payload = [
            'student_id' => $this->student->id,
            'reason_type' => 'sakit',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'description' => 'Demam tinggi dan istirahat dokter.',
        ];

        $response = $this->actingAs($this->parent)
            ->post(route('parent.leave.store'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('leave_requests', [
            'student_id' => $this->student->id,
            'parent_id' => $this->parent->id,
            'reason_type' => 'sakit',
            'status' => 'pending',
            'description' => 'Demam tinggi dan istirahat dokter.',
        ]);
    }

    public function test_teacher_can_approve_leave_request_and_auto_sync_attendances(): void
    {
        $startDate = now()->toDateString();
        $endDate = now()->addDays(1)->toDateString();

        $leaveRequest = LeaveRequest::create([
            'student_id' => $this->student->id,
            'parent_id' => $this->parent->id,
            'reason_type' => LeaveReasonType::Sakit,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => LeaveStatus::Pending,
            'description' => 'Sakit flu berat',
        ]);

        $response = $this->actingAs($this->teacher)
            ->put(route('teacher.leave.update', $leaveRequest), [
                'status' => 'approved',
                'review_notes' => 'Semoga lekas sembuh, santri diizinkan istirahat.',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('leave_requests', [
            'id' => $leaveRequest->id,
            'status' => 'approved',
            'reviewed_by' => $this->teacher->id,
        ]);

        // Auto-synchronization should create attendance records for each day of the leave
        $this->assertDatabaseHas('attendances', [
            'student_id' => $this->student->id,
            'classroom_id' => $this->classroom->id,
            'status' => 'sakit',
        ]);
    }

    public function test_parent_cannot_submit_leave_for_student_they_do_not_own(): void
    {
        $otherParent = User::create([
            'name' => 'Wali Santri Lain',
            'email' => 'other@simad.id',
            'phone_number' => '081234567899',
            'role' => UserRole::Parent,
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $payload = [
            'student_id' => $this->student->id,
            'reason_type' => 'izin',
            'start_date' => now()->toDateString(),
            'end_date' => now()->toDateString(),
            'description' => 'Mencoba izin anak orang lain',
        ];

        $response = $this->actingAs($otherParent)
            ->post(route('parent.leave.store'), $payload);

        $response->assertNotFound();
    }
}
