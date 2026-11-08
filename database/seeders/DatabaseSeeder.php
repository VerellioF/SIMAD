<?php

namespace Database\Seeders;

use App\Enums\PpdbStatus;
use App\Enums\Semester;
use App\Enums\StudentStatus;
use App\Enums\UserRole;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\PpdbRegistration;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. Core Users
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

        $teacher = User::firstOrCreate(
            ['email' => 'guru@simad.id'],
            [
                'name' => 'Ustadzah Fatimah, S.Pd.',
                'phone_number' => '081200000002',
                'role' => UserRole::Teacher,
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        $parent = User::firstOrCreate(
            ['email' => 'wali@simad.id'],
            [
                'name' => 'Ahmad Dahlan (Wali)',
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
                'is_active' => true,
            ]
        );

        // 3. Classroom
        $classroom = Classroom::firstOrCreate(
            ['name' => 'Kelas 1-A Abu Bakar'],
            [
                'academic_year_id' => $academicYear->id,
                'homeroom_teacher_id' => $teacher->id,
                'grade_level' => 1,
            ]
        );

        // 4. Student linked to Parent
        $student = Student::firstOrCreate(
            ['nisn' => '3123456789'],
            [
                'parent_id' => $parent->id,
                'classroom_id' => $classroom->id,
                'nis' => '2026001',
                'full_name' => 'Muhammad Bilal Al-Fatih',
                'nickname' => 'Bilal',
                'gender' => 'L',
                'birth_place' => 'Blitar',
                'birth_date' => '2019-05-15',
                'status' => StudentStatus::Active,
                'address' => 'Jl. Merdeka No. 45, Sananwetan, Blitar',
            ]
        );

        // 5. Sample PPDB Registrations
        PpdbRegistration::firstOrCreate(
            ['registration_number' => 'PPDB-2026-0001'],
            [
                'child_name' => 'Aisyah Putri Azzahra',
                'nik' => '3505016543210001',
                'gender' => 'P',
                'birth_place' => 'Blitar',
                'birth_date' => '2019-08-20',
                'previous_school' => 'TK Islam Al-Ittihad',
                'parent_name' => 'Budi Santoso',
                'parent_whatsapp' => '081234567890',
                'parent_job' => 'Wiraswasta',
                'address' => 'Jl. Melati No. 12, Kepanjenkidul, Blitar',
                'file_birth_certificate' => 'ppdb/akta/sample_akta.pdf',
                'file_family_card' => 'ppdb/kk/sample_kk.pdf',
                'status' => PpdbStatus::Pending,
            ]
        );

        PpdbRegistration::firstOrCreate(
            ['registration_number' => 'PPDB-2026-0002'],
            [
                'child_name' => 'Zaid bin Tsabit',
                'nik' => '3505011122330002',
                'gender' => 'L',
                'birth_place' => 'Kediri',
                'birth_date' => '2019-02-10',
                'previous_school' => 'RA Perwanida Blitar',
                'parent_name' => 'Muhammad Tsabit',
                'parent_whatsapp' => '081298765432',
                'parent_job' => 'PNS',
                'address' => 'Jl. Lawu No. 8, Sukorejo, Blitar',
                'file_birth_certificate' => 'ppdb/akta/sample_akta_2.pdf',
                'file_family_card' => 'ppdb/kk/sample_kk_2.pdf',
                'status' => PpdbStatus::Verified,
                'admin_notes' => 'Berkas lengkap dan sesuai.',
            ]
        );
    }
}
