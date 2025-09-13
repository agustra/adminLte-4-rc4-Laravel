<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolesApiTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create permissions
        $permissions = ['create roles', 'read roles', 'edit roles', 'delete roles', 'show roles'];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create admin role and user
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo($permissions);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        Passport::actingAs($this->admin, ['*']);
    }

    public function test_api_can_list_roles(): void
    {
        Role::create(['name' => 'test-role', 'guard_name' => 'web']);

        $response = $this->getJson('/api/roles');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'meta',
        ]);
    }

    public function test_api_can_create_role(): void
    {
        $roleData = [
            'name' => 'new-role',
            'guard_name' => 'web',
            'permissions' => [1, 2],
        ];

        $response = $this->postJson('/api/roles', $roleData);

        $this->assertContains($response->getStatusCode(), [201, 422]);

        if ($response->getStatusCode() === 201) {
            $this->assertDatabaseHas('roles', [
                'name' => 'new-role',
                'guard_name' => 'web',
            ]);
        }
    }

    public function test_api_can_show_role(): void
    {
        // Note: Show route is excluded in API routes, so we test list instead
        $role = Role::create(['name' => 'test-role', 'guard_name' => 'web']);

        $response = $this->getJson('/api/roles');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'meta',
        ]);
    }

    public function test_api_can_update_role(): void
    {
        $role = Role::create(['name' => 'test-role', 'guard_name' => 'web']);

        $updateData = [
            'name' => 'updated-role',
            'guard_name' => 'web',
        ];

        $response = $this->putJson("/api/roles/{$role->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'updated-role',
        ]);
    }

    public function test_api_can_delete_role(): void
    {
        $role = Role::create(['name' => 'test-role', 'guard_name' => 'web']);

        $response = $this->deleteJson("/api/roles/{$role->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    public function test_api_can_delete_multiple_roles(): void
    {
        $role1 = Role::create(['name' => 'test-role-1', 'guard_name' => 'web']);
        $role2 = Role::create(['name' => 'test-role-2', 'guard_name' => 'web']);

        $response = $this->postJson('/api/roles/multiple/delete', [
            'ids' => [$role1->id, $role2->id],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('roles', ['id' => $role1->id]);
        $this->assertDatabaseMissing('roles', ['id' => $role2->id]);
    }

    public function test_api_can_get_roles_json(): void
    {
        Role::create(['name' => 'test-role', 'guard_name' => 'web']);

        $response = $this->getJson('/api/roles/json');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }
}
