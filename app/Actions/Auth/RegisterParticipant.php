<?php

namespace App\Actions\Auth;

use App\Enums\RoleName;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegisterParticipant
{
    /**
     * Registra un participante y le asigna su rol inicial.
     *
     * @param  array{
     *     name: string,
     *     last_name: string,
     *     email: string,
     *     phone: string|null,
     *     password: string
     * }  $data
     */
    public function execute(array $data): User
    {
        return DB::transaction(function () use ($data): User {
            $user = User::query()->create([
                'name' => Str::squish($data['name']),
                'last_name' => Str::squish($data['last_name']),
                'email' => Str::lower(trim($data['email'])),
                'phone' => $this->normalizePhone($data['phone']),
                'password' => $data['password'],
                'status' => UserStatus::ACTIVE,
            ]);

            /*
             * Todo usuario registrado desde el portal público
             * comienza como participante.
             */
            $user->assignRole(RoleName::PARTICIPANT->value);

            return $user;
        });
    }

    /**
     * Normaliza el teléfono antes de almacenarlo.
     */
    private function normalizePhone(?string $phone): ?string
    {
        if ($phone === null || trim($phone) === '') {
            return null;
        }

        return trim($phone);
    }
}
