<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Semester;
use App\Enums\StudentStatus;
use App\Enums\UserRole;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\CurriculumStructure;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurriculumTest extends TestCase
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
            'name' => 'Ustadz Fatih, S.Pd.I',
            'email' => 'guru@simad.id',
            'phone_number' => '081200000002',
            'role' => UserRole::Teacher,
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->parent = User::create([
            'name' => 'Bapak Irfan Hakim',
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
            'capacity' => 28,
        ]);

        $this->student = Student::create([
            'nisn' => '0011223344',
            'nis' => '202601001',
            'full_name' => 'Muhammad Rayyan Al-Fatih',
            'gender' => 'L',
            'birth_place' => 'Surabaya',
            'birth_date' => '2019-05-12',
            'address' => 'Jl. Ketintang Baru No. 12, Surabaya',
            'classroom_id' => $this->classroom->id,
            'parent_id' => $this->parent->id,
            'status' => StudentStatus::Active,
        ]);
    }

    public function test_public_can_view_curriculum_showcase(): void
    {
        CurriculumStructure::create([
            'name' => 'Tahfidzul Qur\'an Juz 30',
            'code' => 'MLK-TF-A',
            'phase' => 'fase_a',
            'curriculum_type' => 'muatan_lokal_pesantren',
            'hours_per_week' => 4,
            'competency_goals' => 'Menuntaskan hafalan Juz 30 mutqin dengan talaqqi ustadz bersanad.',
            'is_active' => true,
            'display_order' => 1,
        ]);

        $response = $this->get('/kurikulum');
        $response->assertOk();
        $response->assertSee('Tahfidzul Qur\'an Juz 30');
        $response->assertSee('Kurikulum Terpadu Madrasah');
        $response->assertSee('Fase A (Kelas 1 - 2)');
    }

    public function test_public_can_filter_curriculum_by_phase(): void
    {
        CurriculumStructure::create([
            'name' => 'Nahwu Shorof Ibtidai',
            'code' => 'MLK-NS-C',
            'phase' => 'fase_c',
            'curriculum_type' => 'muatan_lokal_pesantren',
            'hours_per_week' => 2,
            'competency_goals' => 'Kaidah dasar bahasa arab kitab kuning.',
            'is_active' => true,
            'display_order' => 1,
        ]);

        $response = $this->get('/kurikulum?phase=fase_c');
        $response->assertOk();
        $response->assertSee('Nahwu Shorof Ibtidai');
    }

    public function test_admin_can_access_curriculum_management(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/kurikulum');
        $response->assertOk();
        $response->assertSee('Kelola Struktur Kurikulum Madrasah');
    }

    public function test_admin_can_store_new_curriculum_structure(): void
    {
        $payload = [
            'name' => 'Fikih Ibadah Amaliah',
            'code' => 'PAI-FQ-A',
            'phase' => 'fase_a',
            'curriculum_type' => 'kemenag_pai',
            'hours_per_week' => 2,
            'competency_goals' => 'Tata cara wudhu dan shalat fardhu secara benar.',
            'syllabus_summary' => 'Thaharah, wudhu, rukun dan syarat shalat.',
            'display_order' => 2,
            'is_active' => '1',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/kurikulum', $payload);
        $response->assertRedirect('/admin/kurikulum?phase=fase_a');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('curriculum_structures', [
            'name' => 'Fikih Ibadah Amaliah',
            'code' => 'PAI-FQ-A',
            'phase' => 'fase_a',
            'curriculum_type' => 'kemenag_pai',
            'hours_per_week' => 2,
        ]);
    }

    public function test_admin_can_update_curriculum_structure(): void
    {
        $structure = CurriculumStructure::create([
            'name' => 'Bahasa Arab Lama',
            'code' => 'ARB-01',
            'phase' => 'fase_b',
            'curriculum_type' => 'kemenag_pai',
            'hours_per_week' => 2,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/kurikulum/{$structure->id}", [
            'name' => 'Bahasa Arab Terapan Modern',
            'code' => 'ARB-REV',
            'phase' => 'fase_b',
            'curriculum_type' => 'kemenag_pai',
            'hours_per_week' => 3,
            'competency_goals' => 'Mampu menyusun kalimat tanya dan bercerita pendek.',
            'is_active' => '1',
            'display_order' => 1,
        ]);

        $response->assertRedirect('/admin/kurikulum?phase=fase_b');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('curriculum_structures', [
            'id' => $structure->id,
            'name' => 'Bahasa Arab Terapan Modern',
            'code' => 'ARB-REV',
            'hours_per_week' => 3,
        ]);
    }

    public function test_admin_can_delete_curriculum_structure(): void
    {
        $structure = CurriculumStructure::create([
            'name' => 'Mata Pelajaran Uji Coba',
            'phase' => 'fase_a',
            'curriculum_type' => 'muatan_lokal_pesantren',
            'hours_per_week' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/kurikulum/{$structure->id}");
        $response->assertRedirect('/admin/kurikulum?phase=fase_a');
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('curriculum_structures', [
            'id' => $structure->id,
        ]);
    }

    public function test_teacher_can_view_curriculum_guide_and_cp(): void
    {
        CurriculumStructure::create([
            'name' => 'Matematika Realistik Fase A',
            'code' => 'MAT-A',
            'phase' => 'fase_a',
            'curriculum_type' => 'nasional_merdeka',
            'hours_per_week' => 5,
            'competency_goals' => 'Membilang cacah 1-100 dan geometri dasar.',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->teacher)->get('/guru/kurikulum');
        $response->assertOk();
        $response->assertSee('Panduan Kurikulum & Capaian Pembelajaran');
        $response->assertSee('Matematika Realistik Fase A');
    }

    public function test_parent_can_view_child_curriculum_and_phase(): void
    {
        CurriculumStructure::create([
            'name' => 'Akidah Akhlak Santri',
            'code' => 'PAI-AA-A',
            'phase' => 'fase_a',
            'curriculum_type' => 'kemenag_pai',
            'hours_per_week' => 2,
            'competency_goals' => 'Mengenal kalimat thoyyibah dan adab harian.',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->parent)->get('/wali/kurikulum');
        $response->assertOk();
        $response->assertSee('Kurikulum & Capaian Belajar Ananda');
        $response->assertSee('Fase A (Kelas 1-2)');
        $response->assertSee('Akidah Akhlak Santri');
    }

    public function test_unauthenticated_user_cannot_access_portal_curriculums(): void
    {
        $this->get('/admin/kurikulum')->assertRedirect('/login');
        $this->get('/guru/kurikulum')->assertRedirect('/login');
        $this->get('/wali/kurikulum')->assertRedirect('/login');
    }
}
