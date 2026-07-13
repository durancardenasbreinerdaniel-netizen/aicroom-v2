<?php

namespace App\Actions\Questionnaires;

use App\Enums\QuestionnaireVersionStatus;
use App\Models\QuestionnaireVersion;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PublishQuestionnaireVersion
{
    /**
     * Publica una versión y crea una copia inmutable
     * de todas sus preguntas.
     */
    public function execute(
        QuestionnaireVersion $version,
        User $publisher
    ): QuestionnaireVersion {
        return DB::transaction(
            function () use ($version, $publisher): QuestionnaireVersion {
                /*
                 * Bloquea el registro para evitar dos publicaciones
                 * simultáneas de la misma versión.
                 */
                $lockedVersion = QuestionnaireVersion::query()
                    ->lockForUpdate()
                    ->findOrFail($version->id);

                $lockedVersion->load([
                    'questionnaire',
                    'items.question.skill',
                ]);

                $this->validateVersion($lockedVersion);

                /*
                 * Crea el snapshot de cada pregunta.
                 */
                foreach ($lockedVersion->items as $item) {
                    $item->createSnapshotFromQuestion(
                        $item->question
                    );
                }

                /*
                 * Retira cualquier versión publicada anteriormente.
                 */
                QuestionnaireVersion::query()
                    ->where(
                        'questionnaire_id',
                        $lockedVersion->questionnaire_id
                    )
                    ->whereKeyNot($lockedVersion->id)
                    ->where(
                        'status',
                        QuestionnaireVersionStatus::PUBLISHED->value
                    )
                    ->update([
                        'status' => QuestionnaireVersionStatus::RETIRED->value,
                        'updated_at' => now(),
                    ]);

                /*
                 * Publica la versión actual.
                 */
                $lockedVersion->forceFill([
                    'status' => QuestionnaireVersionStatus::PUBLISHED,
                    'published_at' => now(),
                    'published_by' => $publisher->id,
                ])->save();

                return $lockedVersion->refresh();
            }
        );
    }

    /**
     * Valida que la versión pueda publicarse.
     */
    private function validateVersion(
        QuestionnaireVersion $version
    ): void {
        if (! $version->isDraft()) {
            throw ValidationException::withMessages([
                'version' => 'Solo pueden publicarse versiones en borrador.',
            ]);
        }

        if (! $version->questionnaire->is_active) {
            throw ValidationException::withMessages([
                'questionnaire' => 'El cuestionario debe estar activo antes de publicar una versión.',
            ]);
        }

        if ($version->items->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'La versión debe contener preguntas.',
            ]);
        }

        $questionIds = $version->items
            ->pluck('question_id');

        if ($questionIds->duplicates()->isNotEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'Una pregunta no puede aparecer más de una vez.',
            ]);
        }

        /*
         * Valida que todas las preguntas seleccionadas sigan activas
         * y pertenezcan a habilidades activas.
         */
        foreach ($version->items as $item) {
            if (
                $item->question->trashed()
                || ! $item->question->is_active
            ) {
                throw ValidationException::withMessages([
                    'items' => "La pregunta #{$item->question_id} no está disponible.",
                ]);
            }

            if (
                $item->question->skill->trashed()
                || ! $item->question->skill->is_active
            ) {
                throw ValidationException::withMessages([
                    'items' => "La habilidad de la pregunta #{$item->question_id} no está activa.",
                ]);
            }
        }

        /*
         * Obtiene todas las habilidades que deben aparecer.
         *
         * Solo se incluyen habilidades activas con preguntas activas.
         */
        $requiredSkills = Skill::query()
            ->active()
            ->whereHas(
                'activeQuestions',
                fn ($query) => $query->whereNull('deleted_at')
            )
            ->orderBy('name')
            ->get();

        $this->validateSkillRepresentation(
            $version,
            $requiredSkills
        );

        $poolCount = $version->items->count();

        if (
            $version->questions_per_evaluation
            > $poolCount
        ) {
            throw ValidationException::withMessages([
                'questions_per_evaluation' => 'La cantidad de preguntas por evaluación no puede superar el banco de la versión.',
            ]);
        }

        if (
            $version->questions_per_evaluation
            < $requiredSkills->count()
        ) {
            throw ValidationException::withMessages([
                'questions_per_evaluation' => 'La evaluación debe permitir seleccionar al menos una pregunta por habilidad.',
            ]);
        }

        if ($version->time_limit_minutes < 1) {
            throw ValidationException::withMessages([
                'time_limit_minutes' => 'El tiempo límite debe ser mayor que cero.',
            ]);
        }
    }

    /**
     * Comprueba que cada habilidad esté suficientemente representada.
     *
     * @param  Collection<int, Skill>  $requiredSkills
     */
    private function validateSkillRepresentation(
        QuestionnaireVersion $version,
        Collection $requiredSkills
    ): void {
        $itemsBySkill = $version->items
            ->groupBy(
                fn ($item): int => $item->question->skill_id
            );

        foreach ($requiredSkills as $skill) {
            $skillItemsCount = $itemsBySkill
                ->get($skill->id, collect())
                ->count();

            if (
                $skillItemsCount
                < Skill::MINIMUM_ACTIVE_QUESTIONS
            ) {
                throw ValidationException::withMessages([
                    'items' => "La habilidad {$skill->name} debe tener al menos "
                        .Skill::MINIMUM_ACTIVE_QUESTIONS
                        .' preguntas dentro de la versión.',
                ]);
            }
        }
    }
}