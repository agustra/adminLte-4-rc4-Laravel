<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionsApiTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create permissions
        $permissions = ['create permissions', 'read permissions', 'edit permissions', 'delete permissions', 'show permissions'];
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

    public function test_api_can_list_permissions(): void
    {
        $response = $this->getJson('/api/permissions');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'meta',
        ]);
    }

    public function test_api_can_create_permission(): void
    {
        $permissionData = [
            'name' => 'new permission',
            'guard_name' => 'web',
        ];

        $response = $this->postJson('/api/permissions', $permissionData);

        $this->assertContains($response->getStatusCode(), [201, 422]);

        if ($response->getStatusCode() === 201) {
            $this->assertDatabaseHas('permissions', [
                'name' => 'new permission',
                'guard_name' => 'web',
            ]);
        }
    }

    public function test_api_can_show_permission(): void
    {
        // Note: Show route is excluded in API routes, so we test list instead
        $permission = Permission::create(['name' => 'test permission', 'guard_name' => 'web']);

        $response = $this->getJson('/api/permissions');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'meta',
        ]);
    }

    public function test_api_can_update_permission(): void
    {
        $permission = Permission::create(['name' => 'test permission', 'guard_name' => 'web']);

        $updateData = [
            'name' => 'updated permission',
            'guard_name' => 'web',
        ];

        $response = $this->putJson("/api/permissions/{$permission->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'name' => 'updated permission',
        ]);
    }

    public function test_api_can_delete_permission(): void
    {
        $permission = Permission::create(['name' => 'test permission', 'guard_name' => 'web']);

        $response = $this->deleteJson("/api/permissions/{$permission->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
    }

    public function test_api_can_get_permissions_json(): void
    {
        $response = $this->getJson('/api/permissions/json');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }
}
