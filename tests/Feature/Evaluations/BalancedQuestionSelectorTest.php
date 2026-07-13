<?php

namespace Tests\Feature\Evaluations;

use App\Actions\Questionnaires\PublishQuestionnaireVersion;
use App\Models\QuestionnaireVersion;
use App\Models\User;
use App\Services\Evaluations\BalancedQuestionSelector;
use Database\Seeders\QuestionnaireSeeder;
use Database\Seeders\QuestionSeeder;
use Database\Seeders\SkillSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BalancedQuestionSelectorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            SkillSeeder::class,
            QuestionSeeder::class,
            QuestionnaireSeeder::class,
        ]);
    }

    public function test_selection_represents_every_skill(): void
    {
        $publisher = User::factory()->create();

        $version = QuestionnaireVersion::query()
            ->firstOrFail();

        /*
         * Seleccionaremos diez preguntas: una por habilidad.
         */
        $version->update([
            'questions_per_evaluation' => 10,
        ]);

        app(PublishQuestionnaireVersion::class)
            ->execute(
                $version,
                $publisher
            );

        $selected = app(
            BalancedQuestionSelector::class
        )->select(
            $version->refresh()
        );

        $this->assertCount(10, $selected);

        $this->assertCount(
            10,
            $selected
                ->pluck('skill_id_snapshot')
                ->unique()
        );
    }

    public function test_selection_does_not_repeat_questions(): void
    {
        $publisher = User::factory()->create();

        $version = QuestionnaireVersion::query()
            ->firstOrFail();

        app(PublishQuestionnaireVersion::class)
            ->execute(
                $version,
                $publisher
            );

        $selected = app(
            BalancedQuestionSelector::class
        )->select(
            $version->refresh()
        );

        $this->assertCount(
            $selected->count(),
            $selected->pluck('id')->unique()
        );
    }
}
