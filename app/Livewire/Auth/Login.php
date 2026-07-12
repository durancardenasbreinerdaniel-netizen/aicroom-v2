<?php

namespace App\Livewire\Auth;

use App\Livewire\Forms\Auth\LoginForm;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Iniciar sesión | AICROOM')]
class Login extends Component
{
    public LoginForm $form;

    /**
     * Inicia sesión y redirige al usuario.
     */
    public function login(): mixed
    {
        /*
         * Valida los campos definidos dentro del formulario Livewire.
         */
        $this->form->validate();

        /*
         * Intenta autenticar al usuario y devuelve el modelo autenticado.
         */
        $user = $this->form->authenticate();

        /*
         * Regenera el identificador de sesión después de autenticar.
         *
         * Utilizamos la fachada Session en lugar de request()->session()
         * porque las pruebas de componentes Livewire no siempre adjuntan
         * el almacén de sesión directamente al objeto Request.
         */
        Session::regenerate();

        /*
         * Registra la fecha del último inicio de sesión exitoso.
         */
        $user->forceFill([
            'last_login_at' => now(),
        ])->save();

        /*
         * Se realiza una redirección HTTP completa porque la sesión
         * y el token CSRF fueron regenerados.
         */
        return redirect()->intended(
            route('dashboard')
        );
    }

    /**
     * Renderiza el formulario de inicio de sesión.
     */
    public function render(): View
    {
        return view('livewire.auth.login');
    }
}
