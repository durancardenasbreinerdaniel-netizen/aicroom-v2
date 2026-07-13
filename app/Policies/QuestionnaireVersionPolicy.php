<?php

namespace App\Policies;

use App\Enums\PermissionName;
use App\Models\QuestionnaireVersion;
use App\Models\User;

class QuestionnaireVersionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(
            PermissionName::VIEW_ANY_QUESTIONNAIRE->value
        );
    }

    public function view(
        User $user,
        QuestionnaireVersion $version
    ): bool {
        return $user->can(
            PermissionName::VIEW_QUESTIONNAIRE->value
        );
    }

    public function create(User $user): bool
    {
        return $user->can(
            PermissionName::UPDATE_QUESTIONNAIRE->value
        );
    }

    /**
     * Solo los borradores pueden editarse.
     */
    public function update(
        User $user,
        QuestionnaireVersion $version
    ): bool {
        return $version->isDraft()
            && $user->can(
                PermissionName::UPDATE_QUESTIONNAIRE->value
            );
    }

    /**
     * Solo los borradores pueden eliminarse.
     */
    public function delete(
        User $user,
        QuestionnaireVersion $version
    ): bool {
        return $version->isDraft()
            && $user->can(
                PermissionName::DELETE_QUESTIONNAIRE->value
            );
    }

    /**
     * Autoriza la publicación.
     */
    public function publish(
        User $user,
        QuestionnaireVersion $version
    ): bool {
        return $version->isDraft()
            && $user->can(
                PermissionName::PUBLISH_QUESTIONNAIRE->value
            );
    }
}
