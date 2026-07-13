<main class="mx-auto max-w-6xl px-6 py-10">
    <div class="space-y-8">
        <div class="space-y-2">
            <flux:heading
                level="1"
                size="xl"
            >
                Evaluaciones disponibles
            </flux:heading>

            <flux:text size="lg">
                Selecciona una evaluación para comenzar.
            </flux:text>
        </div>

        @if ($questionnaires->isEmpty())
            <flux:callout
                icon="information-circle"
                variant="secondary"
            >
                <flux:callout.heading>
                    No hay evaluaciones disponibles
                </flux:callout.heading>

                <flux:callout.text>
                    Todavía no existe un cuestionario publicado.
                </flux:callout.text>
            </flux:callout>
        @else
            <div class="grid gap-5 lg:grid-cols-2">
                @foreach ($questionnaires as $questionnaire)
                    <flux:card class="space-y-5">
                        <div class="space-y-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <flux:badge color="blue">
                                    Versión
                                    {{ $questionnaire->publishedVersion->version_number }}
                                </flux:badge>

                                <flux:badge color="zinc">
                                    {{ $questionnaire->publishedVersion->questions_per_evaluation }}
                                    preguntas
                                </flux:badge>
                            </div>

                            <flux:heading size="lg">
                                {{ $questionnaire->name }}
                            </flux:heading>

                            <flux:text>
                                {{ $questionnaire->description }}
                            </flux:text>
                        </div>

                        <flux:separator />

                        <div class="space-y-2">
                            <flux:text>
                                Tiempo disponible:
                                <strong>
                                    {{ $questionnaire->publishedVersion->time_limit_minutes }}
                                    minutos
                                </strong>
                            </flux:text>

                            <flux:text>
                                El tiempo comenzará cuando confirmes
                                el inicio.
                            </flux:text>
                        </div>

                        <flux:button
                            wire:click="start({{ $questionnaire->id }})"
                            wire:loading.attr="disabled"
                            wire:target="start({{ $questionnaire->id }})"
                            variant="primary"
                            icon:trailing="arrow-right"
                            class="w-full"
                        >
                            <span
                                wire:loading.remove
                                wire:target="start({{ $questionnaire->id }})"
                            >
                                Comenzar evaluación
                            </span>

                            <span
                                wire:loading
                                wire:target="start({{ $questionnaire->id }})"
                            >
                                Preparando evaluación...
                            </span>
                        </flux:button>
                    </flux:card>
                @endforeach
            </div>
        @endif
    </div>
</main>