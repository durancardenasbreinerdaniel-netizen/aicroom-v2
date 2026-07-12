<?php

namespace App\Enums;

/**
 * Roles principales de AICROOM.
 */
enum RoleName: string
{
    /**
     * Tiene acceso al panel administrativo.
     */
    case ADMIN = 'admin';

    /**
     * Puede realizar evaluaciones y consultar sus resultados.
     */
    case PARTICIPANT = 'participant';

    /**
     * Devuelve el nombre legible del rol.
     */
    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrador',
            self::PARTICIPANT => 'Participante',
        };
    }
}
