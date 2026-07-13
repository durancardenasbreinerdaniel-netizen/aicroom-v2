<?php

namespace Database\Factories;

use App\Models\Questionnaire;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Questionnaire>
 */
class QuestionnaireFactory extends Factory
{
    /**
     * Modelo asociado.
     *
     * @var class-string<Questionnaire>
     */
    protected $model = Questionnaire::class;

    /**
     * Estado predeterminado.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->sentence(3);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(),
            'is_active' => true,
        ];
    }

    /**
     * Genera un cuestionario inactivo.
     */
    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'is_active' => false,
        ]);
    }
}