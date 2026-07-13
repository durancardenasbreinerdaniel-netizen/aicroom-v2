<?php

namespace App\Providers;

use App\Models\Question;
use App\Models\Skill;
use App\Policies\QuestionPolicy;
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
         * Detecta problemas de Eloquent durante el desarrollo.
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
         * Registra las políticas de los módulos administrativos.
         */
        Gate::policy(
            Skill::class,
            SkillPolicy::class,
        );

        Gate::policy(
            Question::class,
            QuestionPolicy::class,
        );
    }
}
