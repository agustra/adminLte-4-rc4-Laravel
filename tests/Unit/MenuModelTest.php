<?php

namespace Tests\Unit;

use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_menu_can_be_created(): void
    {
        $menu = Menu::create([
            'name' => 'Test Menu',
            'url' => '/test',
            'icon' => 'fas fa-test',
            'parent_id' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        $this->assertInstanceOf(Menu::class, $menu);
        $this->assertEquals('Test Menu', $menu->name);
        $this->assertEquals('/test', $menu->url);
        $this->assertTrue($menu->is_active);
    }

    public function test_menu_has_fillable_attributes(): void
    {
        $menu = new Menu;
        $fillable = $menu->getFillable();

        $expectedFillable = ['name', 'url', 'icon', 'parent_id', 'order', 'is_active'];

        foreach ($expectedFillable as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    public function test_menu_can_have_parent(): void
    {
        $parentMenu = Menu::create([
            'name' => 'Parent Menu',
            'url' => '/parent',
            'icon' => 'fas fa-parent',
            'parent_id' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        $childMenu = Menu::create([
            'name' => 'Child Menu',
            'url' => '/child',
            'icon' => 'fas fa-child',
            'parent_id' => $parentMenu->id,
            'order' => 1,
            'is_active' => true,
        ]);

        $this->assertEquals($parentMenu->id, $childMenu->parent_id);
    }

    public function test_menu_can_have_children(): void
    {
        $parentMenu = Menu::create([
            'name' => 'Parent Menu',
            'url' => '/parent',
            'icon' => 'fas fa-parent',
            'parent_id' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        Menu::create([
            'name' => 'Child Menu 1',
            'url' => '/child1',
            'icon' => 'fas fa-child',
            'parent_id' => $parentMenu->id,
            'order' => 1,
            'is_active' => true,
        ]);

        Menu::create([
            'name' => 'Child Menu 2',
            'url' => '/child2',
            'icon' => 'fas fa-child',
            'parent_id' => $parentMenu->id,
            'order' => 2,
            'is_active' => true,
        ]);

        // Assuming there's a children relationship in the Menu model
        if (method_exists($parentMenu, 'children')) {
            $this->assertEquals(2, $parentMenu->children()->count());
        } else {
            // If no relationship exists, just check the database
            $childrenCount = Menu::where('parent_id', $parentMenu->id)->count();
            $this->assertEquals(2, $childrenCount);
        }
    }

    public function test_menu_is_active_can_be_set(): void
    {
        $menu = Menu::create([
            'name' => 'Test Menu',
            'url' => '/test',
            'icon' => 'fas fa-test',
            'parent_id' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        $this->assertTrue($menu->is_active);

        $inactiveMenu = Menu::create([
            'name' => 'Inactive Menu',
            'url' => '/inactive',
            'icon' => 'fas fa-inactive',
            'parent_id' => null,
            'order' => 2,
            'is_active' => false,
        ]);

        $this->assertFalse($inactiveMenu->is_active);
    }

    public function test_menu_order_is_numeric(): void
    {
        $menu = Menu::create([
            'name' => 'Test Menu',
            'url' => '/test',
            'icon' => 'fas fa-test',
            'parent_id' => null,
            'order' => 5,
            'is_active' => true,
        ]);

        $this->assertIsNumeric($menu->order);
        $this->assertEquals(5, $menu->order);
    }
}
