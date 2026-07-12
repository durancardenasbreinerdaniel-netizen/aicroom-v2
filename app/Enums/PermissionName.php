<?php

namespace App\Enums;

/**
 * Permisos disponibles inicialmente en AICROOM.
 *
 * En fases posteriores agregaremos permisos específicos para administrar
 * usuarios, habilidades, preguntas, cuestionarios y evaluaciones.
 */
enum PermissionName: string
{
    /**
     * Permite ingresar al panel administrativo de Filament.
     */
    case ACCESS_ADMIN_PANEL = 'access admin panel';

    /**
     * Devuelve el nombre legible del permiso.
     */
    public function label(): string
    {
        return match ($this) {
            self::ACCESS_ADMIN_PANEL => 'Acceder al panel administrativo',
        };
    }
}
