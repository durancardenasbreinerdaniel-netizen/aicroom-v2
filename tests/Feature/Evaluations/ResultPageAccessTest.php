<?php

namespace Tests\Feature\Results;

use App\Actions\Evaluations\CalculateEvaluationResult;
use App\Models\Answer;
use App\Models\Evaluation;
use App\Models\EvaluationItem;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResultPageAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_participant_can_view_own_result(): void
    {
        $participant = User::factory()->create();

        $evaluation = $this->createResultFor(
            $participant,
        );

        $this
            ->actingAs($participant)
            ->get(
                route(
                    'results.show',
                    $evaluation,
                ),
            )
            ->assertOk()
            ->assertSee(
                'Resultado de la evaluación',
            )
            ->assertSee(
                'Resultados por habilidad',
            );
    }

    public function test_participant_cannot_view_another_users_result(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $evaluation = $this->createResultFor(
            $owner,
        );

        $this
            ->actingAs($otherUser)
            ->get(
                route(
                    'results.show',
                    $evaluation,
                ),
            )
            ->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $participant = User::factory()->create();

        $evaluation = $this->createResultFor(
            $participant,
        );

        $this
            ->get(
                route(
                    'results.show',
                    $evaluation,
                ),
            )
            ->assertRedirect(
                route('login'),
            );
    }

    /**
     * Crea una evaluación finalizada con un resultado.
     */
    private function createResultFor(
        User $participant,
    ): Evaluation {
        $evaluation = Evaluation::factory()
            ->submitted()
            ->for($participant)
            ->create([
                'total_questions' => 1,
            ]);

        $skill = Skill::factory()->create([
            'code' => 'COM',
            'name' => 'Comunicación',
        ]);

        $item = EvaluationItem::factory()
            ->for($evaluation)
            ->for($skill, 'skill')
            ->create([
                'position' => 1,
            ]);

        Answer::query()->create([
            'evaluation_id' => $evaluation->id,
            'evaluation_item_id' => $item->id,
            'answer_value' => 4,
            'answered_at' => now(),
        ]);

        app(
            CalculateEvaluationResult::class,
        )->execute(
            $evaluation,
        );

        return $evaluation->refresh();
    }
}
