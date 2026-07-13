<?php

namespace Database\Factories;

use App\Enums\LikertValue;
use App\Models\Answer;
use App\Models\Evaluation;
use App\Models\EvaluationItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Answer>
 */
class AnswerFactory extends Factory
{
    /**
     * Modelo asociado.
     *
     * @var class-string<Answer>
     */
    protected $model = Answer::class;

    /**
     * Estado predeterminado.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'evaluation_id' => Evaluation::factory(),
            'evaluation_item_id' => EvaluationItem::factory(),
            'answer_value' => fake()->numberBetween(
                LikertValue::minimum(),
                LikertValue::maximum(),
            ),
            'answered_at' => now(),
        ];
    }
}
