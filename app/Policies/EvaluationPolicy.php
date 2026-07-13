<?php

namespace App\Policies;

use App\Enums\RoleName;
use App\Models\Evaluation;
use App\Models\User;

class EvaluationPolicy
{
    /**
     * Solo un administrador puede consultar todas las evaluaciones.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(
            RoleName::ADMIN->value
        );
    }

    /**
     * El participante puede consultar únicamente sus evaluaciones.
     */
    public function view(
        User $user,
        Evaluation $evaluation
    ): bool {
        return $evaluation->user_id === $user->id
            || $user->hasRole(
                RoleName::ADMIN->value
            );
    }

    /**
     * Solo una cuenta activa con rol participante puede iniciar.
     */
    public function create(User $user): bool
    {
        return $user->canAuthenticate()
            && $user->hasRole(
                RoleName::PARTICIPANT->value
            );
    }

    /**
     * Solo el propietario puede modificar una evaluación activa.
     */
    public function update(
        User $user,
        Evaluation $evaluation
    ): bool {
        return $evaluation->user_id === $user->id
            && $evaluation->isInProgress()
            && ! $evaluation->hasExpired();
    }

    /**
     * Las evaluaciones no se eliminan desde la aplicación.
     */
    public function delete(
        User $user,
        Evaluation $evaluation
    ): bool {
        return false;
    }
}
