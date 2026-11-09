<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\AnnouncementTarget;
use App\Enums\GalleryCategory;
use App\Enums\StudentStatus;
use App\Enums\UserRole;
use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\Article;
use App\Models\Classroom;
use App\Models\SchoolGallery;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Phase4MasterDataAndCmsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $teacher;
    protected User $parent;
    protected AcademicYear $academicYear;
    protected Classroom $classroom;

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
            'name' => 'Bapak Ahmad Dahlan',
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
        ]);
    }

    public function test_admin_can_manage_students_crud(): void
    {
        // 1. View Index
        $response = $this->actingAs($this->admin)->get(route('admin.students.index'));
        $response->assertOk();
        $response->assertSeeText('Master Data Siswa');

        // 2. Store Student
        $payload = [
            'full_name' => 'Muhammad Fatih Robbani',
            'nickname' => 'Fatih',
            'gender' => 'L',
            'nis' => '262701099',
            'nisn' => '0098765432',
            'birth_place' => 'Blitar',
            'birth_date' => '2019-06-10',
            'classroom_id' => $this->classroom->id,
            'parent_id' => $this->parent->id,
            'address' => 'Jl. Anggrek No. 20, Blitar',
            'status' => 'active',
        ];

        $storeResponse = $this->actingAs($this->admin)->post(route('admin.students.store'), $payload);
        $storeResponse->assertRedirect(route('admin.students.index'));
        $storeResponse->assertSessionHas('success');

        $this->assertDatabaseHas('students', [
            'nis' => '262701099',
            'full_name' => 'Muhammad Fatih Robbani',
        ]);

        $student = Student::where('nis', '262701099')->firstOrFail();

        // 3. Update Student
        $updatePayload = array_merge($payload, [
            'full_name' => 'Muhammad Fatih Robbani Updated',
        ]);

        $updateResponse = $this->actingAs($this->admin)
            ->put(route('admin.students.update', $student), $updatePayload);
        $updateResponse->assertRedirect(route('admin.students.index'));

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'full_name' => 'Muhammad Fatih Robbani Updated',
        ]);

        // 4. Delete Student
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.students.destroy', $student));
        $deleteResponse->assertRedirect(route('admin.students.index'));
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    public function test_admin_can_manage_teachers_crud(): void
    {
        // 1. View Index
        $response = $this->actingAs($this->admin)->get(route('admin.teachers.index'));
        $response->assertOk();
        $response->assertSeeText('Data Dewan Guru & Tenaga Pendidik');

        // 2. Store Teacher
        $payload = [
            'name' => 'Ustadz Ali Ridho, S.Pd.I.',
            'email' => 'ali@simad.id',
            'phone_number' => '081299998888',
            'password' => 'secret123',
            'homeroom_classroom_id' => $this->classroom->id,
        ];

        $storeResponse = $this->actingAs($this->admin)->post(route('admin.teachers.store'), $payload);
        $storeResponse->assertRedirect(route('admin.teachers.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'ali@simad.id',
            'role' => 'teacher',
        ]);

        $newTeacher = User::where('email', 'ali@simad.id')->firstOrFail();

        // 3. Update Teacher
        $updatePayload = [
            'name' => 'Ustadz Ali Ridho, M.Pd.',
            'email' => 'ali@simad.id',
            'phone_number' => '081299997777',
            'is_active' => 1,
            'homeroom_classroom_id' => $this->classroom->id,
        ];

        $updateResponse = $this->actingAs($this->admin)->put(route('admin.teachers.update', $newTeacher), $updatePayload);
        $updateResponse->assertRedirect(route('admin.teachers.index'));

        $this->assertDatabaseHas('users', [
            'id' => $newTeacher->id,
            'name' => 'Ustadz Ali Ridho, M.Pd.',
        ]);

        // 4. Delete Teacher
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.teachers.destroy', $newTeacher));
        $deleteResponse->assertRedirect(route('admin.teachers.index'));
        $this->assertDatabaseMissing('users', ['id' => $newTeacher->id]);
    }

    public function test_admin_can_manage_classrooms_crud(): void
    {
        // 1. View Index
        $response = $this->actingAs($this->admin)->get(route('admin.classrooms.index'));
        $response->assertOk();
        $response->assertSeeText('Rombongan Belajar & Kelas');

        // 2. Store Classroom
        $payload = [
            'name' => 'Kelas 2-A (Utsman bin Affan)',
            'grade_level' => 2,
            'homeroom_teacher_id' => $this->teacher->id,
        ];

        $storeResponse = $this->actingAs($this->admin)->post(route('admin.classrooms.store'), $payload);
        $storeResponse->assertRedirect(route('admin.classrooms.index'));

        $this->assertDatabaseHas('classrooms', [
            'name' => 'Kelas 2-A (Utsman bin Affan)',
            'grade_level' => 2,
        ]);

        $newClass = Classroom::where('name', 'Kelas 2-A (Utsman bin Affan)')->firstOrFail();

        // 3. Update Classroom
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.classrooms.update', $newClass), [
            'name' => 'Kelas 2-A (Utsman Affan Updated)',
            'grade_level' => 2,
            'homeroom_teacher_id' => null,
        ]);
        $updateResponse->assertRedirect(route('admin.classrooms.index'));

        $this->assertDatabaseHas('classrooms', [
            'id' => $newClass->id,
            'name' => 'Kelas 2-A (Utsman Affan Updated)',
        ]);

        // 4. Delete Classroom (empty room)
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.classrooms.destroy', $newClass));
        $deleteResponse->assertRedirect(route('admin.classrooms.index'));
        $this->assertDatabaseMissing('classrooms', ['id' => $newClass->id]);
    }

    public function test_admin_can_manage_articles_crud(): void
    {
        // 1. View Index
        $response = $this->actingAs($this->admin)->get(route('admin.articles.index'));
        $response->assertOk();
        $response->assertSeeText('Warta & Berita Madrasah');

        // 2. Store Article
        $payload = [
            'title' => 'Peringatan Hari Santri Nasional 2026',
            'content' => 'Seluruh asatidz dan santri melaksanakan apel akbar mengenakan sarung dan peci hitam.',
            'is_published' => 1,
            'published_at' => now()->toDateString(),
        ];

        $storeResponse = $this->actingAs($this->admin)->post(route('admin.articles.store'), $payload);
        $storeResponse->assertRedirect(route('admin.articles.index'));

        $this->assertDatabaseHas('articles', [
            'title' => 'Peringatan Hari Santri Nasional 2026',
            'is_published' => true,
        ]);

        $article = Article::where('title', 'Peringatan Hari Santri Nasional 2026')->firstOrFail();

        // 3. Update Article
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.articles.update', $article), [
            'title' => 'Peringatan Hari Santri Nasional 2026 (Updated)',
            'content' => 'Konten revisi warta berita madrasah.',
            'is_published' => 1,
        ]);
        $updateResponse->assertRedirect(route('admin.articles.index'));

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'Peringatan Hari Santri Nasional 2026 (Updated)',
        ]);

        // 4. Delete Article
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.articles.destroy', $article));
        $deleteResponse->assertRedirect(route('admin.articles.index'));
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    public function test_admin_can_manage_announcements_crud(): void
    {
        // 1. View Index
        $response = $this->actingAs($this->admin)->get(route('admin.announcements.index'));
        $response->assertOk();
        $response->assertSeeText('Pengumuman & Surat Edaran');

        // 2. Store Announcement
        $payload = [
            'title' => 'Pengumuman Libur Hari Raya Idul Adha 1447 H',
            'content' => 'Kegiatan belajar mengajar diliburkan selama 3 hari.',
            'target_audience' => 'all',
            'valid_until' => now()->addDays(7)->toDateString(),
        ];

        $storeResponse = $this->actingAs($this->admin)->post(route('admin.announcements.store'), $payload);
        $storeResponse->assertRedirect(route('admin.announcements.index'));

        $this->assertDatabaseHas('announcements', [
            'title' => 'Pengumuman Libur Hari Raya Idul Adha 1447 H',
            'target_audience' => 'all',
        ]);

        $announcement = Announcement::where('title', 'Pengumuman Libur Hari Raya Idul Adha 1447 H')->firstOrFail();

        // 3. Update Announcement
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.announcements.update', $announcement), [
            'title' => 'Pengumuman Libur Hari Raya Idul Adha (Revisi)',
            'content' => 'Libur KBM diperpanjang 1 hari.',
            'target_audience' => 'parent',
            'valid_until' => now()->addDays(10)->toDateString(),
        ]);
        $updateResponse->assertRedirect(route('admin.announcements.index'));

        $this->assertDatabaseHas('announcements', [
            'id' => $announcement->id,
            'title' => 'Pengumuman Libur Hari Raya Idul Adha (Revisi)',
            'target_audience' => 'parent',
        ]);

        // 4. Delete Announcement
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.announcements.destroy', $announcement));
        $deleteResponse->assertRedirect(route('admin.announcements.index'));
        $this->assertDatabaseMissing('announcements', ['id' => $announcement->id]);
    }

    public function test_admin_can_manage_galleries_crud(): void
    {
        // 1. View Index
        $response = $this->actingAs($this->admin)->get(route('admin.galleries.index'));
        $response->assertOk();
        $response->assertSeeText('Galeri Foto & Dokumentasi');

        // 2. Upload Gallery
        $file = UploadedFile::fake()->create('drumband.jpg', 200, 'image/jpeg');

        $storeResponse = $this->actingAs($this->admin)->post(route('admin.galleries.store'), [
            'title' => 'Lomba Drumband Tingkat Karesidenan',
            'category' => 'seni_pentas',
            'image' => $file,
        ]);
        $storeResponse->assertRedirect(route('admin.galleries.index'));

        $this->assertDatabaseHas('school_galleries', [
            'title' => 'Lomba Drumband Tingkat Karesidenan',
            'category' => 'seni_pentas',
        ]);

        $gallery = SchoolGallery::where('title', 'Lomba Drumband Tingkat Karesidenan')->firstOrFail();

        // 3. Delete Gallery
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.galleries.destroy', $gallery));
        $deleteResponse->assertRedirect(route('admin.galleries.index'));
        $this->assertDatabaseMissing('school_galleries', ['id' => $gallery->id]);
    }

    public function test_non_admin_cannot_access_phase4_routes(): void
    {
        // Teacher blocked
        $this->actingAs($this->teacher)->get(route('admin.students.index'))->assertForbidden();
        $this->actingAs($this->teacher)->get(route('admin.teachers.index'))->assertForbidden();
        $this->actingAs($this->teacher)->get(route('admin.classrooms.index'))->assertForbidden();
        $this->actingAs($this->teacher)->get(route('admin.articles.index'))->assertForbidden();

        // Parent blocked
        $this->actingAs($this->parent)->get(route('admin.students.index'))->assertForbidden();
        $this->actingAs($this->parent)->get(route('admin.teachers.index'))->assertForbidden();
        $this->actingAs($this->parent)->get(route('admin.classrooms.index'))->assertForbidden();
        $this->actingAs($this->parent)->get(route('admin.articles.index'))->assertForbidden();
    }
}
