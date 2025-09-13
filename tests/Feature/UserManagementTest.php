<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create permissions for web guard
        $permissions = ['create users', 'read users', 'edit users', 'delete users', 'show users'];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create admin role and assign permissions
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo($permissions);
    }

    public function test_admin_can_access_users_page(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_create_user_page(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/admin/users/create');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_edit_user_page(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();

        $response = $this->actingAs($admin)->get("/admin/users/{$user->id}/edit");

        $response->assertStatus(200);
    }

    public function test_admin_can_view_user_details(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();

        $response = $this->actingAs($admin)->get("/admin/users/{$user->id}");

        $response->assertStatus(200);
    }
}
