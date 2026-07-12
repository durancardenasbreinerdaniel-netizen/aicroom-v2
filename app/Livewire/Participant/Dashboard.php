<?php

namespace App\Livewire\Participant;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Mi panel | AICROOM')]
class Dashboard extends Component
{
    /**
     * Renderiza el panel inicial del participante.
     */
    public function render(): View
    {
        return view('livewire.participant.dashboard');
    }
}
