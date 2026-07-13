<?php

namespace App\Enums;

/**
 * Valores permitidos para las respuestas de una evaluación.
 */
enum LikertValue: int
{
    case NEVER = 1;
    case RARELY = 2;
    case SOMETIMES = 3;
    case OFTEN = 4;
    case ALWAYS = 5;

    /**
     * Devuelve la etiqueta visible para el participante.
     */
    public function label(): string
    {
        return match ($this) {
            self::NEVER => 'Nunca',
            self::RARELY => 'Rara vez',
            self::SOMETIMES => 'Algunas veces',
            self::OFTEN => 'Frecuentemente',
            self::ALWAYS => 'Siempre',
        };
    }

    /**
     * Devuelve el valor inverso de una respuesta.
     *
     * Ejemplos:
     *
     * 1 se convierte en 5.
     * 2 se convierte en 4.
     * 3 permanece en 3.
     */
    public function reversed(): self
    {
        return self::from(6 - $this->value);
    }

    /**
     * Devuelve el valor mínimo permitido.
     */
    public static function minimum(): int
    {
        return self::NEVER->value;
    }

    /**
     * Devuelve el valor máximo permitido.
     */
    public static function maximum(): int
    {
        return self::ALWAYS->value;
    }

    /**
     * Devuelve todas las opciones en formato valor-etiqueta.
     *
     * @return array<int, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
