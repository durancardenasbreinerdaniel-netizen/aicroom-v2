<?php

namespace App\Providers;

use App\Models\Skill;
use App\Policies\SkillPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra servicios en el contenedor.
     */
    public function register(): void
    {
        //
    }

    /**
     * Configura comportamientos globales.
     */
    public function boot(): void
    {
        /*
         * Activa las validaciones estrictas de Eloquent
         * fuera del entorno de producción.
         */
        Model::shouldBeStrict(
            ! $this->app->isProduction()
        );

        /*
         * Política global de contraseñas.
         */
        Password::defaults(
            fn (): Password => Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
        );

        /*
         * Registra explícitamente la política de habilidades.
         */
        Gate::policy(
            Skill::class,
            SkillPolicy::class,
        );
    }
}
