<?php

namespace Tests\Feature\Evaluations;

use App\Actions\Evaluations\StartEvaluation;
use App\Actions\Questionnaires\PublishQuestionnaireVersion;
use App\Enums\RoleName;
use App\Models\QuestionnaireVersion;
use App\Models\User;
use Database\Seeders\QuestionnaireSeeder;
use Database\Seeders\QuestionSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\SkillSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvaluationPageAccessTest extends TestCase
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

    public function test_participant_can_view_own_evaluation(): void
    {
        $publisher = User::factory()->create();

        $version = QuestionnaireVersion::query()
            ->firstOrFail();

        app(PublishQuestionnaireVersion::class)
            ->execute(
                $version,
                $publisher
            );

        $participant = User::factory()->create();

        $participant->assignRole(
            RoleName::PARTICIPANT->value
        );

        $evaluation = app(
            StartEvaluation::class
        )->execute(
            $participant,
            $version->refresh()
        );

        $this
            ->actingAs($participant)
            ->get(
                route(
                    'evaluations.show',
                    $evaluation
                )
            )
            ->assertOk()
            ->assertSee(
                'La evaluación fue preparada correctamente.'
            );
    }

    public function test_participant_cannot_view_another_evaluation(): void
    {
        $publisher = User::factory()->create();

        $version = QuestionnaireVersion::query()
            ->firstOrFail();

        app(PublishQuestionnaireVersion::class)
            ->execute(
                $version,
                $publisher
            );

        $owner = User::factory()->create();

        $owner->assignRole(
            RoleName::PARTICIPANT->value
        );

        $otherParticipant = User::factory()
            ->create();

        $otherParticipant->assignRole(
            RoleName::PARTICIPANT->value
        );

        $evaluation = app(
            StartEvaluation::class
        )->execute(
            $owner,
            $version->refresh()
        );

        $this
            ->actingAs($otherParticipant)
            ->get(
                route(
                    'evaluations.show',
                    $evaluation
                )
            )
            ->assertForbidden();
    }
}
