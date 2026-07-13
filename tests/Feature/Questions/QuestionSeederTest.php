<?php

namespace Tests\Feature\Questions;

use App\Models\Question;
use App\Models\Skill;
use Database\Seeders\QuestionSeeder;
use Database\Seeders\SkillSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_initial_questions_can_be_seeded(): void
    {
        $this->seed([
            SkillSeeder::class,
            QuestionSeeder::class,
        ]);

        $this->assertDatabaseCount('questions', 30);

        $this->assertSame(
            10,
            Question::query()
                ->where('is_reverse_scored', true)
                ->count(),
        );
    }

    public function test_each_skill_has_minimum_active_questions(): void
    {
        $this->seed([
            SkillSeeder::class,
            QuestionSeeder::class,
        ]);

        $skills = Skill::query()
            ->withCount('activeQuestions')
            ->get();

        $this->assertCount(10, $skills);

        foreach ($skills as $skill) {
            $this->assertSame(
                Skill::MINIMUM_ACTIVE_QUESTIONS,
                $skill->active_questions_count,
                "La habilidad {$skill->code} no tiene suficientes preguntas."
            );

            $this->assertTrue(
                $skill->hasMinimumActiveQuestions()
            );
        }
    }

    public function test_question_seeder_is_idempotent(): void
    {
        $this->seed([
            SkillSeeder::class,
            QuestionSeeder::class,
            QuestionSeeder::class,
        ]);

        $this->assertDatabaseCount('questions', 30);
    }
}
