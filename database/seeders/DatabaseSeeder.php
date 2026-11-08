<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AnnouncementTarget;
use App\Enums\AttendanceStatus;
use App\Enums\GalleryCategory;
use App\Enums\GradeType;
use App\Enums\LeaveReasonType;
use App\Enums\LeaveStatus;
use App\Enums\PaymentMethod;
use App\Enums\PpdbStatus;
use App\Enums\Semester;
use App\Enums\StudentStatus;
use App\Enums\SubjectCategory;
use App\Enums\TahfidzFluency;
use App\Enums\TahfidzTajweed;
use App\Enums\TuitionStatus;
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
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Core Users (Admin, Teachers, Demo Parent)
        $admin = User::firstOrCreate(
            ['email' => 'admin@simad.id'],
            [
                'name' => 'Staf Tata Usaha',
                'phone_number' => '081200000001',
                'role' => UserRole::Admin,
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        $teacher1 = User::firstOrCreate(
            ['email' => 'guru@simad.id'],
            [
                'name' => 'Ustadzah Fatimah Azzahra, S.Pd.',
                'phone_number' => '081200000002',
                'role' => UserRole::Teacher,
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        $teacher2 = User::firstOrCreate(
            ['email' => 'guru2@simad.id'],
            [
                'name' => 'Ustadz Ahmad Al-Hafidz, S.Pd.I.',
                'phone_number' => '081200000004',
                'role' => UserRole::Teacher,
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        $demoParent = User::firstOrCreate(
            ['email' => 'wali@simad.id'],
            [
                'name' => 'Bapak Ahmad Dahlan',
                'phone_number' => '081200000003',
                'role' => UserRole::Parent,
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        // 2. Academic Year
        $academicYear = AcademicYear::firstOrCreate(
            ['name' => '2026/2027'],
            [
                'semester' => Semester::Ganjil,
                'start_date' => '2026-07-15',
                'end_date' => '2027-06-20',
                'is_active' => true,
            ]
        );

        // 3. Classrooms
        $classroomA = Classroom::firstOrCreate(
            ['name' => 'Kelas 1-A (Abu Bakar)'],
            [
                'academic_year_id' => $academicYear->id,
                'homeroom_teacher_id' => $teacher1->id,
                'grade_level' => 1,
            ]
        );

        $classroomB = Classroom::firstOrCreate(
            ['name' => 'Kelas 1-B (Umar bin Khattab)'],
            [
                'academic_year_id' => $academicYear->id,
                'homeroom_teacher_id' => $teacher2->id,
                'grade_level' => 1,
            ]
        );

        // 4. Subjects
        $subjectsData = [
            ['code' => 'PAI-QH', 'name' => "Al-Qur'an Hadits", 'category' => SubjectCategory::Agama],
            ['code' => 'PAI-AA', 'name' => 'Akidah Akhlak', 'category' => SubjectCategory::Agama],
            ['code' => 'PAI-FQ', 'name' => 'Fiqih Ibadah', 'category' => SubjectCategory::Agama],
            ['code' => 'PAI-SKI', 'name' => 'Sejarah Kebudayaan Islam', 'category' => SubjectCategory::Agama],
            ['code' => 'B-ARB', 'name' => 'Bahasa Arab', 'category' => SubjectCategory::Agama],
            ['code' => 'TMP-PP', 'name' => 'Pendidikan Pancasila', 'category' => SubjectCategory::Umum],
            ['code' => 'TMP-BI', 'name' => 'Bahasa Indonesia', 'category' => SubjectCategory::Umum],
            ['code' => 'MAT-01', 'name' => 'Matematika Dasar', 'category' => SubjectCategory::Umum],
            ['code' => 'PJOK-01', 'name' => 'Pendidikan Jasmani & Olahraga', 'category' => SubjectCategory::Umum],
            ['code' => 'MLK-JW', 'name' => 'Bahasa Jawa (Mulok)', 'category' => SubjectCategory::MuatanLokal],
        ];

        $subjects = collect();
        foreach ($subjectsData as $s) {
            $subjects->push(
                Subject::firstOrCreate(
                    ['code' => $s['code']],
                    ['name' => $s['name'], 'category' => $s['category']]
                )
            );
        }

        // 5. 30 Indonesian Islamic Students & Parents
        $studentsRaw = [
            // Kelas 1-A (15 Siswa)
            [
                'name' => 'Muhammad Bilal Al-Fatih', 'nick' => 'Bilal', 'gender' => 'L',
                'nis' => '262701001', 'nisn' => '3123456789', 'p_name' => 'Bapak Ahmad Dahlan',
                'p_email' => 'wali@simad.id', 'p_phone' => '081200000003',
                'class' => $classroomA, 'b_date' => '2019-05-15', 'b_place' => 'Blitar',
                'addr' => 'Jl. Merdeka No. 45, Kalipucung, Sanankulon, Blitar',
            ],
            [
                'name' => 'Aisyah Putri Azzahra', 'nick' => 'Aisyah', 'gender' => 'P',
                'nis' => '262701002', 'nisn' => '3123456790', 'p_name' => 'Bapak Bambang Wijaya',
                'p_email' => 'wali2@simad.id', 'p_phone' => '081200000012',
                'class' => $classroomA, 'b_date' => '2019-08-20', 'b_place' => 'Blitar',
                'addr' => 'Jl. Kenanga No. 12, Bendogerit, Sananwetan, Blitar',
            ],
            [
                'name' => 'Zaid bin Tsabit Al-Anshari', 'nick' => 'Zaid', 'gender' => 'L',
                'nis' => '262701003', 'nisn' => '3123456791', 'p_name' => 'Bapak Muhammad Tsabit',
                'p_email' => 'wali3@simad.id', 'p_phone' => '081200000013',
                'class' => $classroomA, 'b_date' => '2019-02-10', 'b_place' => 'Kediri',
                'addr' => 'Jl. Lawu No. 8, Sukorejo, Blitar',
            ],
            [
                'name' => 'Fatimah Zahra Al-Munawwarah', 'nick' => 'Fatimah', 'gender' => 'P',
                'nis' => '262701004', 'nisn' => '3123456792', 'p_name' => 'Bapak Ridwan Kamiludin',
                'p_email' => 'wali4@simad.id', 'p_phone' => '081200000014',
                'class' => $classroomA, 'b_date' => '2019-11-04', 'b_place' => 'Blitar',
                'addr' => 'Jl. Melati No. 24, Kepanjenkidul, Blitar',
            ],
            [
                'name' => 'Umar Faruq Al-Khattab', 'nick' => 'Umar', 'gender' => 'L',
                'nis' => '262701005', 'nisn' => '3123456793', 'p_name' => 'Bapak Hendro Sulistyo',
                'p_email' => 'wali5@simad.id', 'p_phone' => '081200000015',
                'class' => $classroomA, 'b_date' => '2019-03-18', 'b_place' => 'Tulungagung',
                'addr' => 'Dusun Sumberjo, Sanankulon, Blitar',
            ],
            [
                'name' => 'Maryam Sholihatunnisa', 'nick' => 'Maryam', 'gender' => 'P',
                'nis' => '262701006', 'nisn' => '3123456794', 'p_name' => 'Bapak Lukman Hakim',
                'p_email' => 'wali6@simad.id', 'p_phone' => '081200000016',
                'class' => $classroomA, 'b_date' => '2019-07-22', 'b_place' => 'Blitar',
                'addr' => 'Jl. Anggrek No. 15, Kalipucung, Blitar',
            ],
            [
                'name' => 'Hamzah Asadullah Al-Qurasyi', 'nick' => 'Hamzah', 'gender' => 'L',
                'nis' => '262701007', 'nisn' => '3123456795', 'p_name' => 'Bapak Arif Hidayat',
                'p_email' => 'wali7@simad.id', 'p_phone' => '081200000017',
                'class' => $classroomA, 'b_date' => '2019-01-30', 'b_place' => 'Blitar',
                'addr' => 'Jl. Mawar No. 8, Sananwetan, Blitar',
            ],
            [
                'name' => 'Khadijah Al-Kubra Ramadhani', 'nick' => 'Khadijah', 'gender' => 'P',
                'nis' => '262701008', 'nisn' => '3123456796', 'p_name' => 'Bapak Surya Ramadhan',
                'p_email' => 'wali8@simad.id', 'p_phone' => '081200000018',
                'class' => $classroomA, 'b_date' => '2019-06-05', 'b_place' => 'Blitar',
                'addr' => 'Jl. Cempaka No. 3, Sukorejo, Blitar',
            ],
            [
                'name' => 'Ali Zainal Abidin', 'nick' => 'Ali', 'gender' => 'L',
                'nis' => '262701009', 'nisn' => '3123456797', 'p_name' => 'Bapak Faisal Basri',
                'p_email' => 'wali9@simad.id', 'p_phone' => '081200000019',
                'class' => $classroomA, 'b_date' => '2019-09-12', 'b_place' => 'Kediri',
                'addr' => 'Jl. Diponegoro No. 19, Sanankulon, Blitar',
            ],
            [
                'name' => 'Hafshah binti Umar Prasetyo', 'nick' => 'Hafshah', 'gender' => 'P',
                'nis' => '262701010', 'nisn' => '3123456798', 'p_name' => 'Bapak Agus Prasetyo',
                'p_email' => 'wali10@simad.id', 'p_phone' => '081200000020',
                'class' => $classroomA, 'b_date' => '2019-04-25', 'b_place' => 'Blitar',
                'addr' => 'Jl. Dahlia No. 7, Kalipucung, Blitar',
            ],
            [
                'name' => 'Anas bin Malik Al-Khazraji', 'nick' => 'Anas', 'gender' => 'L',
                'nis' => '262701011', 'nisn' => '3123456799', 'p_name' => 'Bapak Deni Irawan',
                'p_email' => 'wali11@simad.id', 'p_phone' => '081200000021',
                'class' => $classroomA, 'b_date' => '2019-10-15', 'b_place' => 'Blitar',
                'addr' => 'Jl. Veteran No. 88, Kepanjenkidul, Blitar',
            ],
            [
                'name' => 'Ruqayyah Al-Hasyimiyyah', 'nick' => 'Ruqayyah', 'gender' => 'P',
                'nis' => '262701012', 'nisn' => '3123456800', 'p_name' => 'Bapak Hasan Basri',
                'p_email' => 'wali12@simad.id', 'p_phone' => '081200000022',
                'class' => $classroomA, 'b_date' => '2019-02-28', 'b_place' => 'Blitar',
                'addr' => 'Jl. Kalimantan No. 34, Sananwetan, Blitar',
            ],
            [
                'name' => 'Ibrahim Khalilullah Ar-Rasyid', 'nick' => 'Ibrahim', 'gender' => 'L',
                'nis' => '262701013', 'nisn' => '3123456801', 'p_name' => 'Bapak Harun Ar-Rasyid',
                'p_email' => 'wali13@simad.id', 'p_phone' => '081200000023',
                'class' => $classroomA, 'b_date' => '2019-12-01', 'b_place' => 'Tulungagung',
                'addr' => 'Jl. Sumatera No. 11, Sukorejo, Blitar',
            ],
            [
                'name' => 'Zainab Al-Ghazali Putri', 'nick' => 'Zainab', 'gender' => 'P',
                'nis' => '262701014', 'nisn' => '3123456802', 'p_name' => 'Bapak Gunawan Wibisono',
                'p_email' => 'wali14@simad.id', 'p_phone' => '081200000024',
                'class' => $classroomA, 'b_date' => '2019-05-09', 'b_place' => 'Blitar',
                'addr' => 'Dusun Kalipucung RT 02 RW 01, Sanankulon, Blitar',
            ],
            [
                'name' => 'Salman Al-Farisi Iskandar', 'nick' => 'Salman', 'gender' => 'L',
                'nis' => '262701015', 'nisn' => '3123456803', 'p_name' => 'Bapak Iskandar Zulkarnain',
                'p_email' => 'wali15@simad.id', 'p_phone' => '081200000025',
                'class' => $classroomA, 'b_date' => '2019-08-14', 'b_place' => 'Blitar',
                'addr' => 'Jl. Ahmad Yani No. 52, Kepanjenkidul, Blitar',
            ],

            // Kelas 1-B (15 Siswa)
            [
                'name' => 'Asma binti Abi Bakar Ash-Shiddiq', 'nick' => 'Asma', 'gender' => 'P',
                'nis' => '262701016', 'nisn' => '3123456804', 'p_name' => 'Bapak Tri Wahyudi',
                'p_email' => 'wali16@simad.id', 'p_phone' => '081200000026',
                'class' => $classroomB, 'b_date' => '2019-03-03', 'b_place' => 'Blitar',
                'addr' => 'Jl. Sudirman No. 10, Sananwetan, Blitar',
            ],
            [
                'name' => 'Thoriq bin Ziyad Al-Fatih', 'nick' => 'Thoriq', 'gender' => 'L',
                'nis' => '262701017', 'nisn' => '3123456805', 'p_name' => 'Bapak Bambang Hermanto',
                'p_email' => 'wali17@simad.id', 'p_phone' => '081200000027',
                'class' => $classroomB, 'b_date' => '2019-07-17', 'b_place' => 'Blitar',
                'addr' => 'Dusun Sumberrejo RT 01 RW 03, Sanankulon, Blitar',
            ],
            [
                'name' => 'Safiyyah binti Huyay Kusuma', 'nick' => 'Safiyyah', 'gender' => 'P',
                'nis' => '262701018', 'nisn' => '3123456806', 'p_name' => 'Bapak Danang Kusuma',
                'p_email' => 'wali18@simad.id', 'p_phone' => '081200000028',
                'class' => $classroomB, 'b_date' => '2019-09-29', 'b_place' => 'Blitar',
                'addr' => 'Jl. KH Mansyur No. 4, Sukorejo, Blitar',
            ],
            [
                'name' => 'Khalid bin Walid Al-Makhzumi', 'nick' => 'Khalid', 'gender' => 'L',
                'nis' => '262701019', 'nisn' => '3123456807', 'p_name' => 'Bapak Walid Abdullah',
                'p_email' => 'wali19@simad.id', 'p_phone' => '081200000029',
                'class' => $classroomB, 'b_date' => '2019-01-11', 'b_place' => 'Kediri',
                'addr' => 'Jl. Semeru No. 16, Kepanjenkidul, Blitar',
            ],
            [
                'name' => 'Juwairiyah Al-Musthaliqiyyah', 'nick' => 'Juwairiyah', 'gender' => 'P',
                'nis' => '262701020', 'nisn' => '3123456808', 'p_name' => 'Bapak Slamet Raharjo',
                'p_email' => 'wali20@simad.id', 'p_phone' => '081200000030',
                'class' => $classroomB, 'b_date' => '2019-06-19', 'b_place' => 'Blitar',
                'addr' => 'Jl. Kawi No. 28, Sananwetan, Blitar',
            ],
            [
                'name' => 'Usamah bin Zaid Al-Habib', 'nick' => 'Usamah', 'gender' => 'L',
                'nis' => '262701021', 'nisn' => '3123456809', 'p_name' => 'Bapak Zaidan Habibi',
                'p_email' => 'wali21@simad.id', 'p_phone' => '081200000031',
                'class' => $classroomB, 'b_date' => '2019-10-08', 'b_place' => 'Blitar',
                'addr' => 'Jl. Bromo No. 12, Kalipucung, Blitar',
            ],
            [
                'name' => 'Halimah As-Sa\'diyah Wardani', 'nick' => 'Halimah', 'gender' => 'P',
                'nis' => '262701022', 'nisn' => '3123456810', 'p_name' => 'Bapak Eko Wardana',
                'p_email' => 'wali22@simad.id', 'p_phone' => '081200000032',
                'class' => $classroomB, 'b_date' => '2019-04-14', 'b_place' => 'Blitar',
                'addr' => 'Jl. Kelud No. 5, Sanankulon, Blitar',
            ],
            [
                'name' => 'Hudzaifah ibnul Yaman Al-Absi', 'nick' => 'Hudzaifah', 'gender' => 'L',
                'nis' => '262701023', 'nisn' => '3123456811', 'p_name' => 'Bapak Yaman Syaifullah',
                'p_email' => 'wali23@simad.id', 'p_phone' => '081200000033',
                'class' => $classroomB, 'b_date' => '2019-02-05', 'b_place' => 'Tulungagung',
                'addr' => 'Jl. Anjasmoro No. 21, Sukorejo, Blitar',
            ],
            [
                'name' => 'Maimunah Al-Hilaliyyah Lestari', 'nick' => 'Maimunah', 'gender' => 'P',
                'nis' => '262701024', 'nisn' => '3123456812', 'p_name' => 'Bapak Joko Lestari',
                'p_email' => 'wali24@simad.id', 'p_phone' => '081200000034',
                'class' => $classroomB, 'b_date' => '2019-08-31', 'b_place' => 'Blitar',
                'addr' => 'Jl. Raung No. 9, Sananwetan, Blitar',
            ],
            [
                'name' => 'Zubair bin Awwam Al-Qurasyi', 'nick' => 'Zubair', 'gender' => 'L',
                'nis' => '262701025', 'nisn' => '3123456813', 'p_name' => 'Bapak Awwam Nurkholis',
                'p_email' => 'wali25@simad.id', 'p_phone' => '081200000035',
                'class' => $classroomB, 'b_date' => '2019-11-20', 'b_place' => 'Blitar',
                'addr' => 'Jl. Wilis No. 17, Kepanjenkidul, Blitar',
            ],
            [
                'name' => 'Ummu Salamah Al-Makhzumiyyah', 'nick' => 'Salamah', 'gender' => 'P',
                'nis' => '262701026', 'nisn' => '3123456814', 'p_name' => 'Bapak Abu Bakar Shidiq',
                'p_email' => 'wali26@simad.id', 'p_phone' => '081200000036',
                'class' => $classroomB, 'b_date' => '2019-03-27', 'b_place' => 'Blitar',
                'addr' => 'Jl. Penataran No. 6, Sanankulon, Blitar',
            ],
            [
                'name' => 'Ammar bin Yasir Al-Anshari', 'nick' => 'Ammar', 'gender' => 'L',
                'nis' => '262701027', 'nisn' => '3123456815', 'p_name' => 'Bapak Yasir Arafat',
                'p_email' => 'wali27@simad.id', 'p_phone' => '081200000037',
                'class' => $classroomB, 'b_date' => '2019-07-09', 'b_place' => 'Kediri',
                'addr' => 'Jl. Panglima Sudirman No. 40, Sukorejo, Blitar',
            ],
            [
                'name' => 'Shofiyyah binti Abdul Muthalib', 'nick' => 'Shofiyyah', 'gender' => 'P',
                'nis' => '262701028', 'nisn' => '3123456816', 'p_name' => 'Bapak Rahmat Hidayatullah',
                'p_email' => 'wali28@simad.id', 'p_phone' => '081200000038',
                'class' => $classroomB, 'b_date' => '2019-12-18', 'b_place' => 'Blitar',
                'addr' => 'Dusun Sanan RT 03 RW 02, Sanankulon, Blitar',
            ],
            [
                'name' => 'Miqdad bin Amr Al-Kindi', 'nick' => 'Miqdad', 'gender' => 'L',
                'nis' => '262701029', 'nisn' => '3123456817', 'p_name' => 'Bapak Amrullah Shodiq',
                'p_email' => 'wali29@simad.id', 'p_phone' => '081200000039',
                'class' => $classroomB, 'b_date' => '2019-05-23', 'b_place' => 'Blitar',
                'addr' => 'Jl. Hayam Wuruk No. 14, Sananwetan, Blitar',
            ],
            [
                'name' => 'Lubabah binti Al-Harits Al-Hilaliyyah', 'nick' => 'Lubabah', 'gender' => 'P',
                'nis' => '262701030', 'nisn' => '3123456818', 'p_name' => 'Bapak Harits Al-Ghozali',
                'p_email' => 'wali30@simad.id', 'p_phone' => '081200000040',
                'class' => $classroomB, 'b_date' => '2019-09-01', 'b_place' => 'Blitar',
                'addr' => 'Jl. Gajah Mada No. 31, Kepanjenkidul, Blitar',
            ],
        ];

        $students = collect();

        foreach ($studentsRaw as $data) {
            // Find or create parent user
            $parentUser = User::firstOrCreate(
                ['email' => $data['p_email']],
                [
                    'name' => $data['p_name'],
                    'phone_number' => $data['p_phone'],
                    'role' => UserRole::Parent,
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ]
            );

            // Find or create student
            $student = Student::firstOrCreate(
                ['nisn' => $data['nisn']],
                [
                    'parent_id' => $parentUser->id,
                    'classroom_id' => $data['class']->id,
                    'nis' => $data['nis'],
                    'full_name' => $data['name'],
                    'nickname' => $data['nick'],
                    'gender' => $data['gender'],
                    'birth_place' => $data['b_place'],
                    'birth_date' => $data['b_date'],
                    'status' => StudentStatus::Active,
                    'address' => $data['addr'],
                ]
            );

            $students->push($student);
        }

        // 6. Presensi Harian Santri (5 Hari Aktif Terakhir)
        $pastDates = [
            now()->subDays(4)->toDateString(),
            now()->subDays(3)->toDateString(),
            now()->subDays(2)->toDateString(),
            now()->subDay()->toDateString(),
            now()->toDateString(),
        ];

        foreach ($students as $index => $student) {
            foreach ($pastDates as $dIndex => $date) {
                // Realistic variation: mostly Hadir, a few Sakit / Izin
                $status = AttendanceStatus::Hadir;
                $notes = 'Hadir tertib';

                if ($index === 2 && $dIndex >= 3) {
                    $status = AttendanceStatus::Sakit;
                    $notes = 'Demam dan flu';
                } elseif ($index === 5 && $dIndex === 2) {
                    $status = AttendanceStatus::Izin;
                    $notes = 'Acara keluarga di luar kota';
                } elseif ($index === 18 && $dIndex === 1) {
                    $status = AttendanceStatus::Alpa;
                    $notes = 'Tanpa keterangan';
                }

                Attendance::firstOrCreate(
                    [
                        'student_id' => $student->id,
                        'date' => $date,
                    ],
                    [
                        'classroom_id' => $student->classroom_id,
                        'recorded_by' => $student->classroom->homeroom_teacher_id ?? $teacher1->id,
                        'status' => $status,
                        'notes' => $notes,
                    ]
                );
            }
        }

        // 7. Pengajuan Izin Santri
        LeaveRequest::firstOrCreate(
            [
                'student_id' => $students[2]->id,
                'start_date' => now()->subDays(1)->toDateString(),
            ],
            [
                'parent_id' => $students[2]->parent_id,
                'reason_type' => LeaveReasonType::Sakit,
                'end_date' => now()->toDateString(),
                'description' => 'Ananda Zaid mengalami demam dan disarankan dokter istirahat di rumah.',
                'status' => LeaveStatus::Approved,
                'reviewed_by' => $teacher1->id,
                'review_notes' => 'Syafakallah ananda Zaid, istirahat sampai pulih.',
            ]
        );

        LeaveRequest::firstOrCreate(
            [
                'student_id' => $students[5]->id,
                'start_date' => now()->addDay()->toDateString(),
            ],
            [
                'parent_id' => $students[5]->parent_id,
                'reason_type' => LeaveReasonType::Izin,
                'end_date' => now()->addDays(2)->toDateString(),
                'description' => 'Izin menghadiri pernikahan bibi di Ponorogo.',
                'status' => LeaveStatus::Pending,
            ]
        );

        // 8. Jurnal Mutaba'ah Tahfidz Santri (Juz 30)
        $tahfidzData = [
            ['surah' => 'An-Naas', 'range' => 'Ayat 1-6', 'fl' => TahfidzFluency::Lancar, 'tj' => TahfidzTajweed::Mumtaz, 'note' => 'Makharijul huruf sangat fasih'],
            ['surah' => 'Al-Falaq', 'range' => 'Ayat 1-5', 'fl' => TahfidzFluency::Lancar, 'tj' => TahfidzTajweed::Mumtaz, 'note' => 'Qolqolah kubro di akhir ayat sudah mantap'],
            ['surah' => 'Al-Ikhlas', 'range' => 'Ayat 1-4', 'fl' => TahfidzFluency::Lancar, 'tj' => TahfidzTajweed::Mumtaz, 'note' => 'Tartil dan fasih'],
            ['surah' => 'Al-Kafirun', 'range' => 'Ayat 1-6', 'fl' => TahfidzFluency::Lancar, 'tj' => TahfidzTajweed::Jayyid, 'note' => 'Perhatikan mad jaiz munfashil'],
            ['surah' => 'Al-Kautsar', 'range' => 'Ayat 1-3', 'fl' => TahfidzFluency::Lancar, 'tj' => TahfidzTajweed::Mumtaz, 'note' => 'Bagus sekali'],
            ['surah' => 'Al-Ma\'un', 'range' => 'Ayat 1-7', 'fl' => TahfidzFluency::KurangLancar, 'tj' => TahfidzTajweed::Jayyid, 'note' => 'Ayat 4-5 perlu diulang kembali'],
            ['surah' => 'Quraisy', 'range' => 'Ayat 1-4', 'fl' => TahfidzFluency::Lancar, 'tj' => TahfidzTajweed::Mumtaz, 'note' => 'Mad lin sudah tepat'],
            ['surah' => 'Al-Fil', 'range' => 'Ayat 1-5', 'fl' => TahfidzFluency::Lancar, 'tj' => TahfidzTajweed::Mumtaz, 'note' => 'Sangat tartil'],
            ['surah' => 'Al-Ashr', 'range' => 'Ayat 1-3', 'fl' => TahfidzFluency::Lancar, 'tj' => TahfidzTajweed::Mumtaz, 'note' => 'Fasih dan lancar'],
            ['surah' => 'At-Takatsur', 'range' => 'Ayat 1-8', 'fl' => TahfidzFluency::Lancar, 'tj' => TahfidzTajweed::Jayyid, 'note' => 'Perhatikan gunnah pada nun tasydid'],
            ['surah' => 'Al-Qari\'ah', 'range' => 'Ayat 1-11', 'fl' => TahfidzFluency::Lancar, 'tj' => TahfidzTajweed::Mumtaz, 'note' => 'Sangat baik'],
            ['surah' => 'Al-Adiyat', 'range' => 'Ayat 1-11', 'fl' => TahfidzFluency::Lancar, 'tj' => TahfidzTajweed::Jayyid, 'note' => 'Qolqolah sughro di awal ayat perlu diperjelas'],
            ['surah' => 'Az-Zalzalah', 'range' => 'Ayat 1-8', 'fl' => TahfidzFluency::Lancar, 'tj' => TahfidzTajweed::Mumtaz, 'note' => 'Alhamdulillah lancar'],
            ['surah' => 'Al-Bayyinah', 'range' => 'Ayat 1-8', 'fl' => TahfidzFluency::KurangLancar, 'tj' => TahfidzTajweed::Maqbul, 'note' => 'Perlu muroja\'ah ayat 5 dan 7'],
            ['surah' => 'Al-Qadr', 'range' => 'Ayat 1-5', 'fl' => TahfidzFluency::Lancar, 'tj' => TahfidzTajweed::Mumtaz, 'note' => 'Makhraj huruf ra mantap'],
        ];

        foreach ($students as $i => $student) {
            $pick = $tahfidzData[$i % count($tahfidzData)];
            TahfidzRecord::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'surah_name' => $pick['surah'],
                ],
                [
                    'teacher_id' => $student->classroom->homeroom_teacher_id ?? $teacher1->id,
                    'date' => now()->subDays($i % 7)->toDateString(),
                    'verse_range' => $pick['range'],
                    'fluency_grade' => $pick['fl'],
                    'tajweed_grade' => $pick['tj'],
                    'notes' => $pick['note'],
                ]
            );
        }

        // 9. Nilai Akademik Santri (UH, Tugas, PTS)
        foreach ($students as $student) {
            foreach ($subjects->take(4) as $subject) {
                // Nilai Harian (UH)
                AcademicGrade::firstOrCreate(
                    [
                        'student_id' => $student->id,
                        'subject_id' => $subject->id,
                        'grade_type' => GradeType::Harian,
                    ],
                    [
                        'classroom_id' => $student->classroom_id,
                        'academic_year_id' => $academicYear->id,
                        'teacher_id' => $student->classroom->homeroom_teacher_id ?? $teacher1->id,
                        'score' => rand(80, 98),
                        'notes' => 'Pemahaman materi sangat baik.',
                    ]
                );

                // Nilai Tugas
                AcademicGrade::firstOrCreate(
                    [
                        'student_id' => $student->id,
                        'subject_id' => $subject->id,
                        'grade_type' => GradeType::Tugas,
                    ],
                    [
                        'classroom_id' => $student->classroom_id,
                        'academic_year_id' => $academicYear->id,
                        'teacher_id' => $student->classroom->homeroom_teacher_id ?? $teacher1->id,
                        'score' => rand(82, 100),
                        'notes' => 'Tugas mandiri dikerjakan tepat waktu dan rapi.',
                    ]
                );
            }
        }

        // 10. Tagihan SPP & Pembayaran Santri (Bulan Juli - November 2026)
        $months = [
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
        ];

        foreach ($students as $sIdx => $student) {
            foreach ($months as $mNum => $mName) {
                $status = TuitionStatus::Unpaid;
                if ($mNum <= 9) {
                    $status = TuitionStatus::Paid;
                } elseif ($mNum === 10 && $sIdx < 15) {
                    $status = TuitionStatus::Paid;
                } elseif ($mNum === 10 && $sIdx >= 15 && $sIdx < 20) {
                    $status = TuitionStatus::PendingVerification;
                }

                $bill = TuitionBill::firstOrCreate(
                    [
                        'student_id' => $student->id,
                        'month' => $mNum,
                        'year' => 2026,
                    ],
                    [
                        'academic_year_id' => $academicYear->id,
                        'bill_title' => "SPP Bulan {$mName} 2026",
                        'amount' => 150000,
                        'status' => $status,
                        'due_date' => "2026-{$mNum}-10",
                    ]
                );

                // If paid, create payment record
                if ($status === TuitionStatus::Paid && ! $bill->payment) {
                    $isCash = ($sIdx % 2 === 0);
                    $receiptCode = $isCash
                        ? 'KWT-CASH-2026' . str_pad((string) $mNum, 2, '0', STR_PAD_LEFT) . '-' . str_pad((string) $bill->id, 4, '0', STR_PAD_LEFT)
                        : 'KWT-2026' . str_pad((string) $mNum, 2, '0', STR_PAD_LEFT) . '-' . str_pad((string) $bill->id, 4, '0', STR_PAD_LEFT);

                    TuitionPayment::firstOrCreate(
                        ['tuition_bill_id' => $bill->id],
                        [
                            'verified_by' => $admin->id,
                            'receipt_number' => $receiptCode,
                            'amount_paid' => 150000,
                            'payment_method' => $isCash ? PaymentMethod::TunaiMadrasah : PaymentMethod::TransferBank,
                            'bank_sender_name' => $isCash ? null : $student->parent->name,
                            'payment_date' => "2026-{$mNum}-05",
                            'verified_at' => now(),
                        ]
                    );
                } elseif ($status === TuitionStatus::PendingVerification && ! $bill->payment) {
                    TuitionPayment::firstOrCreate(
                        ['tuition_bill_id' => $bill->id],
                        [
                            'receipt_number' => 'PENDING-' . $bill->id,
                            'amount_paid' => 150000,
                            'payment_method' => PaymentMethod::TransferBank,
                            'bank_sender_name' => $student->parent->name . ' (BSI Syariah)',
                            'payment_date' => now()->toDateString(),
                        ]
                    );
                }
            }
        }

        // 11. School Galleries
        $galleriesData = [
            [
                'title' => 'Munaqasyah & Wisuda Tahfidz Al-Qur\'an Juz 30',
                'category' => GalleryCategory::Prestasi,
                'image_path' => 'images/gallery/tahfidz.svg',
            ],
            [
                'title' => 'Pembiasaan Sholat Dhuha Berjamaah di Masjid Madrasah',
                'category' => GalleryCategory::Fasilitas,
                'image_path' => 'images/gallery/dhuha.svg',
            ],
            [
                'title' => 'Grup Seni Hadrah Rebana Santri Kalipucung',
                'category' => GalleryCategory::HadrahRebana,
                'image_path' => 'images/gallery/hadrah.svg',
            ],
            [
                'title' => 'Kemah Bhakti & Latihan Pramuka Madrasah (Persami)',
                'category' => GalleryCategory::Pramuka,
                'image_path' => 'images/gallery/pramuka.svg',
            ],
            [
                'title' => 'Praktikum Sains Sederhana & Eksperimen Anak',
                'category' => GalleryCategory::Prestasi,
                'image_path' => 'images/gallery/sains.svg',
            ],
            [
                'title' => 'Peringatan Hari Santri & Santunan 100 Anak Yatim',
                'category' => GalleryCategory::SeniPentas,
                'image_path' => 'images/gallery/santunan.svg',
            ],
        ];

        foreach ($galleriesData as $g) {
            SchoolGallery::firstOrCreate(
                ['title' => $g['title']],
                ['category' => $g['category'], 'image_path' => $g['image_path']]
            );
        }

        // 12. Articles (Berita & Informasi Madrasah)
        $articlesData = [
            [
                'title' => 'Penerimaan Peserta Didik Baru (PPDB) 2026/2027 Resmi Dibuka Secara Online',
                'slug' => 'ppdb-2026-2027-resmi-dibuka',
                'content' => '<p>Alhamdulillah, pendaftaran calon santri baru MI Plus Raudlatul Muta\'allimin Kalipucung Blitar Tahun Ajaran 2026/2027 resmi dibuka mulai 1 Oktober 2026. Pendaftaran dapat dilakukan langsung melalui portal PPDB online tanpa harus datang ke madrasah. Calon wali murid cukup mengisi biodata santri dan mengunggah berkas Akta Kelahiran serta Kartu Keluarga. Kuota santri terbatas untuk 2 rombongan belajar.</p>',
                'is_published' => true,
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Prestasi Membanggakan: Santri MI Plus Raih Juara 1 Munaqasyah Tahfidz Tingkat Kabupaten',
                'slug' => 'prestasi-santri-juara-1-tahfidz-kabupaten-blitar',
                'content' => '<p>Santri MI Plus Raudlatul Muta\'allimin kembali menorehkan prestasi gemilang pada ajang Festival Anak Sholeh dan Munaqasyah Tahfidz se-Kabupaten Blitar. Ananda Muhammad Bilal Al-Fatih meraih peringkat pertama pada cabang Tahfidz Juz 30 berkat kefasihan makharijul huruf dan ketepatan kaidah hukum tajwid.</p>',
                'is_published' => true,
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Pemberian Santunan Muharram dan Gerakan Infaq Berkah Madrasah',
                'slug' => 'pemberian-santunan-muharram-dan-infaq-berkah',
                'content' => '<p>Dalam rangka menyambut tahun baru Islam 1448 H, keluarga besar MI Plus Raudlatul Muta\'allimin mengadakan kegiatan santunan untuk 100 anak yatim dan dhuafa di lingkungan Sanankulon. Dana santunan terkumpul dari gerakan kotak amal Jumat berkah yang rutin disisihkan oleh para santri dan wali murid.</p>',
                'is_published' => true,
                'published_at' => now()->subDay(),
            ],
        ];

        foreach ($articlesData as $art) {
            Article::firstOrCreate(
                ['slug' => $art['slug']],
                [
                    'author_id' => $admin->id,
                    'title' => $art['title'],
                    'content' => $art['content'],
                    'is_published' => $art['is_published'],
                    'published_at' => $art['published_at'],
                ]
            );
        }

        // 13. Pengumuman Resmi Madrasah
        $announcementsData = [
            [
                'title' => 'Jadwal Penilaian Akhir Semester (PAS) Ganjil Tahun Ajaran 2026/2027',
                'content' => 'Diberitahukan kepada seluruh wali murid bahwa Penilaian Akhir Semester (PAS) Ganjil akan dilaksanakan mulai tanggal 1 hingga 10 Desember 2026. Diharapkan bimbingan dan doa orang tua di rumah agar ananda dapat mengikuti ujian dengan tertib.',
                'target' => AnnouncementTarget::All,
                'valid_until' => now()->addDays(30)->toDateString(),
            ],
            [
                'title' => 'Pengambilan Seragam dan Atribut Santri Baru Gelombang 1',
                'content' => 'Bagi calon wali santri yang telah dinyatakan Lolos Berkas pada PPDB 2026/2027, pengambilan seragam madrasah, peci, dan jilbab dapat dilakukan di ruang Tata Usaha setiap hari kerja pukul 08.00 - 12.00 WIB.',
                'target' => AnnouncementTarget::Parent,
                'valid_until' => now()->addDays(15)->toDateString(),
            ],
            [
                'title' => 'Sosialisasi Kurikulum Madrasah dan Pembagian Raport Tengah Semester',
                'content' => 'Pertemuan wali murid dan dewan asatidz terkait evaluasi capaian hafalan Al-Qur\'an Juz 30 dan perkembangan akademik semester berjalan.',
                'target' => AnnouncementTarget::Parent,
                'valid_until' => now()->addDays(20)->toDateString(),
            ],
        ];

        foreach ($announcementsData as $ann) {
            Announcement::firstOrCreate(
                ['title' => $ann['title']],
                [
                    'content' => $ann['content'],
                    'target_audience' => $ann['target'],
                    'valid_until' => $ann['valid_until'],
                ]
            );
        }

        // 14. Sample PPDB Registrations
        $ppdbSamples = [
            [
                'reg_num' => 'PPDB-2026-0001',
                'child_name' => 'Aisyah Putri Azzahra',
                'nik' => '3505016543210001',
                'gender' => 'P',
                'b_place' => 'Blitar',
                'b_date' => '2019-08-20',
                'prev_school' => 'TK Islam Al-Ittihad Sananwetan',
                'p_name' => 'Budi Santoso',
                'p_wa' => '081234567890',
                'p_job' => 'Wiraswasta',
                'addr' => 'Jl. Melati No. 12, Kepanjenkidul, Blitar',
                'status' => PpdbStatus::Accepted,
                'notes' => 'Lolos verifikasi berkas dan tes wawancara hafalan surat pendek.',
            ],
            [
                'reg_num' => 'PPDB-2026-0002',
                'child_name' => 'Zaid bin Tsabit',
                'nik' => '3505011122330002',
                'gender' => 'L',
                'b_place' => 'Kediri',
                'b_date' => '2019-02-10',
                'prev_school' => 'RA Perwanida Blitar',
                'p_name' => 'Muhammad Tsabit',
                'p_wa' => '081298765432',
                'p_job' => 'PNS Kemenag',
                'addr' => 'Jl. Lawu No. 8, Sukorejo, Blitar',
                'status' => PpdbStatus::Verified,
                'notes' => 'Berkas Akta dan KK lengkap dan valid.',
            ],
            [
                'reg_num' => 'PPDB-2026-0003',
                'child_name' => 'Muhammad Rayyan Al-Ghifari',
                'nik' => '3505017788990003',
                'gender' => 'L',
                'b_place' => 'Blitar',
                'b_date' => '2019-04-18',
                'prev_school' => 'TK Kusuma Bangsa',
                'p_name' => 'Agus Hermanto',
                'p_wa' => '081333444555',
                'p_job' => 'Karyawan Swasta',
                'addr' => 'Jl. Ciliwung No. 44, Blitar',
                'status' => PpdbStatus::Pending,
                'notes' => null,
            ],
            [
                'reg_num' => 'PPDB-2026-0004',
                'child_name' => 'Nabila Khairunnisa',
                'nik' => '3505018899000004',
                'gender' => 'P',
                'b_place' => 'Tulungagung',
                'b_date' => '2019-07-25',
                'prev_school' => 'RA Mambaul Ulum',
                'p_name' => 'Dwi Prasetyo',
                'p_wa' => '081255667788',
                'p_job' => 'Pedagang',
                'addr' => 'Dusun Bendosari, Sanankulon, Blitar',
                'status' => PpdbStatus::Pending,
                'notes' => null,
            ],
            [
                'reg_num' => 'PPDB-2026-0005',
                'child_name' => 'Kenzie Raditya Pratama',
                'nik' => '3505013344550005',
                'gender' => 'L',
                'b_place' => 'Blitar',
                'b_date' => '2020-09-10',
                'prev_school' => 'PAUD Melati Indah',
                'p_name' => 'Eko Prasetyo',
                'p_wa' => '081233445566',
                'p_job' => 'Buruh',
                'addr' => 'Jl. Kenari No. 19, Blitar',
                'status' => PpdbStatus::Rejected,
                'notes' => 'Usia calon santri belum mencukupi batas minimal 6 tahun per 1 Juli 2026.',
            ],
            [
                'reg_num' => 'PPDB-2026-0006',
                'child_name' => 'Hanifah Sholihatunnisa',
                'nik' => '3505014455660006',
                'gender' => 'P',
                'b_place' => 'Blitar',
                'b_date' => '2019-03-12',
                'prev_school' => 'TK Dharma Wanita',
                'p_name' => 'Bambang Sudibyo',
                'p_wa' => '081244556677',
                'p_job' => 'Guru',
                'addr' => 'Jl. Dr. Sutomo No. 5, Blitar',
                'status' => PpdbStatus::Accepted,
                'notes' => 'Lolos seleksi, berkas lengkap.',
            ],
        ];

        foreach ($ppdbSamples as $p) {
            PpdbRegistration::firstOrCreate(
                ['registration_number' => $p['reg_num']],
                [
                    'child_name' => $p['child_name'],
                    'nik' => $p['nik'],
                    'gender' => $p['gender'],
                    'birth_place' => $p['b_place'],
                    'birth_date' => $p['b_date'],
                    'previous_school' => $p['prev_school'],
                    'parent_name' => $p['p_name'],
                    'parent_whatsapp' => $p['p_wa'],
                    'parent_job' => $p['p_job'],
                    'address' => $p['addr'],
                    'file_birth_certificate' => 'ppdb/akta/sample_akta.pdf',
                    'file_family_card' => 'ppdb/kk/sample_kk.pdf',
                    'status' => $p['status'],
                    'admin_notes' => $p['notes'],
                ]
            );
        }
    }
}
