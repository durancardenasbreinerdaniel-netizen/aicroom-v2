<?php

namespace App\Enums;

use InvalidArgumentException;

/**
 * Niveles descriptivos utilizados para presentar los resultados.
 *
 * Estos niveles no representan un diagnóstico psicológico o clínico.
 */
enum EvaluationLevel: string
{
    case LOW = 'low';
    case INTERMEDIATE = 'intermediate';
    case ADVANCED = 'advanced';

    /**
     * Determina el nivel correspondiente a un puntaje normalizado.
     */
    public static function fromNormalizedScore(
        float $score,
    ): self {
        if ($score < 0 || $score > 100) {
            throw new InvalidArgumentException(
                'El puntaje normalizado debe estar entre 0 y 100.',
            );
        }

        return match (true) {
            $score < 50 => self::LOW,
            $score < 75 => self::INTERMEDIATE,
            default => self::ADVANCED,
        };
    }

    /**
     * Etiqueta visible para el usuario.
     */
    public function label(): string
    {
        return match ($this) {
            self::LOW => 'Bajo',
            self::INTERMEDIATE => 'Intermedio',
            self::ADVANCED => 'Avanzado',
        };
    }

    /**
     * Color utilizado por Flux UI.
     */
    public function color(): string
    {
        return match ($this) {
            self::LOW => 'red',
            self::INTERMEDIATE => 'amber',
            self::ADVANCED => 'green',
        };
    }

    /**
     * Descripción general del nivel.
     */
    public function description(): string
    {
        return match ($this) {
            self::LOW => 'La habilidad presenta oportunidades importantes de fortalecimiento.',
            self::INTERMEDIATE => 'La habilidad se encuentra en desarrollo y muestra un desempeño funcional.',
            self::ADVANCED => 'La habilidad muestra un desempeño consistente y fortalecido.',
        };
    }
}
