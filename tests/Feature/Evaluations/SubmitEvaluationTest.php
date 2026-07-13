<?php

namespace Tests\Feature\Evaluations;

use App\Actions\Evaluations\SaveEvaluationAnswer;
use App\Actions\Evaluations\SubmitEvaluation;
use App\Enums\EvaluationStatus;
use App\Models\Evaluation;
use App\Models\EvaluationItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SubmitEvaluationTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_evaluation_can_be_submitted(): void
    {
        $participant = User::factory()->create();

        $evaluation = Evaluation::factory()
            ->for($participant)
            ->create([
                'total_questions' => 2,
            ]);

        $firstItem = EvaluationItem::factory()
            ->for($evaluation)
            ->create([
                'position' => 1,
            ]);

        $secondItem = EvaluationItem::factory()
            ->for($evaluation)
            ->create([
                'position' => 2,
            ]);

        $saveAnswer = app(
            SaveEvaluationAnswer::class,
        );

        $saveAnswer->execute(
            $participant,
            $evaluation,
            $firstItem,
            4,
        );

        $saveAnswer->execute(
            $participant,
            $evaluation,
            $secondItem,
            5,
        );

        $submittedEvaluation = app(
            SubmitEvaluation::class,
        )->execute(
            $participant,
            $evaluation,
        );

        $this->assertSame(
            EvaluationStatus::SUBMITTED,
            $submittedEvaluation->status,
        );

        $this->assertNotNull(
            $submittedEvaluation->submitted_at,
        );
    }

    public function test_incomplete_evaluation_cannot_be_submitted(): void
    {
        $participant = User::factory()->create();

        $evaluation = Evaluation::factory()
            ->for($participant)
            ->create([
                'total_questions' => 2,
            ]);

        $item = EvaluationItem::factory()
            ->for($evaluation)
            ->create([
                'position' => 1,
            ]);

        app(SaveEvaluationAnswer::class)->execute(
            $participant,
            $evaluation,
            $item,
            4,
        );

        $this->expectException(
            ValidationException::class,
        );

        app(SubmitEvaluation::class)->execute(
            $participant,
            $evaluation,
        );
    }

    public function test_submitting_twice_is_idempotent(): void
    {
        $participant = User::factory()->create();

        $evaluation = Evaluation::factory()
            ->for($participant)
            ->create([
                'total_questions' => 1,
            ]);

        $item = EvaluationItem::factory()
            ->for($evaluation)
            ->create();

        app(SaveEvaluationAnswer::class)->execute(
            $participant,
            $evaluation,
            $item,
            5,
        );

        $action = app(
            SubmitEvaluation::class,
        );

        $firstResult = $action->execute(
            $participant,
            $evaluation,
        );

        $secondResult = $action->execute(
            $participant,
            $evaluation->refresh(),
        );

        $this->assertTrue(
            $firstResult->is($secondResult),
        );

        $this->assertSame(
            EvaluationStatus::SUBMITTED,
            $secondResult->status,
        );
    }
}
