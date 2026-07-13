<?php

namespace Database\Factories;

use App\Enums\EvaluationStatus;
use App\Enums\EvaluationType;
use App\Models\Evaluation;
use App\Models\QuestionnaireVersion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Evaluation>
 */
class EvaluationFactory extends Factory
{
    /**
     * Modelo asociado.
     *
     * @var class-string<Evaluation>
     */
    protected $model = Evaluation::class;

    /**
     * Estado predeterminado.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startedAt = now();

        return [
            'public_id' => (string) Str::ulid(),
            'user_id' => User::factory(),
            'questionnaire_version_id' => QuestionnaireVersion::factory(),
            'evaluation_type' => EvaluationType::BASELINE,
            'status' => EvaluationStatus::IN_PROGRESS,
            'total_questions' => 30,
            'time_limit_minutes' => 20,
            'started_at' => $startedAt,
            'expires_at' => $startedAt
                ->copy()
                ->addMinutes(20),
            'submitted_at' => null,
            'cancelled_at' => null,
        ];
    }

    /**
     * Genera una evaluación expirada.
     */
    public function expired(): static
    {
        return $this->state(fn (): array => [
            'status' => EvaluationStatus::EXPIRED,
            'started_at' => now()->subMinutes(30),
            'expires_at' => now()->subMinutes(10),
        ]);
    }

    /**
     * Genera una evaluación enviada.
     */
    public function submitted(): static
    {
        return $this->state(fn (): array => [
            'status' => EvaluationStatus::SUBMITTED,
            'submitted_at' => now(),
        ]);
    }
}
