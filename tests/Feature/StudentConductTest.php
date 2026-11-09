<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Semester;
use App\Enums\UserRole;
use App\Models\AcademicYear;
use App\Models\CharacterRule;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentConductRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentConductTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $teacher;
    private User $parent;
    private Student $student;
    private Classroom $classroom;
    private AcademicYear $academicYear;

    protected function setUp(): void
    {
        parent::setUp();

        $this->academicYear = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => Semester::Ganjil,
            'start_date' => '2026-07-15',
            'end_date' => '2027-06-20',
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'name' => 'K.H. Nur Hadi',
            'email' => 'admin@simad.id',
            'phone_number' => '081200000001',
            'role' => UserRole::Admin,
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->teacher = User::create([
            'name' => 'Ustadzah Fatimah, S.Pd.',
            'email' => 'guru@simad.id',
            'phone_number' => '081200000002',
            'role' => UserRole::Teacher,
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->parent = User::create([
            'name' => 'Bapak Ahmad Dahlan',
            'email' => 'wali@simad.id',
            'phone_number' => '081200000003',
            'role' => UserRole::Parent,
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->classroom = Classroom::create([
            'academic_year_id' => $this->academicYear->id,
            'homeroom_teacher_id' => $this->teacher->id,
            'name' => '1-A',
            'grade_level' => 1,
        ]);

        $this->student = Student::create([
            'parent_id' => $this->parent->id,
            'classroom_id' => $this->classroom->id,
            'nis' => '2026001',
            'nisn' => '0123456789',
            'full_name' => 'Muhammad Rayhan Al-Fatih',
            'nickname' => 'Rayhan',
            'gender' => 'L',
            'birth_place' => 'Blitar',
            'birth_date' => '2019-05-10',
            'address' => 'Jl. Mawar No. 12, Blitar',
            'status' => 'active',
        ]);
    }

    public function test_admin_can_view_conduct_dashboard_and_manage_rules(): void
    {
        $this->actingAs($this->admin);

        // 1. View Index
        $res = $this->get(route('admin.conduct.index'));
        $res->assertOk();
        $res->assertSeeText('Buku Disiplin & Karakter Siswa');

        // 2. Add New Character Rule
        $resStoreRule = $this->post(route('admin.conduct.store_rule'), [
            'name' => 'Menolong teman yang terjatuh',
            'type' => 'apresiasi',
            'category' => 'akhlak_adab',
            'points' => 10,
            'description' => 'Membantu sesama santri',
            'is_active' => 1,
        ]);
        $resStoreRule->assertRedirect(route('admin.conduct.index', ['tab' => 'rules']));

        $this->assertDatabaseHas('character_rules', [
            'name' => 'Menolong teman yang terjatuh',
            'points' => 10,
        ]);

        $rule = CharacterRule::firstWhere('name', 'Menolong teman yang terjatuh');

        // 3. Admin Logs Conduct Record
        $resStoreRecord = $this->post(route('admin.conduct.store_record'), [
            'student_id' => $this->student->id,
            'character_rule_id' => $rule->id,
            'type' => 'apresiasi',
            'points' => 10,
            'title' => 'Menolong teman yang terjatuh di halaman',
            'incident_date' => now()->toDateString(),
            'follow_up_action' => 'Diberikan bintang akhlak terpuji',
        ]);
        $resStoreRecord->assertRedirect(route('admin.conduct.index', ['tab' => 'records']));

        $this->assertDatabaseHas('student_conduct_records', [
            'student_id' => $this->student->id,
            'title' => 'Menolong teman yang terjatuh di halaman',
            'points' => 10,
        ]);

        $record = StudentConductRecord::first();

        // 4. Admin Deletes Conduct Record
        $resDelRec = $this->delete(route('admin.conduct.destroy_record', $record));
        $resDelRec->assertRedirect(route('admin.conduct.index', ['tab' => 'records']));
        $this->assertDatabaseMissing('student_conduct_records', ['id' => $record->id]);

        // 5. Admin Deletes Rule
        $resDelRule = $this->delete(route('admin.conduct.destroy_rule', $rule));
        $resDelRule->assertRedirect(route('admin.conduct.index', ['tab' => 'rules']));
        $this->assertDatabaseMissing('character_rules', ['id' => $rule->id]);
    }

    public function test_teacher_can_award_points_and_record_discipline(): void
    {
        $this->actingAs($this->teacher);

        // 1. View Teacher Conduct Page
        $res = $this->get(route('teacher.conduct.index', ['classroom_id' => $this->classroom->id]));
        $res->assertOk();
        $res->assertSeeText('Disiplin & Karakter Siswa');
        $res->assertSeeText($this->student->full_name);

        // 2. Teacher Awards Positive Points
        $resPositive = $this->post(route('teacher.conduct.store'), [
            'student_id' => $this->student->id,
            'type' => 'apresiasi',
            'points' => 15,
            'title' => 'Adzan Dzuhur dengan merdu dan tartil',
            'incident_date' => now()->toDateString(),
            'follow_up_action' => 'Pujian di depan seluruh jamaah',
        ]);
        $resPositive->assertRedirect();

        $this->assertDatabaseHas('student_conduct_records', [
            'student_id' => $this->student->id,
            'type' => 'apresiasi',
            'points' => 15,
            'recorded_by' => $this->teacher->id,
        ]);

        // 3. Teacher Records Educational Discipline Violation
        $resNegative = $this->post(route('teacher.conduct.store'), [
            'student_id' => $this->student->id,
            'type' => 'pelanggaran',
            'points' => 5,
            'title' => 'Lupa membawa buku iqro',
            'incident_date' => now()->toDateString(),
            'follow_up_action' => 'Dinasehati dengan ramah dan meminjam buku perpustakaan',
        ]);
        $resNegative->assertRedirect();

        $this->assertDatabaseHas('student_conduct_records', [
            'student_id' => $this->student->id,
            'type' => 'pelanggaran',
            'points' => 5,
        ]);

        // 4. Verify Total Score Calculation: Base 100 + 15 - 5 = 110
        $this->assertEquals(110, $this->student->fresh()->calculateTotalConductScore());
    }

    public function test_parent_can_view_child_conduct_report(): void
    {
        $this->actingAs($this->parent);

        // Add 1 appreciation (+20) and 1 violation (-5)
        StudentConductRecord::create([
            'student_id' => $this->student->id,
            'recorded_by' => $this->teacher->id,
            'type' => 'apresiasi',
            'points' => 20,
            'title' => 'Juara 1 Lomba Kaligrafi Santri',
            'incident_date' => now()->toDateString(),
            'follow_up_action' => 'Penyerahan piala saat apel Senin',
        ]);

        StudentConductRecord::create([
            'student_id' => $this->student->id,
            'recorded_by' => $this->teacher->id,
            'type' => 'pelanggaran',
            'points' => 5,
            'title' => 'Seragam olahraga lupa dibawa',
            'incident_date' => now()->toDateString(),
            'follow_up_action' => 'Diberikan seragam cadangan madrasah',
        ]);

        $res = $this->get(route('parent.conduct.index', ['student_id' => $this->student->id]));
        $res->assertOk();
        $res->assertSeeText('Raport Karakter & Disiplin Ananda');
        // Total score 100 + 20 - 5 = 115 (Mumtaz / Teladan)
        $res->assertSeeText('115');
        $res->assertSeeText('Juara 1 Lomba Kaligrafi Santri');
        $res->assertSeeText('+20');
        $res->assertSeeText('-5');
    }
}
