<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Selamat Datang Kembali');
    }

    public function test_cashier_can_authenticate_using_username(): void
    {
        $user = User::factory()->create([
            'username' => 'kasir',
            'password' => Hash::make('kasir123'),
            'role' => 'kasir',
        ]);

        $response = $this->post('/login', [
            'login' => 'kasir',
            'password' => 'kasir123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_cashier_can_authenticate_using_email(): void
    {
        $user = User::factory()->create([
            'email' => 'kasir@kopikita.com',
            'password' => Hash::make('kasir123'),
            'role' => 'kasir',
        ]);

        $response = $this->post('/login', [
            'login' => 'kasir@kopikita.com',
            'password' => 'kasir123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        User::factory()->create([
            'username' => 'kasir',
            'password' => Hash::make('kasir123'),
            'role' => 'kasir',
        ]);

        $response = $this->post('/login', [
            'login' => 'kasir',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('login');
    }

    public function test_authenticated_cashier_can_logout(): void
    {
        $user = User::factory()->create([
            'role' => 'kasir',
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }
}
