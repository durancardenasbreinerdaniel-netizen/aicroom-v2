<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra servicios dentro del contenedor de Laravel.
     *
     * En esta fase todavía no necesitamos registrar servicios propios.
     */
    public function register(): void
    {
        //
    }

    /**
     * Configura el comportamiento global de la aplicación.
     */
    public function boot(): void
    {
        /*
         * Activa el modo estricto de Eloquent cuando la aplicación
         * no se encuentra en producción.
         *
         * Esto permite detectar durante el desarrollo:
         *
         * - Relaciones cargadas de manera accidental.
         * - Atributos descartados silenciosamente.
         * - Acceso a columnas que no fueron seleccionadas.
         *
         * En producción se desactiva para evitar que un error de
         * desarrollo interrumpa innecesariamente el sistema.
         */
        Model::shouldBeStrict(
            ! $this->app->isProduction()
        );
    }
}
