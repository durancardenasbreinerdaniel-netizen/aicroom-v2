<?php

namespace Database\Factories;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Contraseña compartida por los usuarios de prueba.
     */
    protected static ?string $password;

    /**
     * Estado predeterminado del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('Password1'),
            'status' => UserStatus::ACTIVE,
            'last_login_at' => null,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Genera una cuenta sin correo verificado.
     */
    public function unverified(): static
    {
        return $this->state(fn (): array => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Genera una cuenta inactiva.
     */
    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'status' => UserStatus::INACTIVE,
        ]);
    }

    /**
     * Genera una cuenta bloqueada.
     */
    public function blocked(): static
    {
        return $this->state(fn (): array => [
            'status' => UserStatus::BLOCKED,
        ]);
    }
}
