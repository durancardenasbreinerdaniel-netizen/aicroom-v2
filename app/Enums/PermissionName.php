<?php

namespace App\Enums;

/**
 * Permisos disponibles dentro de AICROOM.
 */
enum PermissionName: string
{
    case ACCESS_ADMIN_PANEL = 'access admin panel';

    case VIEW_ANY_SKILL = 'view any skill';
    case VIEW_SKILL = 'view skill';
    case CREATE_SKILL = 'create skill';
    case UPDATE_SKILL = 'update skill';
    case DELETE_SKILL = 'delete skill';
    case RESTORE_SKILL = 'restore skill';

    case VIEW_ANY_QUESTION = 'view any question';
    case VIEW_QUESTION = 'view question';
    case CREATE_QUESTION = 'create question';
    case UPDATE_QUESTION = 'update question';
    case DELETE_QUESTION = 'delete question';
    case RESTORE_QUESTION = 'restore question';

    case VIEW_ANY_QUESTIONNAIRE = 'view any questionnaire';
    case VIEW_QUESTIONNAIRE = 'view questionnaire';
    case CREATE_QUESTIONNAIRE = 'create questionnaire';
    case UPDATE_QUESTIONNAIRE = 'update questionnaire';
    case DELETE_QUESTIONNAIRE = 'delete questionnaire';
    case RESTORE_QUESTIONNAIRE = 'restore questionnaire';
    case PUBLISH_QUESTIONNAIRE = 'publish questionnaire';

    /**
     * Devuelve la etiqueta visible del permiso.
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

            self::VIEW_ANY_QUESTION => 'Consultar preguntas',
            self::VIEW_QUESTION => 'Consultar una pregunta',
            self::CREATE_QUESTION => 'Crear preguntas',
            self::UPDATE_QUESTION => 'Editar preguntas',
            self::DELETE_QUESTION => 'Eliminar preguntas',
            self::RESTORE_QUESTION => 'Restaurar preguntas',

            self::VIEW_ANY_QUESTIONNAIRE => 'Consultar cuestionarios',
            self::VIEW_QUESTIONNAIRE => 'Consultar un cuestionario',
            self::CREATE_QUESTIONNAIRE => 'Crear cuestionarios',
            self::UPDATE_QUESTIONNAIRE => 'Editar cuestionarios',
            self::DELETE_QUESTIONNAIRE => 'Eliminar cuestionarios',
            self::RESTORE_QUESTIONNAIRE => 'Restaurar cuestionarios',
            self::PUBLISH_QUESTIONNAIRE => 'Publicar cuestionarios',
        };
    }
}
