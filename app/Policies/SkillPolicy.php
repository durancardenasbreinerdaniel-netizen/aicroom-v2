<?php

namespace App\Policies;

use App\Enums\PermissionName;
use App\Models\Skill;
use App\Models\User;

class SkillPolicy
{
    /**
     * Determina si el usuario puede consultar el listado.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(
            PermissionName::VIEW_ANY_SKILL->value
        );
    }

    /**
     * Determina si el usuario puede consultar una habilidad.
     */
    public function view(User $user, Skill $skill): bool
    {
        return $user->can(
            PermissionName::VIEW_SKILL->value
        );
    }

    /**
     * Determina si el usuario puede crear habilidades.
     */
    public function create(User $user): bool
    {
        return $user->can(
            PermissionName::CREATE_SKILL->value
        );
    }

    /**
     * Determina si el usuario puede editar una habilidad.
     */
    public function update(User $user, Skill $skill): bool
    {
        return $user->can(
            PermissionName::UPDATE_SKILL->value
        );
    }

    /**
     * Determina si el usuario puede eliminar una habilidad.
     */
    public function delete(User $user, Skill $skill): bool
    {
        return $user->can(
            PermissionName::DELETE_SKILL->value
        );
    }

    /**
     * Autoriza eliminaciones masivas.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can(
            PermissionName::DELETE_SKILL->value
        );
    }

    /**
     * Determina si el usuario puede restaurar una habilidad.
     */
    public function restore(User $user, Skill $skill): bool
    {
        return $user->can(
            PermissionName::RESTORE_SKILL->value
        );
    }

    /**
     * Autoriza restauraciones masivas.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can(
            PermissionName::RESTORE_SKILL->value
        );
    }

    /**
     * Las habilidades nunca se eliminarán físicamente.
     */
    public function forceDelete(User $user, Skill $skill): bool
    {
        return false;
    }

    /**
     * Impide eliminaciones físicas masivas.
     */
    public function forceDeleteAny(User $user): bool
    {
        return false;
    }
}
