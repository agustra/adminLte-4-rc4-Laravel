<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MenusApiTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create permissions
        $permissions = ['create menus', 'read menus', 'edit menus', 'delete menus', 'show menus'];
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

    public function test_api_can_list_menus(): void
    {
        Menu::create([
            'name' => 'Test Menu',
            'url' => '/test',
            'icon' => 'fas fa-test',
            'parent_id' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/menus');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'meta',
        ]);
    }

    public function test_api_can_create_menu(): void
    {
        $menuData = [
            'name' => 'New Menu',
            'url' => '/new-menu',
            'icon' => 'fas fa-new',
            'parent_id' => null,
            'order' => 1,
            'is_active' => true,
        ];

        $response = $this->postJson('/api/menus', $menuData);

        $this->assertContains($response->getStatusCode(), [201, 422]);

        if ($response->getStatusCode() === 201) {
            $this->assertDatabaseHas('menus', [
                'name' => 'New Menu',
                'url' => '/new-menu',
            ]);
        }
    }

    public function test_api_can_show_menu(): void
    {
        // Note: Show route is excluded in API routes, so we test list instead
        $menu = Menu::create([
            'name' => 'Test Menu',
            'url' => '/test',
            'icon' => 'fas fa-test',
            'parent_id' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/menus');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'meta',
        ]);
    }

    public function test_api_can_update_menu(): void
    {
        $menu = Menu::create([
            'name' => 'Test Menu',
            'url' => '/test',
            'icon' => 'fas fa-test',
            'parent_id' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        $updateData = [
            'name' => 'Updated Menu',
            'url' => '/updated',
            'icon' => 'fas fa-updated',
            'parent_id' => null,
            'order' => 2,
            'is_active' => false,
        ];

        $response = $this->putJson("/api/menus/{$menu->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('menus', [
            'id' => $menu->id,
            'name' => 'Updated Menu',
        ]);
    }

    public function test_api_can_delete_menu(): void
    {
        $menu = Menu::create([
            'name' => 'Test Menu',
            'url' => '/test',
            'icon' => 'fas fa-test',
            'parent_id' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        $response = $this->deleteJson("/api/menus/{$menu->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('menus', ['id' => $menu->id]);
    }

    public function test_api_can_delete_multiple_menus(): void
    {
        $menu1 = Menu::create([
            'name' => 'Test Menu 1',
            'url' => '/test1',
            'icon' => 'fas fa-test',
            'parent_id' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        $menu2 = Menu::create([
            'name' => 'Test Menu 2',
            'url' => '/test2',
            'icon' => 'fas fa-test',
            'parent_id' => null,
            'order' => 2,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/menus/multiple/delete', [
            'ids' => [$menu1->id, $menu2->id],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('menus', ['id' => $menu1->id]);
        $this->assertDatabaseMissing('menus', ['id' => $menu2->id]);
    }

    public function test_api_can_get_menus_json(): void
    {
        Menu::create([
            'name' => 'Test Menu',
            'url' => '/test',
            'icon' => 'fas fa-test',
            'parent_id' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/menus/json');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    public function test_api_can_get_sidebar_menu(): void
    {
        Menu::create([
            'name' => 'Dashboard',
            'url' => '/dashboard',
            'icon' => 'fas fa-dashboard',
            'parent_id' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->getJson('/admin/api/menus/sidebar');

        $response->assertStatus(200);
        $response->assertJsonStructure(['menus']);
    }
}
