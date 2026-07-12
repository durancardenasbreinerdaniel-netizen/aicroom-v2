<?php

namespace App\Livewire\Forms\Auth;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Validate;
use Livewire\Form;

class RegisterForm extends Form
{
    #[Validate]
    public string $name = '';

    #[Validate]
    public string $lastName = '';

    #[Validate]
    public string $email = '';

    #[Validate]
    public string $phone = '';

    #[Validate]
    public string $password = '';

    #[Validate]
    public string $passwordConfirmation = '';

    /**
     * Reglas de validación del registro.
     *
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
            ],

            'lastName' => [
                'required',
                'string',
                'min:2',
                'max:100',
            ],

            'email' => [
                'required',
                'string',
                'lowercase',
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
                Password::defaults(),
                'same:passwordConfirmation',
            ],

            'passwordConfirmation' => [
                'required',
                'string',
            ],
        ];
    }

    /**
     * Nombres legibles utilizados en los mensajes de error.
     *
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'name' => 'nombre',
            'lastName' => 'apellido',
            'email' => 'correo electrónico',
            'phone' => 'teléfono',
            'password' => 'contraseña',
            'passwordConfirmation' => 'confirmación de contraseña',
        ];
    }

    /**
     * Devuelve los datos normalizados para la acción de registro.
     *
     * @return array{
     *     name: string,
     *     last_name: string,
     *     email: string,
     *     phone: string|null,
     *     password: string
     * }
     */
    public function data(): array
    {
        return [
            'name' => $this->name,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'phone' => $this->phone !== '' ? $this->phone : null,
            'password' => $this->password,
        ];
    }
}
