<?php

namespace Database\Factories;

use App\Enums\EvaluationLevel;
use App\Models\Evaluation;
use App\Models\EvaluationResult;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EvaluationResult>
 */
class EvaluationResultFactory extends Factory
{
    /**
     * Modelo asociado.
     *
     * @var class-string<EvaluationResult>
     */
    protected $model = EvaluationResult::class;

    /**
     * Estado predeterminado.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'evaluation_id' => Evaluation::factory()
                ->submitted(),
            'raw_score' => 90,
            'minimum_score' => 30,
            'maximum_score' => 150,
            'normalized_score' => 50,
            'level' => EvaluationLevel::INTERMEDIATE,
            'algorithm_version' => EvaluationResult::ALGORITHM_VERSION,
            'calculated_at' => now(),
        ];
    }
}
