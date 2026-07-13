<?php

namespace App\Livewire\Participant\Results;

use App\Enums\EvaluationStatus;
use App\Models\Evaluation;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Mis resultados | AICROOM')]
class Index extends Component
{
    /**
     * Renderiza las evaluaciones finalizadas del participante.
     */
    public function render(): View
    {
        $evaluations = Evaluation::query()
            ->ownedBy(auth()->user())
            ->where(
                'status',
                EvaluationStatus::SUBMITTED->value,
            )
            ->whereHas('result')
            ->with([
                'questionnaireVersion.questionnaire',
                'result',
            ])
            ->latest('submitted_at')
            ->get();

        return view(
            'livewire.participant.results.index',
            [
                'evaluations' => $evaluations,
            ],
        );
    }
}
