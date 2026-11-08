<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\PpdbStatus;
use App\Enums\UserRole;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\PpdbRegistration;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomPortalAccessTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $teacher;
    protected User $parent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Staf TU',
            'email' => 'admin@simad.id',
            'phone_number' => '081200000001',
            'role' => UserRole::Admin,
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->teacher = User::create([
            'name' => 'Guru Kelas',
            'email' => 'guru@simad.id',
            'phone_number' => '081200000002',
            'role' => UserRole::Teacher,
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->parent = User::create([
            'name' => 'Wali Murid',
            'email' => 'wali@simad.id',
            'phone_number' => '081200000003',
            'role' => UserRole::Parent,
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
    }

    public function test_guest_is_redirected_to_login_from_all_portals(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->get('/guru')->assertRedirect('/login');
        $this->get('/wali')->assertRedirect('/login');
    }

    public function test_login_page_renders_cleanly(): void
    {
        $response = $this->get('/login');
        $response->assertOk();
        $response->assertSee('Masuk Portal Sistem');
        $response->assertSee('Admin / TU');
        $response->assertSee('Guru Kelas');
        $response->assertSee('Wali Murid');
    }

    public function test_user_can_login_with_email_and_redirects_to_correct_portal(): void
    {
        $response = $this->post('/login', [
            'login' => 'admin@simad.id',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_user_can_login_with_phone_number_and_redirects_to_correct_portal(): void
    {
        $response = $this->post('/login', [
            'login' => '081200000002',
            'password' => 'password',
        ]);

        $response->assertRedirect('/guru');
        $this->assertAuthenticatedAs($this->teacher);
    }

    public function test_inactive_user_cannot_login(): void
    {
        $this->admin->update(['is_active' => false]);

        $response = $this->post('/login', [
            'login' => 'admin@simad.id',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_admin_portal_access_permissions(): void
    {
        $this->actingAs($this->admin)->get('/admin')->assertOk();
        $this->actingAs($this->admin)->get('/guru')->assertForbidden();
        $this->actingAs($this->admin)->get('/wali')->assertForbidden();
    }

    public function test_teacher_portal_access_permissions(): void
    {
        $this->actingAs($this->teacher)->get('/guru')->assertOk();
        $this->actingAs($this->teacher)->get('/admin')->assertForbidden();
        $this->actingAs($this->teacher)->get('/wali')->assertForbidden();
    }

    public function test_parent_portal_access_permissions(): void
    {
        $this->actingAs($this->parent)->get('/wali')->assertOk();
        $this->actingAs($this->parent)->get('/admin')->assertForbidden();
        $this->actingAs($this->parent)->get('/guru')->assertForbidden();
    }

    public function test_admin_can_manage_ppdb_registrations(): void
    {
        $registration = PpdbRegistration::create([
            'registration_number' => 'PPDB-2026-9999',
            'child_name' => 'Fulan bin Fulan',
            'nik' => '3505019999990001',
            'gender' => 'L',
            'birth_place' => 'Blitar',
            'birth_date' => '2019-01-01',
            'previous_school' => 'RA Mambaul Ulum',
            'parent_name' => 'Fulan Senior',
            'parent_whatsapp' => '081299999999',
            'address' => 'Kalipucung, Blitar',
            'file_birth_certificate' => 'ppdb/akta/sample.pdf',
            'file_family_card' => 'ppdb/kk/sample.pdf',
            'status' => PpdbStatus::Pending,
        ]);

        // List PPDB
        $this->actingAs($this->admin)
            ->get('/admin/ppdb')
            ->assertOk()
            ->assertSee('PPDB-2026-9999')
            ->assertSee('Fulan bin Fulan');

        // View Detail PPDB
        $this->actingAs($this->admin)
            ->get("/admin/ppdb/{$registration->id}")
            ->assertOk()
            ->assertSee('Fulan bin Fulan')
            ->assertSee('3505019999990001');

        // Update status to verified
        $this->actingAs($this->admin)
            ->post("/admin/ppdb/{$registration->id}/status", [
                'status' => 'verified',
                'admin_notes' => 'Dokumen sah dan terverifikasi.',
            ])
            ->assertRedirect();

        $this->assertEquals(PpdbStatus::Verified, $registration->fresh()->status);
        $this->assertEquals('Dokumen sah dan terverifikasi.', $registration->fresh()->admin_notes);

        // Export EMIS
        $this->actingAs($this->admin)
            ->get('/admin/export/ppdb-emis')
            ->assertOk();
    }
}
