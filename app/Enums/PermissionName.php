<?php

namespace App\Enums;

/**
 * Permisos disponibles dentro de AICROOM.
 */
enum PermissionName: string
{
    /**
     * Permite ingresar al panel administrativo.
     */
    case ACCESS_ADMIN_PANEL = 'access admin panel';

    /**
     * Permite consultar el listado de habilidades.
     */
    case VIEW_ANY_SKILL = 'view any skill';

    /**
     * Permite consultar una habilidad específica.
     */
    case VIEW_SKILL = 'view skill';

    /**
     * Permite crear habilidades.
     */
    case CREATE_SKILL = 'create skill';

    /**
     * Permite editar habilidades.
     */
    case UPDATE_SKILL = 'update skill';

    /**
     * Permite enviar habilidades a la papelera.
     */
    case DELETE_SKILL = 'delete skill';

    /**
     * Permite restaurar habilidades eliminadas.
     */
    case RESTORE_SKILL = 'restore skill';

    /**
     * Devuelve el nombre legible del permiso.
     */
    public function label(): string
    {
        return match ($this) {
            self::ACCESS_ADMIN_PANEL => 'Acceder al panel administrativo',
            self::VIEW_ANY_SKILL => 'Consultar habilidades',
            self::VIEW_SKILL => 'Consultar una habilidad',
            self::CREATE_SKILL => 'Crear habilidades',
            self::UPDATE_SKILL => 'Editar habilidades',
            self::DELETE_SKILL => 'Eliminar habilidades',
            self::RESTORE_SKILL => 'Restaurar habilidades',
        };
    }
}
