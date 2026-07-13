<?php

namespace Tests\Feature\Evaluations;

use App\Actions\Evaluations\StartEvaluation;
use App\Actions\Questionnaires\PublishQuestionnaireVersion;
use App\Enums\EvaluationStatus;
use App\Enums\EvaluationType;
use App\Enums\RoleName;
use App\Models\QuestionnaireVersion;
use App\Models\User;
use Database\Seeders\QuestionnaireSeeder;
use Database\Seeders\QuestionSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\SkillSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class StartEvaluationTest extends TestCase
{
    use RefreshDatabase;

    private User $participant;

    private QuestionnaireVersion $version;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RoleAndPermissionSeeder::class,
            SkillSeeder::class,
            QuestionSeeder::class,
            QuestionnaireSeeder::class,
        ]);

        $publisher = User::factory()->create();

        $this->version = QuestionnaireVersion::query()
            ->firstOrFail();

        app(PublishQuestionnaireVersion::class)
            ->execute(
                $this->version,
                $publisher
            );

        $this->version->refresh();

        $this->participant = User::factory()->create();

        $this->participant->assignRole(
            RoleName::PARTICIPANT->value
        );
    }

    public function test_participant_can_start_evaluation(): void
    {
        $evaluation = app(
            StartEvaluation::class
        )->execute(
            $this->participant,
            $this->version
        );

        $this->assertSame(
            EvaluationStatus::IN_PROGRESS,
            $evaluation->status
        );

        $this->assertSame(
            EvaluationType::BASELINE,
            $evaluation->evaluation_type
        );

        $this->assertSame(
            $this->version->questions_per_evaluation,
            $evaluation->items->count()
        );

        $this->assertSame(
            $this->version->time_limit_minutes,
            $evaluation->time_limit_minutes
        );

        $this->assertNotEmpty(
            $evaluation->public_id
        );

        foreach ($evaluation->items as $item) {
            $this->assertNotEmpty($item->statement);
            $this->assertGreaterThan(0, $item->weight);
            $this->assertNotNull($item->skill_id);
        }
    }

    public function test_starting_twice_returns_same_active_evaluation(): void
    {
        $first = app(
            StartEvaluation::class
        )->execute(
            $this->participant,
            $this->version
        );

        $second = app(
            StartEvaluation::class
        )->execute(
            $this->participant,
            $this->version
        );

        $this->assertTrue(
            $first->is($second)
        );

        $this->assertDatabaseCount(
            'evaluations',
            1
        );
    }

    public function test_expired_evaluation_is_closed_before_creating_another(): void
    {
        $first = app(
            StartEvaluation::class
        )->execute(
            $this->participant,
            $this->version
        );

        $first->forceFill([
            'expires_at' => now()->subMinute(),
        ])->save();

        $second = app(
            StartEvaluation::class
        )->execute(
            $this->participant,
            $this->version
        );

        $this->assertSame(
            EvaluationStatus::EXPIRED,
            $first->refresh()->status
        );

        $this->assertFalse(
            $first->is($second)
        );

        $this->assertDatabaseCount(
            'evaluations',
            2
        );
    }

    public function test_draft_version_cannot_start_evaluation(): void
    {
        $draft = QuestionnaireVersion::factory()
            ->create();

        $this->expectException(
            ValidationException::class
        );

        app(StartEvaluation::class)
            ->execute(
                $this->participant,
                $draft
            );
    }
}
