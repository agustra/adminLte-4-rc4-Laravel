<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_page_can_be_rendered(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertSee('Sign in to start your session');
        $response->assertSee('Email');
        $response->assertSee('Password');
    }

    public function test_users_can_authenticate_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200); // Login returns 200
        $this->assertAuthenticatedAs($user);
    }

    public function test_users_cannot_authenticate_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'status' => false,
            'message' => 'Email atau password salah',
        ]);
    }

    public function test_kasir_cannot_login_from_mobile_device(): void
    {
        $user = User::factory()->create([
            'email' => 'kasir@mail.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15',
        ])->post('/admin/login', [
            'email' => 'kasir@mail.com',
            'password' => 'password',
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'status' => false,
            'message' => 'Kasir hanya bisa login melalui PC!',
        ]);
    }

    public function test_logout_redirects_to_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/admin/logout');

        $response->assertRedirect('/dashboard');
    }
}
