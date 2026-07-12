<?php

namespace Tests\Feature\Auth;

use App\Enums\RoleName;
use App\Livewire\Auth\Register;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Prepara los roles necesarios para cada prueba.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_registration_page_can_be_rendered(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('Crear cuenta');
    }

    public function test_participant_can_register(): void
    {
        Livewire::test(Register::class)
            ->set('form.name', 'Ana')
            ->set('form.lastName', 'Pérez')
            ->set('form.email', 'ana@example.com')
            ->set('form.phone', '3001234567')
            ->set('form.password', 'Password1')
            ->set('form.passwordConfirmation', 'Password1')
            ->call('register')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'name' => 'Ana',
            'last_name' => 'Pérez',
            'email' => 'ana@example.com',
        ]);

        $user = auth()->user();

        $this->assertTrue(
            $user->hasRole(RoleName::PARTICIPANT->value)
        );
    }

    public function test_email_must_be_unique(): void
    {
        Livewire::test(Register::class)
            ->set('form.name', 'Ana')
            ->set('form.lastName', 'Pérez')
            ->set('form.email', 'ana@example.com')
            ->set('form.password', 'Password1')
            ->set('form.passwordConfirmation', 'Password1')
            ->call('register')
            ->assertHasNoErrors();

        auth()->logout();

        Livewire::test(Register::class)
            ->set('form.name', 'Otra')
            ->set('form.lastName', 'Persona')
            ->set('form.email', 'ana@example.com')
            ->set('form.password', 'Password1')
            ->set('form.passwordConfirmation', 'Password1')
            ->call('register')
            ->assertHasErrors([
                'form.email' => 'unique',
            ]);
    }
}
