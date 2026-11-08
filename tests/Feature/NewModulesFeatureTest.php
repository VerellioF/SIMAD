<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Semester;
use App\Enums\UserRole;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\ClassSchedule;
use App\Models\DailyWorshipRecord;
use App\Models\Extracurricular;
use App\Models\ExtracurricularEnrollment;
use App\Models\SchoolEvent;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewModulesFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $teacher;
    private User $parent;
    private Student $student;
    private Classroom $classroom;
    private AcademicYear $academicYear;
    private Subject $subject;

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
            'name' => 'K.H. Nur Hadi, M.Pd.I.',
            'email' => 'admin@simad.id',
            'phone_number' => '081200000001',
            'role' => UserRole::Admin,
            'password' => bcrypt('password'),
            'position' => 'Pimpinan Pesantren',
            'education' => 'S2 UIN Malang',
            'bio' => 'Pengasuh Pesantren',
            'is_active' => true,
        ]);

        $this->teacher = User::create([
            'name' => 'Ustadzah Fatimah, S.Pd.',
            'email' => 'guru@simad.id',
            'phone_number' => '081200000002',
            'role' => UserRole::Teacher,
            'password' => bcrypt('password'),
            'position' => 'Wali Kelas 1-A',
            'education' => 'S1 PGMI UM',
            'bio' => 'Pendidik aktif',
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

        $this->subject = Subject::create([
            'code' => 'PAI-1',
            'name' => 'Pendidikan Agama Islam',
            'category' => 'agama',
            'is_active' => true,
        ]);
    }

    public function test_public_pages_render_successfully(): void
    {
        // 1. Profil Guru & Asatidz
        $resGuru = $this->get('/tentang/guru');
        $resGuru->assertOk();
        $resGuru->assertSeeText('Dewan Asatidz & Pimpinan');
        $resGuru->assertSeeText($this->teacher->name);

        // 2. Kalender Akademik
        SchoolEvent::create([
            'academic_year_id' => $this->academicYear->id,
            'title' => 'Asesmen Tengah Semester Ganjil',
            'event_type' => 'ujian',
            'start_date' => '2026-09-20',
            'end_date' => '2026-09-25',
            'is_holiday' => false,
        ]);

        $resCal = $this->get('/kalender');
        $resCal->assertOk();
        $resCal->assertSeeText('Kalender Akademik & Agenda');
        $resCal->assertSeeText('Asesmen Tengah Semester Ganjil');

        // 3. Ekstrakurikuler
        Extracurricular::create([
            'name' => 'Seni Hadrah Banjari',
            'category' => 'seni_budaya',
            'instructor_name' => 'Ustadz Ridwan',
            'schedule_day' => 'Kamis',
            'schedule_time' => '15:30 - 17:00',
            'is_active' => true,
        ]);

        $resEks = $this->get('/ekstrakurikuler');
        $resEks->assertOk();
        $resEks->assertSeeText('Ekstrakurikuler Unggulan');
        $resEks->assertSeeText('Seni Hadrah Banjari');
    }

    public function test_admin_can_manage_agenda_events(): void
    {
        $this->actingAs($this->admin);

        // 1. Create Event
        $res = $this->post(route('admin.agenda.store'), [
            'academic_year_id' => $this->academicYear->id,
            'title' => 'Hari Libur Isra Mi\'raj',
            'event_type' => 'libur',
            'start_date' => '2027-01-27',
            'end_date' => '2027-01-27',
            'is_holiday' => 1,
            'description' => 'Libur resmi kalender madrasah',
        ]);
        $res->assertRedirect(route('admin.agenda.index'));

        $this->assertDatabaseHas('school_events', [
            'title' => 'Hari Libur Isra Mi\'raj',
            'is_holiday' => 1,
        ]);

        $event = SchoolEvent::firstWhere('title', 'Hari Libur Isra Mi\'raj');

        // 2. Update Event
        $resUpdate = $this->put(route('admin.agenda.update', $event), [
            'academic_year_id' => $this->academicYear->id,
            'title' => 'Hari Libur Isra Mi\'raj 1448 H',
            'event_type' => 'phbi',
            'start_date' => '2027-01-27',
            'end_date' => '2027-01-27',
            'is_holiday' => 1,
        ]);
        $resUpdate->assertRedirect(route('admin.agenda.index'));
        $this->assertDatabaseHas('school_events', ['title' => 'Hari Libur Isra Mi\'raj 1448 H']);

        // 3. Delete Event
        $resDel = $this->delete(route('admin.agenda.destroy', $event));
        $resDel->assertRedirect(route('admin.agenda.index'));
        $this->assertDatabaseMissing('school_events', ['id' => $event->id]);
    }

    public function test_admin_can_manage_class_schedules(): void
    {
        $this->actingAs($this->admin);

        // 1. Create Schedule Slot
        $res = $this->post(route('admin.schedules.store'), [
            'classroom_id' => $this->classroom->id,
            'academic_year_id' => $this->academicYear->id,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'day_of_week' => 'senin',
            'start_time' => '07:30',
            'end_time' => '08:45',
            'period_order' => 1,
        ]);
        $res->assertRedirect(route('admin.schedules.index', ['classroom_id' => $this->classroom->id]));

        $this->assertDatabaseHas('class_schedules', [
            'classroom_id' => $this->classroom->id,
            'day_of_week' => 'senin',
            'period_order' => 1,
        ]);

        $schedule = ClassSchedule::first();

        // 2. Delete Schedule Slot
        $resDel = $this->delete(route('admin.schedules.destroy', $schedule));
        $resDel->assertRedirect(route('admin.schedules.index', ['classroom_id' => $this->classroom->id]));
        $this->assertDatabaseMissing('class_schedules', ['id' => $schedule->id]);
    }

    public function test_admin_can_manage_extracurriculars_and_enrollments(): void
    {
        $this->actingAs($this->admin);

        // 1. Create Extracurricular
        $res = $this->post(route('admin.ekskul.store'), [
            'name' => 'Pramuka Gudep MI Plus',
            'category' => 'kepanduan',
            'instructor_name' => 'Kak Hendra',
            'schedule_day' => 'Jumat',
            'schedule_time' => '14:00 - 16:00',
            'location' => 'Lapangan Madrasah',
            'is_active' => 1,
        ]);
        $res->assertRedirect(route('admin.ekskul.index'));
        $this->assertDatabaseHas('extracurriculars', ['name' => 'Pramuka Gudep MI Plus']);

        $ekskul = Extracurricular::firstWhere('name', 'Pramuka Gudep MI Plus');

        // 2. Enroll student via admin
        $resEnroll = $this->post(route('admin.ekskul.enroll'), [
            'extracurricular_id' => $ekskul->id,
            'student_id' => $this->student->id,
            'academic_year_id' => $this->academicYear->id,
        ]);
        $resEnroll->assertRedirect(route('admin.ekskul.index', ['ekskul_id' => $ekskul->id]));

        $this->assertDatabaseHas('extracurricular_enrollments', [
            'student_id' => $this->student->id,
            'extracurricular_id' => $ekskul->id,
            'status' => 'active',
        ]);

        $enrollment = ExtracurricularEnrollment::first();

        // 3. Remove student
        $resUnenroll = $this->delete(route('admin.ekskul.unenroll', $enrollment));
        $resUnenroll->assertRedirect(route('admin.ekskul.index', ['ekskul_id' => $ekskul->id]));
        $this->assertDatabaseMissing('extracurricular_enrollments', ['id' => $enrollment->id]);
    }

    public function test_teacher_can_view_schedule_and_evaluate_worship(): void
    {
        $this->actingAs($this->teacher);

        // 1. Create schedule for this teacher
        ClassSchedule::create([
            'classroom_id' => $this->classroom->id,
            'academic_year_id' => $this->academicYear->id,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'day_of_week' => 'selasa',
            'start_time' => '09:00',
            'end_time' => '10:15',
            'period_order' => 2,
        ]);

        $resSch = $this->get(route('teacher.schedules.index'));
        $resSch->assertOk();
        $resSch->assertSeeText('Jadwal Mengajar Asatidz');
        $resSch->assertSeeText('Pendidikan Agama Islam');

        // 2. Teacher worship page & adding affirmation notes
        $resWorship = $this->get(route('teacher.worship.index', [
            'classroom_id' => $this->classroom->id,
            'date' => now()->toDateString(),
        ]));
        $resWorship->assertOk();
        $resWorship->assertSeeText('Mutaba\'ah Ibadah Yaumiyah');
        $resWorship->assertSeeText($this->student->full_name);

        $resNotes = $this->post(route('teacher.worship.notes'), [
            'student_id' => $this->student->id,
            'date' => now()->toDateString(),
            'teacher_notes' => 'Masya Allah, ananda sangat tertib sholat berjamaah.',
        ]);
        $resNotes->assertRedirect();
        $this->assertDatabaseHas('daily_worship_records', [
            'student_id' => $this->student->id,
            'teacher_notes' => 'Masya Allah, ananda sangat tertib sholat berjamaah.',
        ]);
    }

    public function test_parent_can_manage_child_worship_and_extracurricular(): void
    {
        $this->actingAs($this->parent);

        // 1. Parent views child schedule
        ClassSchedule::create([
            'classroom_id' => $this->classroom->id,
            'academic_year_id' => $this->academicYear->id,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'day_of_week' => 'senin',
            'start_time' => '07:30',
            'end_time' => '08:45',
            'period_order' => 1,
        ]);

        $resSch = $this->get(route('parent.schedules.index', ['student_id' => $this->student->id]));
        $resSch->assertOk();
        $resSch->assertSeeText('Jadwal Pelajaran Ananda');
        $resSch->assertSeeText('Pendidikan Agama Islam');

        // 2. Parent fills daily worship checklist
        $resWorshipStore = $this->post(route('parent.worship.store'), [
            'student_id' => $this->student->id,
            'date' => now()->toDateString(),
            'subuh' => 1,
            'dzuhur' => 1,
            'ashar' => 1,
            'maghrib' => 1,
            'isya' => 1,
            'dhuha' => 1,
            'tadarus_quran' => 1,
            'birrul_walidain' => 1,
            'parent_notes' => 'Ananda sholat subuh berjamaah di musholla.',
        ]);
        $resWorshipStore->assertRedirect(route('parent.worship.index', [
            'student_id' => $this->student->id,
            'date' => now()->toDateString(),
        ]));

        $this->assertDatabaseHas('daily_worship_records', [
            'student_id' => $this->student->id,
            'subuh' => true,
            'dhuha' => true,
            'parent_notes' => 'Ananda sholat subuh berjamaah di musholla.',
        ]);

        // 3. Parent enrolls child in extracurricular
        $ekskul = Extracurricular::create([
            'name' => 'Tahfidz Club',
            'category' => 'keagamaan',
            'instructor_name' => 'Ustadz Ahmad Al-Hafidz',
            'schedule_day' => 'Selasa',
            'schedule_time' => '15:30 - 17:00',
            'is_active' => true,
        ]);

        $resEnroll = $this->post(route('parent.ekskul.enroll'), [
            'student_id' => $this->student->id,
            'extracurricular_id' => $ekskul->id,
            'academic_year_id' => $this->academicYear->id,
        ]);
        $resEnroll->assertRedirect(route('parent.ekskul.index', ['student_id' => $this->student->id]));

        $this->assertDatabaseHas('extracurricular_enrollments', [
            'student_id' => $this->student->id,
            'extracurricular_id' => $ekskul->id,
            'status' => 'active',
        ]);

        $enrollment = ExtracurricularEnrollment::firstWhere('extracurricular_id', $ekskul->id);

        // 4. Parent cancels extracurricular
        $resCancel = $this->delete(route('parent.ekskul.cancel', $enrollment));
        $resCancel->assertRedirect(route('parent.ekskul.index', ['student_id' => $this->student->id]));
        $this->assertDatabaseMissing('extracurricular_enrollments', ['id' => $enrollment->id]);
    }
}
