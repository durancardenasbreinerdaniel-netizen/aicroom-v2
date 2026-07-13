<?php

namespace Database\Factories;

use App\Models\Evaluation;
use App\Models\EvaluationItem;
use App\Models\QuestionnaireItem;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EvaluationItem>
 */
class EvaluationItemFactory extends Factory
{
    /**
     * Modelo asociado.
     *
     * @var class-string<EvaluationItem>
     */
    protected $model = EvaluationItem::class;

    /**
     * Estado predeterminado.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'evaluation_id' => Evaluation::factory(),
            'questionnaire_item_id' => QuestionnaireItem::factory(),
            'position' => 1,
            'skill_id' => Skill::factory(),
            'statement' => fake()->sentence(10),
            'weight' => 1,
            'is_reverse_scored' => false,
        ];
    }
}
