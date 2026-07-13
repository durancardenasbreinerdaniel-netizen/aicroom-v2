<?php

namespace App\Providers;

use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\QuestionnaireVersion;
use App\Models\Skill;
use App\Policies\QuestionnairePolicy;
use App\Policies\QuestionnaireVersionPolicy;
use App\Policies\QuestionPolicy;
use App\Policies\SkillPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra servicios.
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
        Model::shouldBeStrict(
            ! $this->app->isProduction()
        );

        Password::defaults(
            fn (): Password => Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
        );

        Gate::policy(
            Skill::class,
            SkillPolicy::class,
        );

        Gate::policy(
            Question::class,
            QuestionPolicy::class,
        );

        Gate::policy(
            Questionnaire::class,
            QuestionnairePolicy::class,
        );

        Gate::policy(
            QuestionnaireVersion::class,
            QuestionnaireVersionPolicy::class,
        );
    }
}