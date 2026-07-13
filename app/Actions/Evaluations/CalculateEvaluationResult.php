<?php

namespace App\Actions\Evaluations;

use App\Enums\EvaluationLevel;
use App\Enums\EvaluationStatus;
use App\Models\Evaluation;
use App\Models\EvaluationItem;
use App\Models\EvaluationResult;
use Illuminate\Validation\ValidationException;

class CalculateEvaluationResult
{
    /**
     * Calcula y almacena el resultado de una evaluación finalizada.
     */
    public function execute(
        Evaluation $evaluation,
    ): EvaluationResult {
        $evaluation->loadMissing([
            'items.answer',
            'items.skill',
        ]);

        $this->validateEvaluation(
            $evaluation,
        );

        $globalRawScore = 0;
        $globalMinimumScore = 0;
        $globalMaximumScore = 0;

        /**
         * @var array<int, array{
         *     skill_id: int,
         *     skill_code_snapshot: string,
         *     skill_name_snapshot: string,
         *     raw_score: int,
         *     minimum_score: int,
         *     maximum_score: int
         * }> $skillScores
         */
        $skillScores = [];

        foreach ($evaluation->items as $item) {
            $answerValue = $item
                ->answer
                ->answer_value
                ->value;

            /*
             * Para preguntas inversas:
             *
             * 1 se convierte en 5.
             * 2 se convierte en 4.
             * 3 permanece en 3.
             * 4 se convierte en 2.
             * 5 se convierte en 1.
             */
            $scoredValue = $item->is_reverse_scored
                ? 6 - $answerValue
                : $answerValue;

            $rawScore = $scoredValue
                * $item->weight;

            $minimumScore = 1
                * $item->weight;

            $maximumScore = 5
                * $item->weight;

            $globalRawScore += $rawScore;
            $globalMinimumScore += $minimumScore;
            $globalMaximumScore += $maximumScore;

            $skill = $item->skill;

            if ($skill === null) {
                throw ValidationException::withMessages([
                    'evaluation' => "La pregunta #{$item->id} no tiene una habilidad disponible.",
                ]);
            }

            if (! isset($skillScores[$skill->id])) {
                $skillScores[$skill->id] = [
                    'skill_id' => $skill->id,
                    'skill_code_snapshot' => $skill->code,
                    'skill_name_snapshot' => $skill->name,
                    'raw_score' => 0,
                    'minimum_score' => 0,
                    'maximum_score' => 0,
                ];
            }

            $skillScores[$skill->id]['raw_score']
                += $rawScore;

            $skillScores[$skill->id]['minimum_score']
                += $minimumScore;

            $skillScores[$skill->id]['maximum_score']
                += $maximumScore;
        }

        $normalizedScore = $this->normalize(
            rawScore: $globalRawScore,
            minimumScore: $globalMinimumScore,
            maximumScore: $globalMaximumScore,
        );

        $result = EvaluationResult::query()
            ->updateOrCreate(
                [
                    'evaluation_id' => $evaluation->id,
                ],
                [
                    'raw_score' => $globalRawScore,
                    'minimum_score' => $globalMinimumScore,
                    'maximum_score' => $globalMaximumScore,
                    'normalized_score' => $normalizedScore,
                    'level' => EvaluationLevel::fromNormalizedScore(
                        $normalizedScore,
                    ),
                    'algorithm_version' => EvaluationResult::ALGORITHM_VERSION,
                    'calculated_at' => now(),
                ],
            );

        /*
         * Reemplaza los resultados por habilidad para que ejecutar
         * nuevamente la acción no genere registros duplicados.
         */
        $result->skillResults()->delete();

        foreach ($skillScores as $skillScore) {
            $skillNormalizedScore = $this->normalize(
                rawScore: $skillScore['raw_score'],
                minimumScore: $skillScore['minimum_score'],
                maximumScore: $skillScore['maximum_score'],
            );

            $result->skillResults()->create([
                ...$skillScore,
                'normalized_score' => $skillNormalizedScore,
                'level' => EvaluationLevel::fromNormalizedScore(
                    $skillNormalizedScore,
                ),
            ]);
        }

        return $result
            ->refresh()
            ->load([
                'evaluation',
                'skillResults.skill',
            ]);
    }

    /**
     * Comprueba que la evaluación pueda calcularse.
     */
    private function validateEvaluation(
        Evaluation $evaluation,
    ): void {
        if (
            $evaluation->status
            !== EvaluationStatus::SUBMITTED
        ) {
            throw ValidationException::withMessages([
                'evaluation' => 'Solo se pueden calcular evaluaciones finalizadas.',
            ]);
        }

        if (
            $evaluation->items->count()
            !== $evaluation->total_questions
        ) {
            throw ValidationException::withMessages([
                'evaluation' => 'La cantidad de preguntas no coincide con la evaluación.',
            ]);
        }

        $missingAnswer = $evaluation
            ->items
            ->first(
                fn (
                    EvaluationItem $item,
                ): bool => $item->answer === null,
            );

        if ($missingAnswer !== null) {
            throw ValidationException::withMessages([
                'evaluation' => "La pregunta #{$missingAnswer->id} no tiene respuesta.",
            ]);
        }
    }

    /**
     * Convierte un puntaje ponderado al intervalo de 0 a 100.
     */
    private function normalize(
        int $rawScore,
        int $minimumScore,
        int $maximumScore,
    ): float {
        if ($maximumScore <= $minimumScore) {
            return 0;
        }

        return round(
            (
                ($rawScore - $minimumScore)
                / ($maximumScore - $minimumScore)
            ) * 100,
            2,
        );
    }
}
