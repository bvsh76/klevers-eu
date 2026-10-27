<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    private function user(Role $role): User
    {
        $user = User::factory()->create(['email' => 'admin@example.com', 'password' => 'secret-pass']);
        $user->role = $role;
        $user->save();

        return $user;
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login'));
        $this->get('/admin/login')->assertOk()->assertSee('Вход в админку')->assertSee('noindex', false);
    }

    public function test_admin_can_log_in_and_see_dashboard(): void
    {
        $this->user(Role::Admin);

        $this->post('/admin/login', ['email' => 'Admin@Example.com', 'password' => 'secret-pass'])
            ->assertRedirect(route('admin.dashboard'));

        $this->get('/admin')->assertOk()->assertSee('Дашборд')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_wrong_password_is_rejected(): void
    {
        $this->user(Role::Admin);

        $this->from('/admin/login')
            ->post('/admin/login', ['email' => 'admin@example.com', 'password' => 'wrong'])
            ->assertRedirect('/admin/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_client_role_cannot_access_admin(): void
    {
        $this->user(Role::Client);

        $this->post('/admin/login', ['email' => 'admin@example.com', 'password' => 'secret-pass'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->actingAs(User::first())->get('/admin')->assertForbidden();
    }

    public function test_logout(): void
    {
        $this->actingAs($this->user(Role::Admin))
            ->post('/admin/logout')
            ->assertRedirect(route('admin.login'));

        $this->assertGuest();
    }

    public function test_login_is_throttled_after_five_failures(): void
    {
        $this->user(Role::Admin);

        $this->freezeTime();

        foreach (range(1, 5) as $i) {
            $this->post('/admin/login', ['email' => 'admin@example.com', 'password' => 'wrong']);
        }

        $this->post('/admin/login', ['email' => 'admin@example.com', 'password' => 'secret-pass'])
            ->assertSessionHasErrors(['email' => 'Слишком много попыток. Повторите через 60 с.']);
        $this->assertGuest();
    }
}
