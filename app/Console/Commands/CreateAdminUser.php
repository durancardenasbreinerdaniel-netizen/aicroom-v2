<?php

namespace App\Console\Commands;

use App\Enums\RoleName;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class CreateAdminUser extends Command
{
    /**
     * Nombre y argumentos del comando.
     */
    protected $signature = 'aicroom:create-admin';

    /**
     * Descripción mostrada en Artisan.
     */
    protected $description = 'Crea un usuario administrador para AICROOM';

    /**
     * Ejecuta el comando.
     */
    public function handle(): int
    {
        $name = (string) $this->ask('Nombre');
        $lastName = (string) $this->ask('Apellido');
        $email = strtolower(
            trim((string) $this->ask('Correo electrónico'))
        );
        $phone = $this->ask('Teléfono opcional');
        $password = (string) $this->secret('Contraseña');
        $passwordConfirmation = (string) $this->secret(
            'Confirmar contraseña'
        );

        $validator = Validator::make(
            [
                'name' => $name,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => $phone,
                'password' => $password,
                'password_confirmation' => $passwordConfirmation,
            ],
            [
                'name' => [
                    'required',
                    'string',
                    'min:2',
                    'max:100',
                ],

                'last_name' => [
                    'required',
                    'string',
                    'min:2',
                    'max:100',
                ],

                'email' => [
                    'required',
                    'email:rfc',
                    'max:255',
                    Rule::unique(User::class, 'email'),
                ],

                'phone' => [
                    'nullable',
                    'string',
                    'max:30',
                ],

                'password' => [
                    'required',
                    'confirmed',
                    Password::defaults(),
                ],
            ],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::query()->create([
            'name' => trim($name),
            'last_name' => trim($lastName),
            'email' => $email,
            'phone' => $phone !== null
                ? trim((string) $phone)
                : null,
            'password' => $password,
            'status' => UserStatus::ACTIVE,
        ]);

        $user->assignRole(RoleName::ADMIN->value);

        $this->newLine();
        $this->info('Administrador creado correctamente.');
        $this->line("Nombre: {$user->full_name}");
        $this->line("Correo: {$user->email}");

        return self::SUCCESS;
    }
}
