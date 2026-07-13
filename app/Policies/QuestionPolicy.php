<?php

namespace App\Policies;

use App\Enums\PermissionName;
use App\Models\Question;
use App\Models\User;

class QuestionPolicy
{
    /**
     * Autoriza el listado de preguntas.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(
            PermissionName::VIEW_ANY_QUESTION->value
        );
    }

    /**
     * Autoriza la consulta de una pregunta.
     */
    public function view(User $user, Question $question): bool
    {
        return $user->can(
            PermissionName::VIEW_QUESTION->value
        );
    }

    /**
     * Autoriza la creación.
     */
    public function create(User $user): bool
    {
        return $user->can(
            PermissionName::CREATE_QUESTION->value
        );
    }

    /**
     * Autoriza la edición.
     */
    public function update(User $user, Question $question): bool
    {
        return $user->can(
            PermissionName::UPDATE_QUESTION->value
        );
    }

    /**
     * Autoriza el borrado lógico.
     */
    public function delete(User $user, Question $question): bool
    {
        return $user->can(
            PermissionName::DELETE_QUESTION->value
        );
    }

    /**
     * Autoriza el borrado lógico masivo.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can(
            PermissionName::DELETE_QUESTION->value
        );
    }

    /**
     * Autoriza la restauración.
     */
    public function restore(User $user, Question $question): bool
    {
        return $user->can(
            PermissionName::RESTORE_QUESTION->value
        );
    }

    /**
     * Autoriza la restauración masiva.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can(
            PermissionName::RESTORE_QUESTION->value
        );
    }

    /**
     * Las preguntas nunca se eliminan físicamente.
     */
    public function forceDelete(User $user, Question $question): bool
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
