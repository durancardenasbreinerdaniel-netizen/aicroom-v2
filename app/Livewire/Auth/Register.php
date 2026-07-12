<?php

namespace App\Livewire\Auth;

use App\Actions\Auth\RegisterParticipant;
use App\Livewire\Forms\Auth\RegisterForm;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Crear cuenta | AICROOM')]
class Register extends Component
{
    public RegisterForm $form;

    /**
     * Registra y autentica un nuevo participante.
     */
    public function register(
        RegisterParticipant $registerParticipant
    ): mixed {
        /*
         * Valida todos los datos del formulario.
         */
        $this->form->validate();

        /*
         * Crea el usuario y le asigna el rol de participante.
         */
        $user = $registerParticipant->execute(
            $this->form->data()
        );

        /*
         * Publica el evento estándar de registro de Laravel.
         */
        event(new Registered($user));

        /*
         * Inicia la sesión del participante recién creado.
         */
        Auth::login($user);

        /*
         * Regenera el identificador de sesión para evitar ataques
         * de fijación de sesión.
         *
         * La fachada funciona tanto en solicitudes web normales
         * como en las pruebas realizadas mediante Livewire::test().
         */
        Session::regenerate();

        /*
         * Se utiliza una redirección HTTP completa porque la sesión
         * y el token CSRF acaban de cambiar.
         */
        return redirect()->route('dashboard');
    }

    /**
     * Renderiza el formulario de registro.
     */
    public function render(): View
    {
        return view('livewire.auth.register');
    }
}
