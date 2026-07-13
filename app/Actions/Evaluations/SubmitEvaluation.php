<?php

namespace App\Actions\Evaluations;

use App\Enums\EvaluationStatus;
use App\Models\Evaluation;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubmitEvaluation
{
    /**
     * Finaliza una evaluación completamente respondida.
     */
    public function execute(
        User $participant,
        Evaluation $evaluation,
    ): Evaluation {
        return DB::transaction(
            function () use (
                $participant,
                $evaluation,
            ): Evaluation {
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
                 * Si ya fue enviada, devuelve el mismo registro.
                 * Esto mantiene la operación idempotente.
                 */
                if (
                    $lockedEvaluation->status
                    === EvaluationStatus::SUBMITTED
                ) {
                    return $lockedEvaluation;
                }

                if ($lockedEvaluation->hasExpired()) {
                    $lockedEvaluation->markAsExpired();

                    throw ValidationException::withMessages([
                        'evaluation' => 'El tiempo de la evaluación ha terminado.',
                    ]);
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
                    $missingAnswers = $lockedEvaluation->total_questions
                        - $answeredQuestions;

                    throw ValidationException::withMessages([
                        'evaluation' => "Todavía faltan {$missingAnswers} preguntas por responder.",
                    ]);
                }

                $lockedEvaluation->forceFill([
                    'status' => EvaluationStatus::SUBMITTED,
                    'submitted_at' => now(),
                ])->save();

                return $lockedEvaluation->refresh();
            },
        );
    }
}
