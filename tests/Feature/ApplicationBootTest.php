<?php

namespace Tests\Feature;

use App\Livewire\Pages\Home;
use Livewire\Livewire;
use Tests\TestCase;

class ApplicationBootTest extends TestCase
{
    /**
     * Comprueba que la página principal esté disponible.
     */
    public function test_home_page_is_available(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('AICROOM V2')
            ->assertSee('Base visual configurada')
            ->assertSee('Livewire 4')
            ->assertSee('Flux UI 2')
            ->assertSee('Filament 5');
    }

    /**
     * Comprueba directamente el componente Livewire.
     */
    public function test_home_livewire_component_can_render(): void
    {
        Livewire::test(Home::class)
            ->assertStatus(200)
            ->assertSee('Base visual configurada');
    }

    /**
     * Comprueba que la pantalla de acceso administrativo exista.
     */
    public function test_admin_login_page_is_available(): void
    {
        $this->get('/admin/login')
            ->assertOk();
    }

    /**
     * Comprueba el endpoint de salud de Laravel.
     */
    public function test_health_endpoint_is_available(): void
    {
        $this->get('/up')
            ->assertOk();
    }
}
