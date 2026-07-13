<?php

namespace Database\Factories;

use App\Enums\QuestionnaireVersionStatus;
use App\Models\Questionnaire;
use App\Models\QuestionnaireVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuestionnaireVersion>
 */
class QuestionnaireVersionFactory extends Factory
{
    /**
     * Modelo asociado.
     *
     * @var class-string<QuestionnaireVersion>
     */
    protected $model = QuestionnaireVersion::class;

    /**
     * Estado predeterminado.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'questionnaire_id' => Questionnaire::factory(),
            'version_number' => null,
            'questions_per_evaluation' => 30,
            'time_limit_minutes' => 20,
            'status' => QuestionnaireVersionStatus::DRAFT,
            'published_at' => null,
            'created_by' => null,
            'published_by' => null,
        ];
    }
}