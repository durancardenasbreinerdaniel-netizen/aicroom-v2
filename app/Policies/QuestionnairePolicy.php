<?php

namespace App\Policies;

use App\Enums\PermissionName;
use App\Models\Questionnaire;
use App\Models\User;

class QuestionnairePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(
            PermissionName::VIEW_ANY_QUESTIONNAIRE->value
        );
    }

    public function view(
        User $user,
        Questionnaire $questionnaire
    ): bool {
        return $user->can(
            PermissionName::VIEW_QUESTIONNAIRE->value
        );
    }

    public function create(User $user): bool
    {
        return $user->can(
            PermissionName::CREATE_QUESTIONNAIRE->value
        );
    }

    public function update(
        User $user,
        Questionnaire $questionnaire
    ): bool {
        return $user->can(
            PermissionName::UPDATE_QUESTIONNAIRE->value
        );
    }

    public function delete(
        User $user,
        Questionnaire $questionnaire
    ): bool {
        return $user->can(
            PermissionName::DELETE_QUESTIONNAIRE->value
        );
    }

    public function deleteAny(User $user): bool
    {
        return $user->can(
            PermissionName::DELETE_QUESTIONNAIRE->value
        );
    }

    public function restore(
        User $user,
        Questionnaire $questionnaire
    ): bool {
        return $user->can(
            PermissionName::RESTORE_QUESTIONNAIRE->value
        );
    }

    public function restoreAny(User $user): bool
    {
        return $user->can(
            PermissionName::RESTORE_QUESTIONNAIRE->value
        );
    }

    /**
     * Los cuestionarios no se eliminan físicamente.
     */
    public function forceDelete(
        User $user,
        Questionnaire $questionnaire
    ): bool {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }
}