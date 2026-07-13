<?php

namespace App\Livewire\Participant\Evaluations;

use App\Models\Evaluation;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Evaluación | AICROOM')]
class Show extends Component
{
    /**
     * El atributo Locked evita que el ID pueda alterarse
     * desde el navegador.
     */
    #[Locked]
    public int $evaluationId;

    /**
     * Inicializa la página.
     */
    public function mount(Evaluation $evaluation): void
    {
        Gate::authorize(
            'view',
            $evaluation
        );

        /*
         * Actualiza el estado si el tiempo terminó.
         */
        if (
            $evaluation->isInProgress()
            && $evaluation->hasExpired()
        ) {
            $evaluation->markAsExpired();
        }

        $this->evaluationId = $evaluation->id;
    }

    /**
     * Renderiza la preparación de la evaluación.
     */
    public function render(): View
    {
        $evaluation = Evaluation::query()
            ->with([
                'questionnaireVersion.questionnaire',
            ])
            ->findOrFail($this->evaluationId);

        Gate::authorize(
            'view',
            $evaluation
        );

        return view(
            'livewire.participant.evaluations.show',
            [
                'evaluation' => $evaluation,
            ],
        );
    }
}
