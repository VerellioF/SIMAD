<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentMultiPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_users_are_redirected_to_panel_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/guru')->assertRedirect('/guru/login');
        $this->get('/wali')->assertRedirect('/wali/login');
    }

    public function test_admin_can_access_admin_panel_only(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'phone_number' => '081299990001',
            'password' => bcrypt('password'),
            'role' => UserRole::Admin,
        ]);

        $this->actingAs($admin)->get('/admin')->assertSuccessful();
        $this->actingAs($admin)->get('/guru')->assertForbidden();
        $this->actingAs($admin)->get('/wali')->assertForbidden();
    }

    public function test_teacher_can_access_teacher_panel_only(): void
    {
        $teacher = User::create([
            'name' => 'Guru User',
            'phone_number' => '081299990002',
            'password' => bcrypt('password'),
            'role' => UserRole::Teacher,
        ]);

        $this->actingAs($teacher)->get('/guru')->assertSuccessful();
        $this->actingAs($teacher)->get('/admin')->assertForbidden();
        $this->actingAs($teacher)->get('/wali')->assertForbidden();
    }

    public function test_parent_can_access_parent_panel_only(): void
    {
        $parent = User::create([
            'name' => 'Wali User',
            'phone_number' => '081299990003',
            'password' => bcrypt('password'),
            'role' => UserRole::Parent,
        ]);

        $this->actingAs($parent)->get('/wali')->assertSuccessful();
        $this->actingAs($parent)->get('/admin')->assertForbidden();
        $this->actingAs($parent)->get('/guru')->assertForbidden();
    }
}
