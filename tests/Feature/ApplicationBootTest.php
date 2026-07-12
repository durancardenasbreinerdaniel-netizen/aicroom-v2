<?php

namespace Tests\Feature;

use App\Livewire\Pages\Home;
use Livewire\Livewire;
use Tests\TestCase;

class ApplicationBootTest extends TestCase
{
    public function test_home_page_is_available(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('AICROOM V2');
    }

    public function test_home_livewire_component_can_render(): void
    {
        Livewire::test(Home::class)
            ->assertStatus(200);
    }

    public function test_health_endpoint_is_available(): void
    {
        $this->get('/up')
            ->assertOk();
    }
}
