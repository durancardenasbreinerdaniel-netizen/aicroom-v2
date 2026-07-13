<?php

namespace App\Actions\Evaluations;

use App\Enums\EvaluationStatus;
use App\Enums\EvaluationType;
use App\Enums\QuestionnaireVersionStatus;
use App\Models\Evaluation;
use App\Models\EvaluationItem;
use App\Models\QuestionnaireItem;
use App\Models\QuestionnaireVersion;
use App\Models\User;
use App\Services\Evaluations\BalancedQuestionSelector;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StartEvaluation
{
    public function __construct(
        private readonly BalancedQuestionSelector $selector
    ) {}

    /**
     * Inicia una evaluación o devuelve la evaluación activa existente.
     */
    public function execute(
        User $participant,
        QuestionnaireVersion $version
    ): Evaluation {
        return DB::transaction(
            function () use (
                $participant,
                $version
            ): Evaluation {
                /*
                 * Bloquea al participante para evitar dos inicios
                 * simultáneos desde distintas solicitudes.
                 */
                $lockedParticipant = User::query()
                    ->lockForUpdate()
                    ->findOrFail($participant->id);

                /*
                 * Bloquea y vuelve a cargar la versión desde la base
                 * de datos para no confiar en datos antiguos.
                 */
                $lockedVersion = QuestionnaireVersion::query()
                    ->with([
                        'questionnaire',
                        'items',
                    ])
                    ->lockForUpdate()
                    ->findOrFail($version->id);

                $this->validateVersion(
                    $lockedVersion
                );

                /*
                 * Busca una evaluación activa de esta versión.
                 */
                $existingEvaluation = Evaluation::query()
                    ->ownedBy($lockedParticipant)
                    ->where(
                        'questionnaire_version_id',
                        $lockedVersion->id
                    )
                    ->inProgress()
                    ->latest('id')
                    ->first();

                if ($existingEvaluation !== null) {
                    /*
                     * Si todavía tiene tiempo, la operación es
                     * idempotente y devuelve la misma evaluación.
                     */
                    if (! $existingEvaluation->hasExpired()) {
                        return $existingEvaluation
                            ->load('items');
                    }

                    /*
                     * Si ya venció, se cierra antes de crear otra.
                     */
                    $existingEvaluation->markAsExpired();
                }

                $selectedItems = $this->selector
                    ->select($lockedVersion);

                $startedAt = now();

                $evaluation = Evaluation::query()->create([
                    'user_id' => $lockedParticipant->id,
                    'questionnaire_version_id' => $lockedVersion->id,
                    'evaluation_type' => $this->determineType(
                        $lockedParticipant,
                        $lockedVersion
                    ),
                    'status' => EvaluationStatus::IN_PROGRESS,
                    'total_questions' => $selectedItems->count(),
                    'time_limit_minutes' => $lockedVersion->time_limit_minutes,
                    'started_at' => $startedAt,
                    'expires_at' => $startedAt
                        ->copy()
                        ->addMinutes(
                            $lockedVersion->time_limit_minutes
                        ),
                ]);

                /*
                 * Crea una copia inmutable de cada pregunta seleccionada.
                 */
                foreach (
                    $selectedItems as $position => $questionnaireItem
                ) {
                    $this->createEvaluationItem(
                        $evaluation,
                        $questionnaireItem,
                        $position + 1
                    );
                }

                return $evaluation->load([
                    'items.skill',
                    'questionnaireVersion.questionnaire',
                ]);
            }
        );
    }

    /**
     * Valida que la versión esté disponible.
     */
    private function validateVersion(
        QuestionnaireVersion $version
    ): void {
        if (
            $version->status
            !== QuestionnaireVersionStatus::PUBLISHED
        ) {
            throw ValidationException::withMessages([
                'questionnaire' => 'Solo se pueden iniciar evaluaciones desde versiones publicadas.',
            ]);
        }

        if (
            $version->questionnaire->trashed()
            || ! $version->questionnaire->is_active
        ) {
            throw ValidationException::withMessages([
                'questionnaire' => 'El cuestionario no se encuentra disponible.',
            ]);
        }

        if ($version->time_limit_minutes < 1) {
            throw ValidationException::withMessages([
                'questionnaire' => 'La versión no tiene un tiempo límite válido.',
            ]);
        }
    }

    /**
     * Determina si se trata de una evaluación inicial
     * o una reevaluación.
     */
    private function determineType(
        User $participant,
        QuestionnaireVersion $version
    ): EvaluationType {
        $hasPreviousEvaluation = Evaluation::query()
            ->ownedBy($participant)
            ->whereHas(
                'questionnaireVersion',
                fn ($query) => $query->where(
                    'questionnaire_id',
                    $version->questionnaire_id
                )
            )
            ->whereIn('status', [
                EvaluationStatus::SUBMITTED->value,
                EvaluationStatus::EXPIRED->value,
            ])
            ->exists();

        return $hasPreviousEvaluation
            ? EvaluationType::FOLLOW_UP
            : EvaluationType::BASELINE;
    }

    /**
     * Crea una pregunta inmutable dentro de la evaluación.
     */
    private function createEvaluationItem(
        Evaluation $evaluation,
        QuestionnaireItem $item,
        int $position
    ): void {
        EvaluationItem::query()->create([
            'evaluation_id' => $evaluation->id,
            'questionnaire_item_id' => $item->id,
            'position' => $position,
            'skill_id' => $item->skill_id_snapshot,
            'statement' => $item->statement_snapshot,
            'weight' => $item->weight_snapshot,
            'is_reverse_scored' => $item
                ->is_reverse_scored_snapshot,
        ]);
    }
}
