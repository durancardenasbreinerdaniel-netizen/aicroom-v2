<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Modelo asociado a la factory.
     *
     * @var class-string<Question>
     */
    protected $model = Question::class;

    /**
     * Estado predeterminado de una pregunta.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'skill_id' => Skill::factory(),
            'statement' => fake()
                ->unique()
                ->sentence(10),
            'weight' => 1,
            'is_reverse_scored' => false,
            'is_active' => true,
        ];
    }

    /**
     * Genera una pregunta inactiva.
     */
    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'is_active' => false,
        ]);
    }

    /**
     * Genera una pregunta de puntuación inversa.
     */
    public function reverseScored(): static
    {
        return $this->state(fn (): array => [
            'is_reverse_scored' => true,
        ]);
    }

    /**
     * Genera una pregunta con un peso específico.
     */
    public function weighted(int $weight): static
    {
        return $this->state(fn (): array => [
            'weight' => $weight,
        ]);
    }
}
