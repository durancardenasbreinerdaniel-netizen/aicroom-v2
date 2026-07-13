<?php

namespace App\Enums;

/**
 * Tipos de evaluación disponibles.
 */
enum EvaluationType: string
{
    /**
     * Primera evaluación realizada por el participante.
     */
    case BASELINE = 'baseline';

    /**
     * Evaluación posterior utilizada para medir evolución.
     */
    case FOLLOW_UP = 'follow_up';

    /**
     * Devuelve una etiqueta legible.
     */
    public function label(): string
    {
        return match ($this) {
            self::BASELINE => 'Evaluación inicial',
            self::FOLLOW_UP => 'Reevaluación',
        };
    }
}
