<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\QuestionnaireItem;
use App\Models\QuestionnaireVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuestionnaireItem>
 */
class QuestionnaireItemFactory extends Factory
{
    /**
     * Modelo asociado.
     *
     * @var class-string<QuestionnaireItem>
     */
    protected $model = QuestionnaireItem::class;

    /**
     * Estado predeterminado.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'questionnaire_version_id' => QuestionnaireVersion::factory(),
            'question_id' => Question::factory(),
            'position' => 1,
            'skill_id_snapshot' => null,
            'statement_snapshot' => null,
            'weight_snapshot' => null,
            'is_reverse_scored_snapshot' => null,
        ];
    }
}