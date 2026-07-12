<?php

namespace App\Enums;

/**
 * Estados disponibles para una cuenta de usuario.
 */
enum UserStatus: string
{
    /**
     * El usuario puede iniciar sesión y utilizar el sistema.
     */
    case ACTIVE = 'active';

    /**
     * El usuario existe, pero no puede iniciar sesión.
     */
    case INACTIVE = 'inactive';

    /**
     * El usuario fue bloqueado por un administrador.
     */
    case BLOCKED = 'blocked';

    /**
     * Devuelve el nombre legible del estado.
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Activo',
            self::INACTIVE => 'Inactivo',
            self::BLOCKED => 'Bloqueado',
        };
    }
}
