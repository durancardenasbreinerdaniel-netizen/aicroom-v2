<?php

namespace App\Livewire\Participant\Evaluations;

use App\Actions\Evaluations\SaveEvaluationAnswer;
use App\Actions\Evaluations\SubmitEvaluation;
use App\Enums\LikertValue;
use App\Models\Evaluation;
use App\Models\EvaluationItem;
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
     * ID interno protegido contra modificaciones del navegador.
     */
    #[Locked]
    public int $evaluationId;

    /**
     * Posición de la pregunta mostrada.
     */
    public int $currentPosition = 1;

    /**
     * Respuesta seleccionada.
     */
    public ?int $answerValue = null;

    /**
     * Segundos restantes según el servidor.
     */
    public int $remainingSeconds = 0;

    /**
     * Inicializa la evaluación.
     */
    public function mount(Evaluation $evaluation): void
    {
        Gate::authorize('view', $evaluation);

        if (
            $evaluation->isInProgress()
            && $evaluation->hasExpired()
        ) {
            $evaluation->markAsExpired();
        }

        $this->evaluationId = $evaluation->id;
        $this->remainingSeconds = $evaluation->remainingSeconds();

        /*
         * Abre la primera pregunta que todavía no tenga respuesta.
         */
        $firstUnansweredPosition = $evaluation
            ->items()
            ->whereDoesntHave('answer')
            ->orderBy('position')
            ->value('position');

        $this->currentPosition = $firstUnansweredPosition
            ?? max(1, $evaluation->total_questions);

        $this->loadCurrentAnswer();
    }

    /**
     * Guarda automáticamente cuando cambia la opción seleccionada.
     */
    public function updatedAnswerValue(
        mixed $value,
    ): void {
        if ($value === null || $value === '') {
            return;
        }

        $evaluation = $this->getEvaluation();

        $item = $this->getCurrentItem(
            $evaluation,
        );

        app(SaveEvaluationAnswer::class)->execute(
            participant: auth()->user(),
            evaluation: $evaluation,
            item: $item,
            value: (int) $value,
        );

        $this->resetErrorBag('answerValue');
    }

    /**
     * Muestra la pregunta anterior.
     */
    public function previousQuestion(): void
    {
        if ($this->currentPosition <= 1) {
            return;
        }

        $this->currentPosition--;

        $this->loadCurrentAnswer();
    }

    /**
     * Muestra la siguiente pregunta.
     */
    public function nextQuestion(): void
    {
        $evaluation = $this->getEvaluation();

        $currentItem = $this->getCurrentItem(
            $evaluation,
        );

        if (! $currentItem->answer()->exists()) {
            $this->addError(
                'answerValue',
                'Selecciona una respuesta antes de continuar.',
            );

            return;
        }

        if (
            $this->currentPosition
            >= $evaluation->total_questions
        ) {
            return;
        }

        $this->currentPosition++;

        $this->loadCurrentAnswer();
    }

    /**
     * Sincroniza el temporizador con el servidor.
     */
    public function syncTimer(): void
    {
        $evaluation = $this->getEvaluation();

        if (
            $evaluation->isInProgress()
            && $evaluation->hasExpired()
        ) {
            $evaluation->markAsExpired();
        }

        $this->remainingSeconds = $evaluation
            ->refresh()
            ->remainingSeconds();
    }

    /**
     * Finaliza la evaluación y abre el resultado.
     */
    public function submitEvaluation(
        SubmitEvaluation $submitEvaluation,
    ): mixed {
        $evaluation = $this->getEvaluation();

        $submittedEvaluation = $submitEvaluation->execute(
            participant: auth()->user(),
            evaluation: $evaluation,
        );

        return $this->redirectRoute(
            'results.show',
            [
                'evaluation' => $submittedEvaluation,
            ],
            navigate: true,
        );
    }

    /**
     * Devuelve la evaluación autorizada.
     */
    private function getEvaluation(): Evaluation
    {
        $evaluation = Evaluation::query()
            ->findOrFail($this->evaluationId);

        Gate::authorize('view', $evaluation);

        return $evaluation;
    }

    /**
     * Devuelve la pregunta correspondiente a la posición actual.
     */
    private function getCurrentItem(
        Evaluation $evaluation,
    ): EvaluationItem {
        return $evaluation
            ->items()
            ->where(
                'position',
                $this->currentPosition,
            )
            ->firstOrFail();
    }

    /**
     * Carga la respuesta guardada para la pregunta actual.
     */
    private function loadCurrentAnswer(): void
    {
        $evaluation = $this->getEvaluation();

        $item = $this->getCurrentItem(
            $evaluation,
        );

        $answer = $item
            ->answer()
            ->first();

        $this->answerValue = $answer?->answer_value?->value;
        $this->resetErrorBag('answerValue');
    }

    /**
     * Renderiza la evaluación.
     */
    public function render(): View
    {
        $evaluation = Evaluation::query()
            ->with([
                'questionnaireVersion.questionnaire',
                'items.answer',
            ])
            ->withCount('answers')
            ->findOrFail($this->evaluationId);

        Gate::authorize('view', $evaluation);

        $currentItem = $evaluation
            ->items
            ->firstWhere(
                'position',
                $this->currentPosition,
            );

        $progressPercentage = $evaluation->total_questions > 0
            ? (int) round(
                ($evaluation->answers_count / $evaluation->total_questions)
                * 100,
            )
            : 0;

        $minutes = intdiv(
            $this->remainingSeconds,
            60,
        );

        $seconds = $this->remainingSeconds % 60;

        return view(
            'livewire.participant.evaluations.show',
            [
                'evaluation' => $evaluation,
                'currentItem' => $currentItem,
                'progressPercentage' => $progressPercentage,
                'allAnswered' => $evaluation->answers_count
                    === $evaluation->total_questions,
                'formattedRemainingTime' => sprintf(
                    '%02d:%02d',
                    $minutes,
                    $seconds,
                ),
                'likertOptions' => LikertValue::options(),
            ],
        );
    }
}
