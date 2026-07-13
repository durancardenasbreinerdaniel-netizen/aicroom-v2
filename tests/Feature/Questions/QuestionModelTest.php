<?php

namespace Tests\Feature\Questions;

use App\Enums\LikertValue;
use App\Models\Question;
use App\Models\Skill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ValueError;

class QuestionModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_question_belongs_to_skill(): void
    {
        $skill = Skill::factory()->create();

        $question = Question::factory()
            ->for($skill)
            ->create();

        $this->assertTrue(
            $question->skill->is($skill)
        );
    }

    public function test_statement_is_normalized(): void
    {
        $question = Question::factory()->create([
            'statement' => '  Expreso   mis ideas   con claridad.  ',
        ]);

        $this->assertSame(
            'Expreso mis ideas con claridad.',
            $question->statement,
        );
    }

    public function test_active_scope_excludes_inactive_questions(): void
    {
        Question::factory()->create([
            'statement' => 'Pregunta activa para la prueba.',
            'is_active' => true,
        ]);

        Question::factory()
            ->inactive()
            ->create([
                'statement' => 'Pregunta inactiva para la prueba.',
            ]);

        $questions = Question::query()
            ->active()
            ->get();

        $this->assertCount(1, $questions);

        $this->assertSame(
            'Pregunta activa para la prueba.',
            $questions->first()->statement,
        );
    }

    public function test_direct_question_preserves_answer_value(): void
    {
        $question = Question::factory()->create([
            'is_reverse_scored' => false,
            'weight' => 1,
        ]);

        $this->assertSame(
            5,
            $question->scoredValue(LikertValue::ALWAYS),
        );
    }

    public function test_reverse_question_inverts_answer_value(): void
    {
        $question = Question::factory()
            ->reverseScored()
            ->create();

        $this->assertSame(
            1,
            $question->scoredValue(LikertValue::ALWAYS),
        );

        $this->assertSame(
            5,
            $question->scoredValue(LikertValue::NEVER),
        );
    }

    public function test_weight_is_applied_to_score(): void
    {
        $question = Question::factory()
            ->weighted(3)
            ->create();

        $this->assertSame(
            12,
            $question->weightedScore(LikertValue::OFTEN),
        );

        $this->assertSame(
            3,
            $question->minimumWeightedScore(),
        );

        $this->assertSame(
            15,
            $question->maximumWeightedScore(),
        );
    }

    public function test_invalid_likert_value_is_rejected(): void
    {
        $question = Question::factory()->create();

        $this->expectException(ValueError::class);

        $question->scoredValue(6);
    }

    public function test_question_uses_soft_deletes(): void
    {
        $question = Question::factory()->create();

        $question->delete();

        $this->assertSoftDeleted($question);

        $this->assertNull(
            Question::query()->find($question->id)
        );

        $this->assertNotNull(
            Question::withTrashed()->find($question->id)
        );
    }
}
