<?php

namespace App\Enums;

/**
 * Estados disponibles para una versión de cuestionario.
 */
enum QuestionnaireVersionStatus: string
{
    /**
     * La versión todavía puede modificarse.
     */
    case DRAFT = 'draft';

    /**
     * La versión está disponible para nuevas evaluaciones.
     */
    case PUBLISHED = 'published';

    /**
     * La versión fue reemplazada por una versión más reciente.
     */
    case RETIRED = 'retired';

    /**
     * Devuelve la etiqueta visible.
     */
    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Borrador',
            self::PUBLISHED => 'Publicada',
            self::RETIRED => 'Retirada',
        };
    }

    /**
     * Devuelve el color utilizado por Filament.
     */
    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::PUBLISHED => 'success',
            self::RETIRED => 'warning',
        };
    }
}