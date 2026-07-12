<?php

namespace Database\Factories;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Skill>
 */
class SkillFactory extends Factory
{
    /**
     * Modelo asociado a la factory.
     *
     * @var class-string<Skill>
     */
    protected $model = Skill::class;

    /**
     * Estado predeterminado de una habilidad.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'code' => Str::upper(
                fake()->unique()->lexify('???')
            ),
            'name' => Str::title($name),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }

    /**
     * Genera una habilidad inactiva.
     */
    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'is_active' => false,
        ]);
    }
}
