<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_can_be_rendered(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Iniciar sesión');
    }

    public function test_active_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'participant@example.com',
            'password' => 'Password1',
        ]);

        Livewire::test(Login::class)
            ->set('form.email', 'participant@example.com')
            ->set('form.password', 'Password1')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);

        $this->assertNotNull(
            $user->fresh()->last_login_at
        );
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'participant@example.com',
            'password' => 'Password1',
        ]);

        Livewire::test(Login::class)
            ->set('form.email', 'participant@example.com')
            ->set('form.password', 'IncorrectPassword1')
            ->call('login')
            ->assertHasErrors('form.email');

        $this->assertGuest();
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()
            ->inactive()
            ->create([
                'email' => 'inactive@example.com',
                'password' => 'Password1',
            ]);

        Livewire::test(Login::class)
            ->set('form.email', 'inactive@example.com')
            ->set('form.password', 'Password1')
            ->call('login')
            ->assertHasErrors('form.email');

        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->post('/logout')
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }
}
