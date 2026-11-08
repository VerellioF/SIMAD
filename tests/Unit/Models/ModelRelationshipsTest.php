<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Enums\UserRole;
use App\Models\AcademicGrade;
use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\Article;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\LeaveRequest;
use App\Models\PpdbRegistration;
use App\Models\SchoolGallery;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TahfidzRecord;
use App\Models\TuitionBill;
use App\Models\TuitionPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_relationships_and_role_methods(): void
    {
        $parent = User::create([
            'name' => 'Bapak Ahmad',
            'phone_number' => '081234567891',
            'password' => bcrypt('password'),
            'role' => UserRole::Parent,
        ]);

        $teacher = User::create([
            'name' => 'Ustadzah Fatimah',
            'phone_number' => '081234567892',
            'password' => bcrypt('password'),
            'role' => UserRole::Teacher,
        ]);

        $this->assertTrue($parent->isParent());
        $this->assertTrue($teacher->isTeacher());

        $year = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        $classroom = Classroom::create([
            'academic_year_id' => $year->id,
            'homeroom_teacher_id' => $teacher->id,
            'name' => 'Kelas 1-A',
            'grade_level' => 1,
        ]);

        $student = Student::create([
            'parent_id' => $parent->id,
            'classroom_id' => $classroom->id,
            'nis' => '1001',
            'nisn' => '0012345678',
            'full_name' => 'Muhammad Ali',
            'gender' => 'L',
            'birth_place' => 'Blitar',
            'birth_date' => '2019-05-10',
            'address' => 'Desa Kalipucung',
        ]);

        $this->assertEquals('Bapak Ahmad', $student->parent->name);
        $this->assertCount(1, $parent->students);
        $this->assertEquals('Kelas 1-A', $student->classroom->name);
        $this->assertEquals('Ustadzah Fatimah', $classroom->homeroomTeacher->name);
    }

    public function test_attendance_and_tahfidz_relationships(): void
    {
        $parent = User::create([
            'name' => 'Wali Santri',
            'phone_number' => '0811111111',
            'password' => bcrypt('password'),
            'role' => UserRole::Parent,
        ]);

        $teacher = User::create([
            'name' => 'Ustadz Pembina',
            'phone_number' => '0822222222',
            'password' => bcrypt('password'),
            'role' => UserRole::Teacher,
        ]);

        $year = AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        $classroom = Classroom::create(['academic_year_id' => $year->id, 'name' => '1-A', 'grade_level' => 1]);
        $student = Student::create([
            'parent_id' => $parent->id,
            'classroom_id' => $classroom->id,
            'full_name' => 'Santri Satu',
            'gender' => 'L',
            'birth_place' => 'Blitar',
            'birth_date' => '2019-01-01',
            'address' => 'Blitar',
        ]);

        $attendance = Attendance::create([
            'student_id' => $student->id,
            'classroom_id' => $classroom->id,
            'recorded_by' => $teacher->id,
            'date' => '2026-11-08',
            'status' => 'hadir',
        ]);

        $tahfidz = TahfidzRecord::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'date' => '2026-11-08',
            'surah_name' => 'An-Naba',
            'verse_range' => 'Ayat 1-10',
            'fluency_grade' => 'lancar',
            'tajweed_grade' => 'mumtaz',
        ]);

        $this->assertEquals($student->id, $attendance->student->id);
        $this->assertEquals($teacher->id, $attendance->recorder->id);
        $this->assertEquals('An-Naba', $tahfidz->surah_name);
        $this->assertEquals($student->id, $tahfidz->student->id);
    }

    public function test_tuition_bill_and_payment_relationships(): void
    {
        $parent = User::create([
            'name' => 'Wali Dono',
            'phone_number' => '0833333333',
            'password' => bcrypt('password'),
            'role' => UserRole::Parent,
        ]);

        $admin = User::create([
            'name' => 'Admin Bendahara',
            'phone_number' => '0844444444',
            'password' => bcrypt('password'),
            'role' => UserRole::Admin,
        ]);

        $year = AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);
        $student = Student::create([
            'parent_id' => $parent->id,
            'full_name' => 'Santri Dua',
            'gender' => 'P',
            'birth_place' => 'Blitar',
            'birth_date' => '2019-02-02',
            'address' => 'Blitar',
        ]);

        $bill = TuitionBill::create([
            'student_id' => $student->id,
            'academic_year_id' => $year->id,
            'bill_title' => 'SPP November 2026',
            'month' => 11,
            'year' => 2026,
            'amount' => 150000.00,
            'status' => 'pending_verification',
            'due_date' => '2026-11-10',
        ]);

        $payment = TuitionPayment::create([
            'tuition_bill_id' => $bill->id,
            'verified_by' => $admin->id,
            'receipt_number' => 'KW-2026-11-001',
            'amount_paid' => 150000.00,
            'payment_method' => 'transfer_bank',
            'payment_date' => '2026-11-08',
        ]);

        $this->assertEquals('KW-2026-11-001', $bill->payments->first()->receipt_number);
        $this->assertEquals('Admin Bendahara', $payment->verifier->name);
    }
}
