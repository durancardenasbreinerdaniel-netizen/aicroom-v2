<?php

namespace App\Livewire\Pages;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Página pública principal de AICROOM.
 *
 * Esta clase representa una página completa manejada por Livewire.
 * En esta fase no contiene lógica de negocio ni acceso a la base
 * de datos.
 */
#[Layout('components.layouts.app')]
#[Title('Inicio | AICROOM')]
class Home extends Component
{
    /**
     * Renderiza la vista principal.
     */
    public function render(): View
    {
        return view('livewire.pages.home');
    }
}
