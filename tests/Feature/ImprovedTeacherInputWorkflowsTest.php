<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Enums\Semester;
use App\Enums\StudentStatus;
use App\Enums\UserRole;
use App\Models\AcademicGrade;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\ClassSchedule;
use App\Models\DailyWorshipRecord;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImprovedTeacherInputWorkflowsTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher1;
    private User $teacher2;
    private User $parent;
    private Classroom $classroom1;
    private Classroom $classroom2;
    private Subject $subjectFiqih;
    private Subject $subjectMath;
    private AcademicYear $academicYear;
    private Student $student1;
    private Student $student2;
    private ClassSchedule $scheduleTeacher1;
    private ClassSchedule $scheduleTeacher2;

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

        $this->teacher1 = User::create([
            'name' => 'Ustadz Fathurrahman, S.Pd.I',
            'email' => 'fathur@simad.id',
            'phone_number' => '081211110001',
            'role' => UserRole::Teacher,
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->teacher2 = User::create([
            'name' => 'Ustadzah Nurul Hidayah, S.Si',
            'email' => 'nurul@simad.id',
            'phone_number' => '081211110002',
            'role' => UserRole::Teacher,
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->parent = User::create([
            'name' => 'Bapak Hendro Siswanto',
            'email' => 'hendro@simad.id',
            'phone_number' => '081211110003',
            'role' => UserRole::Parent,
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->classroom1 = Classroom::create([
            'name' => 'Kelas 1-A (Abu Bakar)',
            'grade_level' => 1,
            'academic_year_id' => $this->academicYear->id,
            'homeroom_teacher_id' => $this->teacher1->id,
            'capacity' => 28,
        ]);

        $this->classroom2 = Classroom::create([
            'name' => 'Kelas 2-B (Umar Bin Khattab)',
            'grade_level' => 2,
            'academic_year_id' => $this->academicYear->id,
            'homeroom_teacher_id' => $this->teacher2->id,
            'capacity' => 28,
        ]);

        $this->subjectFiqih = Subject::create([
            'code' => 'PAI-FQ',
            'name' => 'Fikih Ibadah',
            'category' => 'agama',
        ]);

        $this->subjectMath = Subject::create([
            'code' => 'MAT-01',
            'name' => 'Matematika Realistik',
            'category' => 'umum',
        ]);

        $this->student1 = Student::create([
            'nisn' => '0019283741',
            'nis' => '202601011',
            'full_name' => 'Zaidan Al-Farisi',
            'gender' => 'L',
            'birth_place' => 'Blitar',
            'birth_date' => '2019-03-20',
            'address' => 'Jl. Kalipucung No. 7, Blitar',
            'classroom_id' => $this->classroom1->id,
            'parent_id' => $this->parent->id,
            'status' => StudentStatus::Active,
        ]);

        $this->student2 = Student::create([
            'nisn' => '0019283742',
            'nis' => '202601012',
            'full_name' => 'Fathiyyah Az-Zahra',
            'gender' => 'P',
            'birth_place' => 'Blitar',
            'birth_date' => '2019-07-11',
            'address' => 'Jl. Kalipucung No. 15, Blitar',
            'classroom_id' => $this->classroom1->id,
            'parent_id' => $this->parent->id,
            'status' => StudentStatus::Active,
        ]);

        // Schedule: Teacher 1 teaches Fiqih in Kelas 1-A on Senin 07:30 - 08:45
        $this->scheduleTeacher1 = ClassSchedule::create([
            'classroom_id' => $this->classroom1->id,
            'subject_id' => $this->subjectFiqih->id,
            'teacher_id' => $this->teacher1->id,
            'academic_year_id' => $this->academicYear->id,
            'day_of_week' => 'senin',
            'start_time' => '07:30',
            'end_time' => '08:45',
            'period_order' => 1,
        ]);

        // Schedule: Teacher 2 teaches Math in Kelas 1-A on Senin 09:15 - 10:30
        $this->scheduleTeacher2 = ClassSchedule::create([
            'classroom_id' => $this->classroom1->id,
            'subject_id' => $this->subjectMath->id,
            'teacher_id' => $this->teacher2->id,
            'academic_year_id' => $this->academicYear->id,
            'day_of_week' => 'senin',
            'start_time' => '09:15',
            'end_time' => '10:30',
            'period_order' => 2,
        ]);
    }

    public function test_teacher_can_view_schedule_based_attendance(): void
    {
        $response = $this->actingAs($this->teacher1)->get(route('teacher.attendance.index', [
            'date' => '2026-07-20', // Monday
            'schedule_id' => $this->scheduleTeacher1->id,
        ]));

        $response->assertOk();
        $response->assertSee('Presensi Harian Santri');
        $response->assertSee('Kelas 1-A (Abu Bakar)');
        $response->assertSee('Fikih Ibadah');
        $response->assertSee('Zaidan Al-Farisi');
    }

    public function test_different_teachers_can_record_attendance_for_their_own_subjects_on_same_day(): void
    {
        $testDate = '2026-07-20';

        // 1. Teacher 1 records attendance for Fikih
        $res1 = $this->actingAs($this->teacher1)->post(route('teacher.attendance.store'), [
            'date' => $testDate,
            'classroom_id' => $this->classroom1->id,
            'subject_id' => $this->subjectFiqih->id,
            'class_schedule_id' => $this->scheduleTeacher1->id,
            'attendances' => [
                [
                    'student_id' => $this->student1->id,
                    'status' => 'hadir',
                    'notes' => 'Fasilitator bacaan shalat',
                ],
                [
                    'student_id' => $this->student2->id,
                    'status' => 'hadir',
                    'notes' => 'Hadir tepat waktu',
                ],
            ],
        ]);

        $res1->assertRedirect();
        $res1->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'student_id' => $this->student1->id,
            'classroom_id' => $this->classroom1->id,
            'subject_id' => $this->subjectFiqih->id,
            'class_schedule_id' => $this->scheduleTeacher1->id,
            'status' => 'hadir',
        ]);

        // 2. Teacher 2 records attendance for Matematika in the same class and same day
        $res2 = $this->actingAs($this->teacher2)->post(route('teacher.attendance.store'), [
            'date' => $testDate,
            'classroom_id' => $this->classroom1->id,
            'subject_id' => $this->subjectMath->id,
            'class_schedule_id' => $this->scheduleTeacher2->id,
            'attendances' => [
                [
                    'student_id' => $this->student1->id,
                    'status' => 'izin',
                    'notes' => 'Izin ke UKS sakit kepala',
                ],
                [
                    'student_id' => $this->student2->id,
                    'status' => 'hadir',
                    'notes' => 'Mengerjakan lembar kerja',
                ],
            ],
        ]);

        $res2->assertRedirect();
        $res2->assertSessionHas('success');

        // Both subject attendances coexist happily without collision!
        $this->assertDatabaseHas('attendances', [
            'student_id' => $this->student1->id,
            'classroom_id' => $this->classroom1->id,
            'subject_id' => $this->subjectMath->id,
            'class_schedule_id' => $this->scheduleTeacher2->id,
            'status' => 'izin',
        ]);

        // Verify parent portal displays subject info
        $resParent = $this->actingAs($this->parent)->get(route('parent.attendance.index', [
            'student_id' => $this->student1->id,
        ]));
        $resParent->assertOk();
        $resParent->assertSee('Fikih Ibadah');
        $resParent->assertSee('Matematika Realistik');
    }

    public function test_teacher_can_switch_classrooms_and_input_grades(): void
    {
        // Teacher 1 views and inputs grades for classroom 1
        $resView = $this->actingAs($this->teacher1)->get(route('teacher.grades.index', [
            'classroom_id' => $this->classroom1->id,
            'subject_id' => $this->subjectFiqih->id,
            'grade_type' => 'harian',
        ]));
        $resView->assertOk();
        $resView->assertSee('Buku Penilaian Akademik');
        $resView->assertSee('Fikih Ibadah');

        // Teacher 1 submits grades for student 1 & student 2
        $resStore = $this->actingAs($this->teacher1)->post(route('teacher.grades.store'), [
            'classroom_id' => $this->classroom1->id,
            'subject_id' => $this->subjectFiqih->id,
            'grade_type' => 'harian',
            'grades' => [
                [
                    'student_id' => $this->student1->id,
                    'score' => 88,
                    'notes' => 'Sangat paham rukun wudhu',
                ],
                [
                    'student_id' => $this->student2->id,
                    'score' => 92,
                    'notes' => 'Praktik sholat sangat tertib',
                ],
            ],
        ]);

        $resStore->assertRedirect();
        $resStore->assertSessionHas('success');

        $this->assertDatabaseHas('academic_grades', [
            'student_id' => $this->student1->id,
            'classroom_id' => $this->classroom1->id,
            'subject_id' => $this->subjectFiqih->id,
            'score' => 88,
            'grade_type' => 'harian',
        ]);
        $this->assertDatabaseHas('academic_grades', [
            'student_id' => $this->student2->id,
            'classroom_id' => $this->classroom1->id,
            'subject_id' => $this->subjectFiqih->id,
            'score' => 92,
            'grade_type' => 'harian',
        ]);
    }

    public function test_teacher_can_save_grades_using_smart_spreadsheet_mode(): void
    {
        // Teacher submits multiple assessment types at once (UH, Tugas, PTS, PAS)
        $res = $this->actingAs($this->teacher1)->post(route('teacher.grades.store'), [
            'classroom_id' => $this->classroom1->id,
            'subject_id' => $this->subjectFiqih->id,
            'mode' => 'spreadsheet',
            'grades' => [
                [
                    'student_id' => $this->student1->id,
                    'harian' => 85,
                    'tugas' => 90,
                    'pts' => 88,
                    'pas' => 92,
                    'notes' => 'Menunjukkan penguasaan kompetensi materi dengan sangat baik dan mandiri.',
                ],
            ],
        ]);

        $res->assertRedirect();
        $res->assertSessionHas('success');

        $this->assertDatabaseHas('academic_grades', [
            'student_id' => $this->student1->id,
            'classroom_id' => $this->classroom1->id,
            'subject_id' => $this->subjectFiqih->id,
            'grade_type' => 'harian',
            'score' => 85,
        ]);
        $this->assertDatabaseHas('academic_grades', [
            'student_id' => $this->student1->id,
            'classroom_id' => $this->classroom1->id,
            'subject_id' => $this->subjectFiqih->id,
            'grade_type' => 'tugas',
            'score' => 90,
        ]);
        $this->assertDatabaseHas('academic_grades', [
            'student_id' => $this->student1->id,
            'classroom_id' => $this->classroom1->id,
            'subject_id' => $this->subjectFiqih->id,
            'grade_type' => 'pts',
            'score' => 88,
        ]);
        $this->assertDatabaseHas('academic_grades', [
            'student_id' => $this->student1->id,
            'classroom_id' => $this->classroom1->id,
            'subject_id' => $this->subjectFiqih->id,
            'grade_type' => 'pas',
            'score' => 92,
        ]);
    }

    public function test_teacher_can_bulk_record_and_evaluate_worship_for_class(): void
    {
        $testDate = '2026-07-20';

        // Teacher bulk marks daily worship for the classroom
        $resStore = $this->actingAs($this->teacher1)->post(route('teacher.worship.store'), [
            'classroom_id' => $this->classroom1->id,
            'date' => $testDate,
            'worship' => [
                [
                    'student_id' => $this->student1->id,
                    'subuh' => '1',
                    'dzuhur' => '1',
                    'ashar' => '1',
                    'maghrib' => '1',
                    'isya' => '1',
                    'dhuha' => '1',
                    'tadarus_quran' => '1',
                    'birrul_walidain' => '1',
                    'teacher_notes' => 'MasyaAllah istiqomah berjamaah di madrasah',
                ],
                [
                    'student_id' => $this->student2->id,
                    'subuh' => '1',
                    'dzuhur' => '1',
                    'ashar' => '1',
                    'maghrib' => '1',
                    'isya' => '0',
                    'dhuha' => '1',
                    'tadarus_quran' => '1',
                    'birrul_walidain' => '1',
                    'teacher_notes' => 'Sholat dhuha sangat khusyuk',
                ],
            ],
        ]);

        $resStore->assertRedirect();
        $resStore->assertSessionHas('success');

        $this->assertDatabaseHas('daily_worship_records', [
            'student_id' => $this->student1->id,
            'dzuhur' => 1,
            'dhuha' => 1,
            'tadarus_quran' => 1,
            'teacher_notes' => 'MasyaAllah istiqomah berjamaah di madrasah',
        ]);

        // Verify page renders stats correctly
        $resView = $this->actingAs($this->teacher1)->get(route('teacher.worship.index', [
            'classroom_id' => $this->classroom1->id,
            'date' => $testDate,
        ]));
        $resView->assertOk();
        $resView->assertSee('Mutaba\'ah Ibadah Yaumiyah');
        $resView->assertSee('Zaidan Al-Farisi');
    }
}
