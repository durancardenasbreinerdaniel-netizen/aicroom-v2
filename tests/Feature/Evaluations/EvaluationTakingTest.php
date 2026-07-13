<?php

namespace Tests\Feature\Evaluations;

use App\Actions\Evaluations\StartEvaluation;
use App\Actions\Questionnaires\PublishQuestionnaireVersion;
use App\Enums\RoleName;
use App\Livewire\Participant\Evaluations\Show;
use App\Models\QuestionnaireVersion;
use App\Models\User;
use Database\Seeders\QuestionnaireSeeder;
use Database\Seeders\QuestionSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\SkillSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EvaluationTakingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RoleAndPermissionSeeder::class,
            SkillSeeder::class,
            QuestionSeeder::class,
            QuestionnaireSeeder::class,
        ]);
    }

    public function test_answer_is_saved_from_livewire_component(): void
    {
        $publisher = User::factory()->create();

        $version = QuestionnaireVersion::query()
            ->firstOrFail();

        app(PublishQuestionnaireVersion::class)
            ->execute(
                $version,
                $publisher,
            );

        $participant = User::factory()->create();

        $participant->assignRole(
            RoleName::PARTICIPANT->value,
        );

        $evaluation = app(
            StartEvaluation::class,
        )->execute(
            $participant,
            $version->refresh(),
        );

        Livewire::actingAs($participant)
            ->test(
                Show::class,
                [
                    'evaluation' => $evaluation,
                ],
            )
            ->set('answerValue', 4)
            ->assertHasNoErrors();

        $firstItem = $evaluation
            ->items()
            ->orderBy('position')
            ->firstOrFail();

        $this->assertDatabaseHas('answers', [
            'evaluation_id' => $evaluation->id,
            'evaluation_item_id' => $firstItem->id,
            'answer_value' => 4,
        ]);
    }
}
