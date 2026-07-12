<?php

namespace App\Livewire\Forms\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class LoginForm extends Form
{
    #[Validate]
    public string $email = '';

    #[Validate]
    public string $password = '';

    public bool $remember = false;

    /**
     * Reglas de validación del formulario.
     *
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'email:rfc',
                'max:255',
            ],

            'password' => [
                'required',
                'string',
            ],

            'remember' => [
                'boolean',
            ],
        ];
    }

    /**
     * Intenta autenticar al usuario.
     */
    public function authenticate(): User
    {
        $this->ensureIsNotRateLimited();

        $authenticated = Auth::attempt(
            [
                'email' => Str::lower(trim($this->email)),
                'password' => $this->password,
            ],
            $this->remember,
        );

        if (! $authenticated) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'form.email' => 'Las credenciales proporcionadas no son correctas.',
            ]);
        }

        /** @var User $user */
        $user = Auth::user();

        /*
         * La contraseña puede ser correcta, pero la cuenta debe estar activa.
         */
        if (! $user->canAuthenticate()) {
            Auth::logout();

            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'form.email' => 'Esta cuenta no se encuentra habilitada.',
            ]);
        }

        /*
         * El inicio de sesión fue válido, por lo que se limpia
         * el contador de intentos fallidos.
         */
        RateLimiter::clear($this->throttleKey());

        return $user;
    }

    /**
     * Evita intentos de inicio de sesión ilimitados.
     */
    private function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'form.email' => trans(
                'auth.throttle',
                [
                    'seconds' => $seconds,
                    'minutes' => (int) ceil($seconds / 60),
                ],
            ),
        ]);
    }

    /**
     * Construye una clave por correo e IP.
     */
    private function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower(trim($this->email))
            .'|'
            .request()->ip()
        );
    }
}
