<?php

namespace App\Actions\Evaluations;

use App\Enums\EvaluationStatus;
use App\Models\Evaluation;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use LogicException;

class SubmitEvaluation
{
    /**
     * Inyecta la acción encargada de calcular el resultado.
     */
    public function __construct(
        private readonly CalculateEvaluationResult $calculateResult,
    ) {}

    /**
     * Finaliza una evaluación completamente respondida.
     */
    public function execute(
        User $participant,
        Evaluation $evaluation,
    ): Evaluation {
        $evaluationExpired = false;

        $submittedEvaluation = DB::transaction(
            function () use (
                $participant,
                $evaluation,
                &$evaluationExpired,
            ): ?Evaluation {
                $lockedEvaluation = Evaluation::query()
                    ->lockForUpdate()
                    ->findOrFail($evaluation->id);

                if (
                    $lockedEvaluation->user_id
                    !== $participant->id
                ) {
                    throw new AuthorizationException(
                        'No puedes finalizar esta evaluación.',
                    );
                }

                /*
                 * Si ya fue enviada, mantiene la operación
                 * idempotente y comprueba que tenga resultado.
                 */
                if (
                    $lockedEvaluation->status
                    === EvaluationStatus::SUBMITTED
                ) {
                    if (
                        ! $lockedEvaluation
                            ->result()
                            ->exists()
                    ) {
                        $this->calculateResult->execute(
                            $lockedEvaluation,
                        );
                    }

                    return $lockedEvaluation
                        ->refresh()
                        ->load([
                            'result.skillResults',
                        ]);
                }

                /*
                 * Guarda el cambio a EXPIRED antes de lanzar
                 * el error fuera de la transacción.
                 */
                if ($lockedEvaluation->hasExpired()) {
                    if ($lockedEvaluation->isInProgress()) {
                        $lockedEvaluation->forceFill([
                            'status' => EvaluationStatus::EXPIRED,
                        ])->save();
                    }

                    $evaluationExpired = true;

                    return null;
                }

                if (
                    $lockedEvaluation->status
                    !== EvaluationStatus::IN_PROGRESS
                ) {
                    throw ValidationException::withMessages([
                        'evaluation' => 'La evaluación no puede finalizarse en su estado actual.',
                    ]);
                }

                $answeredQuestions = $lockedEvaluation
                    ->answers()
                    ->count();

                if (
                    $answeredQuestions
                    !== $lockedEvaluation->total_questions
                ) {
                    $missingAnswers = $lockedEvaluation
                        ->total_questions
                        - $answeredQuestions;

                    throw ValidationException::withMessages([
                        'evaluation' => "Todavía faltan {$missingAnswers} preguntas por responder.",
                    ]);
                }

                $lockedEvaluation->forceFill([
                    'status' => EvaluationStatus::SUBMITTED,
                    'submitted_at' => now(),
                ])->save();

                /*
                 * El resultado se calcula dentro de la misma
                 * transacción que finaliza la evaluación.
                 */
                $this->calculateResult->execute(
                    $lockedEvaluation->refresh(),
                );

                return $lockedEvaluation
                    ->refresh()
                    ->load([
                        'result.skillResults',
                    ]);
            },
        );

        if ($evaluationExpired) {
            throw ValidationException::withMessages([
                'evaluation' => 'El tiempo de la evaluación ha terminado.',
            ]);
        }

        if (! $submittedEvaluation instanceof Evaluation) {
            throw new LogicException(
                'No fue posible finalizar la evaluación.',
            );
        }

        return $submittedEvaluation;
    }
}
