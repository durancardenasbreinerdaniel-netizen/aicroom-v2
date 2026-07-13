<?php

namespace Database\Factories;

use App\Enums\EvaluationLevel;
use App\Models\EvaluationResult;
use App\Models\EvaluationSkillResult;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EvaluationSkillResult>
 */
class EvaluationSkillResultFactory extends Factory
{
    /**
     * Modelo asociado.
     *
     * @var class-string<EvaluationSkillResult>
     */
    protected $model = EvaluationSkillResult::class;

    /**
     * Estado predeterminado.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $skill = Skill::factory();

        return [
            'evaluation_result_id' => EvaluationResult::factory(),
            'skill_id' => $skill,
            'skill_code_snapshot' => fake()
                ->unique()
                ->lexify('???'),
            'skill_name_snapshot' => fake()->words(
                2,
                true,
            ),
            'raw_score' => 9,
            'minimum_score' => 3,
            'maximum_score' => 15,
            'normalized_score' => 50,
            'level' => EvaluationLevel::INTERMEDIATE,
        ];
    }
}
