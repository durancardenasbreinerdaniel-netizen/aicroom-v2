<?php

namespace App\Livewire\Participant\Results;

use App\Enums\EvaluationStatus;
use App\Models\Evaluation;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Resultado | AICROOM')]
class Show extends Component
{
    /**
     * ID interno protegido contra modificaciones.
     */
    #[Locked]
    public int $evaluationId;

    /**
     * Comprueba el acceso al resultado.
     */
    public function mount(
        Evaluation $evaluation,
    ): void {
        Gate::authorize(
            'view',
            $evaluation,
        );

        abort_unless(
            $evaluation->status
                === EvaluationStatus::SUBMITTED,
            404,
        );

        abort_if(
            $evaluation->result()->doesntExist(),
            404,
        );

        $this->evaluationId = $evaluation->id;
    }

    /**
     * Renderiza el resultado completo.
     */
    public function render(): View
    {
        $evaluation = Evaluation::query()
            ->with([
                'questionnaireVersion.questionnaire',
                'result.skillResults.skill',
            ])
            ->findOrFail($this->evaluationId);

        Gate::authorize(
            'view',
            $evaluation,
        );

        return view(
            'livewire.participant.results.show',
            [
                'evaluation' => $evaluation,
                'result' => $evaluation->result,
            ],
        );
    }
}
