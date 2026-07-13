<?php

namespace Tests\Unit;

use App\Enums\EvaluationLevel;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class EvaluationLevelTest extends TestCase
{
    public function test_low_level_is_assigned_correctly(): void
    {
        $this->assertSame(
            EvaluationLevel::LOW,
            EvaluationLevel::fromNormalizedScore(49.99),
        );
    }

    public function test_intermediate_level_is_assigned_correctly(): void
    {
        $this->assertSame(
            EvaluationLevel::INTERMEDIATE,
            EvaluationLevel::fromNormalizedScore(50),
        );

        $this->assertSame(
            EvaluationLevel::INTERMEDIATE,
            EvaluationLevel::fromNormalizedScore(74.99),
        );
    }

    public function test_advanced_level_is_assigned_correctly(): void
    {
        $this->assertSame(
            EvaluationLevel::ADVANCED,
            EvaluationLevel::fromNormalizedScore(75),
        );

        $this->assertSame(
            EvaluationLevel::ADVANCED,
            EvaluationLevel::fromNormalizedScore(100),
        );
    }

    public function test_invalid_score_is_rejected(): void
    {
        $this->expectException(
            InvalidArgumentException::class,
        );

        EvaluationLevel::fromNormalizedScore(
            101,
        );
    }
}
