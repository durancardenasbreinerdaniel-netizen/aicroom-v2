<?php

namespace Tests\Feature\Results;

use App\Actions\Evaluations\CalculateEvaluationResult;
use App\Enums\EvaluationLevel;
use App\Models\Answer;
use App\Models\Evaluation;
use App\Models\EvaluationItem;
use App\Models\Skill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CalculateEvaluationResultTest extends TestCase
{
    use RefreshDatabase;

    public function test_result_is_calculated_using_weights_and_reverse_questions(): void
    {
        $evaluation = Evaluation::factory()
            ->submitted()
            ->create([
                'total_questions' => 4,
            ]);

        $firstSkill = Skill::factory()->create([
            'code' => 'COM',
            'name' => 'Comunicación',
        ]);

        $secondSkill = Skill::factory()->create([
            'code' => 'EMP',
            'name' => 'Empatía',
        ]);

        /*
         * Comunicación:
         *
         * Pregunta directa:
         * respuesta 5 × peso 2 = 10
         *
         * Pregunta inversa:
         * respuesta 5 se convierte en 1
         * 1 × peso 1 = 1
         *
         * Total:
         * raw = 11
         * min = 3
         * max = 15
         * normalizado = 66.67
         */
        $firstItem = EvaluationItem::factory()
            ->for($evaluation)
            ->for($firstSkill, 'skill')
            ->create([
                'position' => 1,
                'weight' => 2,
                'is_reverse_scored' => false,
            ]);

        $secondItem = EvaluationItem::factory()
            ->for($evaluation)
            ->for($firstSkill, 'skill')
            ->create([
                'position' => 2,
                'weight' => 1,
                'is_reverse_scored' => true,
            ]);

        /*
         * Empatía:
         *
         * Pregunta directa:
         * respuesta 1 = 1
         *
         * Pregunta inversa:
         * respuesta 1 se convierte en 5
         *
         * Total:
         * raw = 6
         * min = 2
         * max = 10
         * normalizado = 50
         */
        $thirdItem = EvaluationItem::factory()
            ->for($evaluation)
            ->for($secondSkill, 'skill')
            ->create([
                'position' => 3,
                'weight' => 1,
                'is_reverse_scored' => false,
            ]);

        $fourthItem = EvaluationItem::factory()
            ->for($evaluation)
            ->for($secondSkill, 'skill')
            ->create([
                'position' => 4,
                'weight' => 1,
                'is_reverse_scored' => true,
            ]);

        $this->createAnswer(
            $evaluation,
            $firstItem,
            5,
        );

        $this->createAnswer(
            $evaluation,
            $secondItem,
            5,
        );

        $this->createAnswer(
            $evaluation,
            $thirdItem,
            1,
        );

        $this->createAnswer(
            $evaluation,
            $fourthItem,
            1,
        );

        $result = app(
            CalculateEvaluationResult::class,
        )->execute(
            $evaluation,
        );

        /*
         * Resultado global:
         *
         * raw = 17
         * min = 5
         * max = 25
         *
         * ((17 - 5) / (25 - 5)) × 100 = 60
         */
        $this->assertSame(
            17,
            $result->raw_score,
        );

        $this->assertSame(
            5,
            $result->minimum_score,
        );

        $this->assertSame(
            25,
            $result->maximum_score,
        );

        $this->assertEqualsWithDelta(
            60,
            (float) $result->normalized_score,
            0.01,
        );

        $this->assertSame(
            EvaluationLevel::INTERMEDIATE,
            $result->level,
        );

        $this->assertDatabaseCount(
            'evaluation_skill_results',
            2,
        );

        $communicationResult = $result
            ->skillResults()
            ->where('skill_id', $firstSkill->id)
            ->firstOrFail();

        $this->assertEqualsWithDelta(
            66.67,
            (float) $communicationResult->normalized_score,
            0.01,
        );

        $empathyResult = $result
            ->skillResults()
            ->where('skill_id', $secondSkill->id)
            ->firstOrFail();

        $this->assertEqualsWithDelta(
            50,
            (float) $empathyResult->normalized_score,
            0.01,
        );
    }

    public function test_calculation_is_idempotent(): void
    {
        $evaluation = Evaluation::factory()
            ->submitted()
            ->create([
                'total_questions' => 1,
            ]);

        $skill = Skill::factory()->create();

        $item = EvaluationItem::factory()
            ->for($evaluation)
            ->for($skill, 'skill')
            ->create([
                'position' => 1,
            ]);

        $this->createAnswer(
            $evaluation,
            $item,
            4,
        );

        $action = app(
            CalculateEvaluationResult::class,
        );

        $firstResult = $action->execute(
            $evaluation,
        );

        $secondResult = $action->execute(
            $evaluation->refresh(),
        );

        $this->assertTrue(
            $firstResult->is($secondResult),
        );

        $this->assertDatabaseCount(
            'evaluation_results',
            1,
        );

        $this->assertDatabaseCount(
            'evaluation_skill_results',
            1,
        );
    }

    public function test_in_progress_evaluation_cannot_be_calculated(): void
    {
        $evaluation = Evaluation::factory()->create();

        $this->expectException(
            ValidationException::class,
        );

        app(CalculateEvaluationResult::class)
            ->execute(
                $evaluation,
            );
    }

    /**
     * Crea una respuesta asociada con un ítem.
     */
    private function createAnswer(
        Evaluation $evaluation,
        EvaluationItem $item,
        int $value,
    ): void {
        Answer::query()->create([
            'evaluation_id' => $evaluation->id,
            'evaluation_item_id' => $item->id,
            'answer_value' => $value,
            'answered_at' => now(),
        ]);
    }
}
