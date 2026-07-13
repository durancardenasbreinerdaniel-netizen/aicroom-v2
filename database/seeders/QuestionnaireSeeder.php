<?php

namespace Database\Seeders;

use App\Enums\QuestionnaireVersionStatus;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\QuestionnaireItem;
use App\Models\QuestionnaireVersion;
use Illuminate\Database\Seeder;
use RuntimeException;

class QuestionnaireSeeder extends Seeder
{
    /**
     * Crea el cuestionario general inicial.
     *
     * La versión se crea como borrador para que un administrador
     * revise su contenido antes de publicarla.
     */
    public function run(): void
    {
        $questionnaire = Questionnaire::withTrashed()
            ->updateOrCreate(
                [
                    'slug' => 'evaluacion-general',
                ],
                [
                    'name' => 'Evaluación general de habilidades blandas',
                    'description' => 'Cuestionario general para evaluar las habilidades blandas iniciales de AICROOM.',
                    'is_active' => true,
                    'deleted_at' => null,
                ],
            );

        $questions = Question::query()
            ->active()
            ->whereHas(
                'skill',
                fn ($query) => $query
                    ->where('is_active', true)
                    ->whereNull('deleted_at')
            )
            ->with('skill')
            ->get()
            ->sortBy([
                ['skill.code', 'asc'],
                ['id', 'asc'],
            ])
            ->values();

        if ($questions->isEmpty()) {
            throw new RuntimeException(
                'No existen preguntas activas para crear el cuestionario.'
            );
        }

        $version = QuestionnaireVersion::query()
            ->firstOrCreate(
                [
                    'questionnaire_id' => $questionnaire->id,
                    'version_number' => 1,
                ],
                [
                    'questions_per_evaluation' => min(
                        30,
                        $questions->count()
                    ),
                    'time_limit_minutes' => 20,
                    'status' => QuestionnaireVersionStatus::DRAFT,
                ],
            );

        /*
         * No modifica una versión que ya haya sido publicada.
         */
        if (! $version->isDraft()) {
            return;
        }

        foreach ($questions as $index => $question) {
            QuestionnaireItem::query()->firstOrCreate(
                [
                    'questionnaire_version_id' => $version->id,
                    'question_id' => $question->id,
                ],
                [
                    'position' => $index + 1,
                ],
            );
        }
    }
}