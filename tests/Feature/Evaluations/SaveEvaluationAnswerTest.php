<?php

namespace Tests\Feature\Evaluations;

use App\Actions\Evaluations\SaveEvaluationAnswer;
use App\Enums\EvaluationStatus;
use App\Models\Evaluation;
use App\Models\EvaluationItem;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SaveEvaluationAnswerTest extends TestCase
{
    use RefreshDatabase;

    public function test_participant_can_save_answer(): void
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

        $answer = app(
            SaveEvaluationAnswer::class,
        )->execute(
            $participant,
            $evaluation,
            $item,
            4,
        );

        $this->assertSame(
            4,
            $answer->answer_value->value,
        );

        $this->assertDatabaseHas('answers', [
            'evaluation_id' => $evaluation->id,
            'evaluation_item_id' => $item->id,
            'answer_value' => 4,
        ]);
    }

    public function test_existing_answer_is_updated(): void
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

        $action = app(
            SaveEvaluationAnswer::class,
        );

        $action->execute(
            $participant,
            $evaluation,
            $item,
            2,
        );

        $action->execute(
            $participant,
            $evaluation,
            $item,
            5,
        );

        $this->assertDatabaseCount(
            'answers',
            1,
        );

        $this->assertDatabaseHas('answers', [
            'evaluation_item_id' => $item->id,
            'answer_value' => 5,
        ]);
    }

    public function test_invalid_likert_value_is_rejected(): void
    {
        $participant = User::factory()->create();

        $evaluation = Evaluation::factory()
            ->for($participant)
            ->create();

        $item = EvaluationItem::factory()
            ->for($evaluation)
            ->create();

        $this->expectException(
            ValidationException::class,
        );

        app(SaveEvaluationAnswer::class)->execute(
            $participant,
            $evaluation,
            $item,
            6,
        );
    }

    public function test_another_user_cannot_save_answer(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $evaluation = Evaluation::factory()
            ->for($owner)
            ->create();

        $item = EvaluationItem::factory()
            ->for($evaluation)
            ->create();

        $this->expectException(
            AuthorizationException::class,
        );

        app(SaveEvaluationAnswer::class)->execute(
            $otherUser,
            $evaluation,
            $item,
            3,
        );
    }

    public function test_expired_evaluation_rejects_answers(): void
    {
        $participant = User::factory()->create();

        $evaluation = Evaluation::factory()
            ->for($participant)
            ->create([
                'expires_at' => now()->subMinute(),
            ]);

        $item = EvaluationItem::factory()
            ->for($evaluation)
            ->create();

        try {
            app(SaveEvaluationAnswer::class)->execute(
                $participant,
                $evaluation,
                $item,
                3,
            );

            $this->fail(
                'La evaluación expirada aceptó una respuesta.',
            );
        } catch (ValidationException) {
            $this->assertSame(
                EvaluationStatus::EXPIRED,
                $evaluation->refresh()->status,
            );
        }
    }

    public function test_submitted_evaluation_rejects_answers(): void
    {
        $participant = User::factory()->create();

        $evaluation = Evaluation::factory()
            ->submitted()
            ->for($participant)
            ->create();

        $item = EvaluationItem::factory()
            ->for($evaluation)
            ->create();

        $this->expectException(
            ValidationException::class,
        );

        app(SaveEvaluationAnswer::class)->execute(
            $participant,
            $evaluation,
            $item,
            3,
        );
    }
}
