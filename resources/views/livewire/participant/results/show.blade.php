<main class="mx-auto max-w-5xl px-6 py-10">
    <div class="space-y-6">
        <flux:card class="space-y-6">
            <div class="flex flex-wrap items-start justify-between gap-5">
                <div class="space-y-2">
                    <flux:text>
                        Resultado de la evaluación
                    </flux:text>

                    <flux:heading
                        level="1"
                        size="xl"
                    >
                        {{ $evaluation->questionnaireVersion->questionnaire->name }}
                    </flux:heading>

                    <flux:text>
                        Finalizada el
                        {{ $evaluation->submitted_at->format('d/m/Y H:i') }}
                    </flux:text>
                </div>

                <flux:badge
                    color="{{ $result->level->color() }}"
                    size="lg"
                >
                    Nivel {{ $result->level->label() }}
                </flux:badge>
            </div>

            <flux:separator />

            <div class="grid gap-5 md:grid-cols-2">
                <div class="space-y-3">
                    <flux:text>
                        Puntaje global
                    </flux:text>

                    <flux:heading size="xl">
                        {{ number_format(
                            (float) $result->normalized_score,
                            2
                        ) }}
                        / 100
                    </flux:heading>

                    <flux:progress
                        :value="(float) $result->normalized_score"
                    />
                </div>

                <flux:callout
                    icon="information-circle"
                    variant="secondary"
                >
                    <flux:callout.heading>
                        {{ $result->level->label() }}
                    </flux:callout.heading>

                    <flux:callout.text>
                        {{ $result->level->description() }}
                    </flux:callout.text>
                </flux:callout>
            </div>
        </flux:card>

        <div class="space-y-4">
            <flux:heading
                level="2"
                size="lg"
            >
                Resultados por habilidad
            </flux:heading>

            <div class="grid gap-4 md:grid-cols-2">
                @foreach ($result->skillResults as $skillResult)
                    <flux:card class="space-y-4">
                        <div class="flex items-start justify-between gap-4">
                            <div class="space-y-1">
                                <flux:badge color="zinc">
                                    {{ $skillResult->skill_code_snapshot }}
                                </flux:badge>

                                <flux:heading size="lg">
                                    {{ $skillResult->skill_name_snapshot }}
                                </flux:heading>
                            </div>

                            <flux:badge
                                color="{{ $skillResult->level->color() }}"
                            >
                                {{ $skillResult->level->label() }}
                            </flux:badge>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-between gap-4">
                                <flux:text>
                                    Puntaje
                                </flux:text>

                                <flux:text>
                                    {{ number_format(
                                        (float) $skillResult->normalized_score,
                                        2
                                    ) }}
                                    / 100
                                </flux:text>
                            </div>

                            <flux:progress
                                :value="(float) $skillResult->normalized_score"
                            />
                        </div>

                        <flux:text>
                            {{ $skillResult->level->description() }}
                        </flux:text>
                    </flux:card>
                @endforeach
            </div>
        </div>

        <flux:callout
            icon="exclamation-triangle"
            variant="warning"
        >
            <flux:callout.heading>
                Interpretación informativa
            </flux:callout.heading>

            <flux:callout.text>
                Estos resultados describen las respuestas registradas
                en la evaluación y no constituyen un diagnóstico clínico,
                psicológico ni profesional.
            </flux:callout.text>
        </flux:callout>

        <div class="flex flex-wrap gap-3">
            <flux:button
                href="{{ route('results.index') }}"
                variant="ghost"
                icon="arrow-left"
            >
                Mis resultados
            </flux:button>

            <flux:button
                href="{{ route('dashboard') }}"
                variant="primary"
                icon="home"
            >
                Volver al panel
            </flux:button>
        </div>
    </div>
</main>