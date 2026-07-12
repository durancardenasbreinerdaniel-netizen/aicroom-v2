<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra servicios dentro del contenedor.
     */
    public function register(): void
    {
        //
    }

    /**
     * Configura comportamientos globales de la aplicación.
     */
    public function boot(): void
    {
        /*
         * Activa validaciones estrictas de Eloquent durante desarrollo.
         */
        Model::shouldBeStrict(
            ! $this->app->isProduction()
        );

        /*
         * Centraliza la política de contraseñas.
         *
         * Todos los formularios que utilicen Password::defaults()
         * compartirán estas mismas condiciones.
         */
        Password::defaults(
            fn (): Password => Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
        );
    }
}
