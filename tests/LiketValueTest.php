<?php

namespace Tests\Unit;

use App\Enums\LikertValue;
use PHPUnit\Framework\TestCase;

class LikertValueTest extends TestCase
{
    public function test_likert_values_have_correct_labels(): void
    {
        $this->assertSame(
            'Nunca',
            LikertValue::NEVER->label(),
        );

        $this->assertSame(
            'Algunas veces',
            LikertValue::SOMETIMES->label(),
        );

        $this->assertSame(
            'Siempre',
            LikertValue::ALWAYS->label(),
        );
    }

    public function test_likert_values_can_be_reversed(): void
    {
        $this->assertSame(
            LikertValue::ALWAYS,
            LikertValue::NEVER->reversed(),
        );

        $this->assertSame(
            LikertValue::OFTEN,
            LikertValue::RARELY->reversed(),
        );

        $this->assertSame(
            LikertValue::SOMETIMES,
            LikertValue::SOMETIMES->reversed(),
        );
    }

    public function test_options_return_all_five_values(): void
    {
        $this->assertSame([
            1 => 'Nunca',
            2 => 'Rara vez',
            3 => 'Algunas veces',
            4 => 'Frecuentemente',
            5 => 'Siempre',
        ], LikertValue::options());
    }
}
