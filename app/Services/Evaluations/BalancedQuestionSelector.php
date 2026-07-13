<?php

namespace App\Services\Evaluations;

use App\Models\QuestionnaireItem;
use App\Models\QuestionnaireVersion;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class BalancedQuestionSelector
{
    /**
     * Selecciona preguntas garantizando que cada habilidad
     * esté representada al menos una vez.
     *
     * @return Collection<int, QuestionnaireItem>
     */
    public function select(
        QuestionnaireVersion $version
    ): Collection {
        $version->loadMissing('items');

        $items = $version->items->values();

        $targetAmount = $version->questions_per_evaluation;

        if ($items->count() < $targetAmount) {
            throw ValidationException::withMessages([
                'questionnaire' => 'La versión publicada no contiene suficientes preguntas.',
            ]);
        }

        /*
         * Una versión publicada debe tener snapshots completos.
         */
        $invalidSnapshot = $items->first(
            fn (
                QuestionnaireItem $item
            ): bool => ! $item->hasSnapshot()
        );

        if ($invalidSnapshot !== null) {
            throw ValidationException::withMessages([
                'questionnaire' => 'La versión publicada contiene preguntas sin snapshot.',
            ]);
        }

        /*
         * Agrupa el banco por habilidad.
         */
        $itemsBySkill = $items->groupBy(
            'skill_id_snapshot'
        );

        if ($itemsBySkill->has(null)) {
            throw ValidationException::withMessages([
                'questionnaire' => 'Existen preguntas sin habilidad asociada.',
            ]);
        }

        if ($targetAmount < $itemsBySkill->count()) {
            throw ValidationException::withMessages([
                'questionnaire' => 'La evaluación no permite seleccionar al menos una pregunta por habilidad.',
            ]);
        }

        /*
         * Selecciona primero una pregunta aleatoria por habilidad.
         */
        $selected = collect();

        foreach ($itemsBySkill as $skillItems) {
            $selected->push(
                $skillItems->shuffle()->first()
            );
        }

        /*
         * Completa los espacios restantes con preguntas que
         * todavía no fueron seleccionadas.
         */
        $remainingAmount = $targetAmount
            - $selected->count();

        if ($remainingAmount > 0) {
            $selectedIds = $selected->pluck('id');

            $additionalItems = $items
                ->reject(
                    fn (
                        QuestionnaireItem $item
                    ): bool => $selectedIds->contains($item->id)
                )
                ->shuffle()
                ->take($remainingAmount);

            $selected = $selected->concat(
                $additionalItems
            );
        }

        /*
         * Mezcla el orden final para que las habilidades no
         * aparezcan agrupadas.
         */
        return $selected
            ->shuffle()
            ->values();
    }
}
