<?php

namespace App\Enums;

/**
 * Estados disponibles durante el ciclo de vida de una evaluación.
 */
enum EvaluationStatus: string
{
    /**
     * La evaluación fue creada y todavía acepta respuestas.
     */
    case IN_PROGRESS = 'in_progress';

    /**
     * El tiempo límite terminó antes de finalizarla.
     */
    case EXPIRED = 'expired';

    /**
     * El participante envió todas sus respuestas.
     *
     * Este estado se utilizará en la siguiente fase.
     */
    case SUBMITTED = 'submitted';

    /**
     * La evaluación fue cancelada.
     */
    case CANCELLED = 'cancelled';

    /**
     * Devuelve una etiqueta legible.
     */
    public function label(): string
    {
        return match ($this) {
            self::IN_PROGRESS => 'En progreso',
            self::EXPIRED => 'Expirada',
            self::SUBMITTED => 'Enviada',
            self::CANCELLED => 'Cancelada',
        };
    }

    /**
     * Devuelve un color compatible con Flux y Filament.
     */
    public function color(): string
    {
        return match ($this) {
            self::IN_PROGRESS => 'blue',
            self::EXPIRED => 'red',
            self::SUBMITTED => 'green',
            self::CANCELLED => 'zinc',
        };
    }

    /**
     * Indica si la evaluación se encuentra cerrada.
     */
    public function isClosed(): bool
    {
        return match ($this) {
            self::IN_PROGRESS => false,
            self::EXPIRED,
            self::SUBMITTED,
            self::CANCELLED => true,
        };
    }
}
