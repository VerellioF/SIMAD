<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\PpdbStatus;
use App\Enums\UserRole;
use App\Exports\PpdbEmisExport;
use App\Models\PpdbRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class AdminPpdbResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_ppdb_registrations_resource(): void
    {
        $admin = User::create([
            'name' => 'Staf TU Admin',
            'phone_number' => '081299990011',
            'password' => bcrypt('password'),
            'role' => UserRole::Admin,
        ]);

        PpdbRegistration::create([
            'registration_number' => 'PPDB-2026-0001',
            'child_name' => 'Santri Terdaftar',
            'nik' => '3505012304190001',
            'gender' => 'L',
            'birth_place' => 'Blitar',
            'birth_date' => '2019-01-01',
            'previous_school' => 'RA Mambaul Ulum',
            'parent_name' => 'Wali Santri',
            'parent_whatsapp' => '081234567890',
            'address' => 'Kalipucung, Blitar',
            'file_birth_certificate' => 'ppdb/akta.pdf',
            'file_family_card' => 'ppdb/kk.pdf',
            'status' => PpdbStatus::Pending,
        ]);

        $response = $this->actingAs($admin)->get('/admin/ppdb-registrations');

        $response->assertOk();
        $response->assertSeeText('PPDB-2026-0001');
        $response->assertSeeText('Santri Terdaftar');
    }

    public function test_non_admin_cannot_access_ppdb_resource(): void
    {
        $teacher = User::create([
            'name' => 'Guru Kelas',
            'phone_number' => '081299990012',
            'password' => bcrypt('password'),
            'role' => UserRole::Teacher,
        ]);

        $this->actingAs($teacher)->get('/admin/ppdb-registrations')->assertForbidden();
    }

    public function test_emis_export_returns_download(): void
    {
        Excel::fake();

        $admin = User::create([
            'name' => 'Staf TU Admin',
            'phone_number' => '081299990013',
            'password' => bcrypt('password'),
            'role' => UserRole::Admin,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.ppdb.export_emis'));

        $response->assertOk();
        Excel::assertDownloaded('data-ppdb-emis.xlsx');
    }
}
