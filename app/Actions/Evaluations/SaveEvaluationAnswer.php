<?php

namespace App\Actions\Evaluations;

use App\Enums\EvaluationStatus;
use App\Enums\LikertValue;
use App\Models\Answer;
use App\Models\Evaluation;
use App\Models\EvaluationItem;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use LogicException;

class SaveEvaluationAnswer
{
    /**
     * Guarda o actualiza una respuesta de la evaluación.
     */
    public function execute(
        User $participant,
        Evaluation $evaluation,
        EvaluationItem $item,
        LikertValue|int $value,
    ): Answer {
        /*
         * Esta variable permite lanzar la excepción después de que
         * la transacción confirme el cambio de estado a EXPIRED.
         */
        $evaluationExpired = false;

        $answer = DB::transaction(
            function () use (
                $participant,
                $evaluation,
                $item,
                $value,
                &$evaluationExpired,
            ): ?Answer {
                /*
                 * Bloquea la evaluación para evitar modificaciones
                 * simultáneas mientras se guarda la respuesta.
                 */
                $lockedEvaluation = Evaluation::query()
                    ->lockForUpdate()
                    ->findOrFail($evaluation->id);

                $this->authorizeParticipant(
                    $participant,
                    $lockedEvaluation,
                );

                /*
                 * Si el tiempo terminó, actualizamos el estado dentro
                 * de la transacción, pero no lanzamos todavía la excepción.
                 *
                 * Lanzarla aquí provocaría que el cambio se revirtiera.
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

                /*
                 * Una evaluación enviada, cancelada o expirada
                 * ya no puede recibir respuestas.
                 */
                if (
                    $lockedEvaluation->status
                    !== EvaluationStatus::IN_PROGRESS
                ) {
                    throw ValidationException::withMessages([
                        'answerValue' => 'La evaluación ya no acepta respuestas.',
                    ]);
                }

                /*
                 * Comprueba que la pregunta pertenezca realmente
                 * a la evaluación indicada.
                 */
                $lockedItem = EvaluationItem::query()
                    ->where(
                        'evaluation_id',
                        $lockedEvaluation->id,
                    )
                    ->whereKey($item->id)
                    ->first();

                if ($lockedItem === null) {
                    throw ValidationException::withMessages([
                        'answerValue' => 'La pregunta no pertenece a esta evaluación.',
                    ]);
                }

                /*
                 * Convierte y valida la respuesta de la escala Likert.
                 */
                $likertValue = $value instanceof LikertValue
                    ? $value
                    : LikertValue::tryFrom((int) $value);

                if ($likertValue === null) {
                    throw ValidationException::withMessages([
                        'answerValue' => 'La respuesta seleccionada no es válida.',
                    ]);
                }

                /*
                 * Actualiza una respuesta existente o crea una nueva.
                 *
                 * La restricción única de evaluation_item_id evita
                 * respuestas duplicadas para una misma pregunta.
                 */
                return Answer::query()->updateOrCreate(
                    [
                        'evaluation_item_id' => $lockedItem->id,
                    ],
                    [
                        'evaluation_id' => $lockedEvaluation->id,
                        'answer_value' => $likertValue,
                        'answered_at' => now(),
                    ],
                );
            },
        );

        /*
         * La transacción ya terminó correctamente y el estado EXPIRED
         * quedó guardado. Ahora sí podemos devolver el error al formulario.
         */
        if ($evaluationExpired) {
            throw ValidationException::withMessages([
                'answerValue' => 'El tiempo de la evaluación ha terminado.',
            ]);
        }

        /*
         * Este caso no debería ocurrir, pero protege el tipo de retorno
         * del método ante una condición inesperada.
         */
        if (! $answer instanceof Answer) {
            throw new LogicException(
                'No fue posible guardar la respuesta de la evaluación.',
            );
        }

        return $answer;
    }

    /**
     * Comprueba que el participante sea propietario de la evaluación.
     */
    private function authorizeParticipant(
        User $participant,
        Evaluation $evaluation,
    ): void {
        if ($evaluation->user_id !== $participant->id) {
            throw new AuthorizationException(
                'No puedes responder esta evaluación.',
            );
        }
    }
}
