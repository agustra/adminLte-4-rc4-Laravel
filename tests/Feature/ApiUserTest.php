<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ApiUserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup Passport for API testing
        Passport::actingAs(
            User::factory()->create(),
            ['*']
        );

        // Create permissions for web guard (as used in actual app)
        $permissions = [
            'create users', 'read users', 'edit users', 'delete users', 'show users',
            'create roles', 'read roles', 'edit roles', 'delete roles', 'show roles',
            'create permissions', 'read permissions', 'edit permissions', 'delete permissions', 'show permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create admin role and assign all permissions
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo($permissions);
    }

    public function test_user_model_can_be_created(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    public function test_admin_role_exists(): void
    {
        $role = Role::where('name', 'admin')->where('guard_name', 'web')->first();
        $this->assertNotNull($role);
    }

    public function test_permissions_exist(): void
    {
        $permission = Permission::where('name', 'read users')->first();
        $this->assertNotNull($permission);
    }
}
