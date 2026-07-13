<?php

namespace App\Livewire\Participant\Evaluations;

use App\Actions\Evaluations\StartEvaluation;
use App\Models\Evaluation;
use App\Models\Questionnaire;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Evaluaciones | AICROOM')]
class Index extends Component
{
    /**
     * Inicia una evaluación desde la versión publicada.
     */
    public function start(
        int $questionnaireId,
        StartEvaluation $startEvaluation
    ): mixed {
        Gate::authorize(
            'create',
            Evaluation::class
        );

        $questionnaire = Questionnaire::query()
            ->active()
            ->whereKey($questionnaireId)
            ->whereHas('publishedVersion')
            ->with('publishedVersion')
            ->firstOrFail();

        $evaluation = $startEvaluation->execute(
            auth()->user(),
            $questionnaire->publishedVersion
        );

        return $this->redirectRoute(
            'evaluations.show',
            [
                'evaluation' => $evaluation,
            ],
            navigate: true,
        );
    }

    /**
     * Renderiza los cuestionarios disponibles.
     */
    public function render(): View
    {
        $questionnaires = Questionnaire::query()
            ->active()
            ->whereHas('publishedVersion')
            ->with([
                'publishedVersion:id,questionnaire_id,version_number,questions_per_evaluation,time_limit_minutes,status',
            ])
            ->orderBy('name')
            ->get();

        return view(
            'livewire.participant.evaluations.index',
            [
                'questionnaires' => $questionnaires,
            ],
        );
    }
}
