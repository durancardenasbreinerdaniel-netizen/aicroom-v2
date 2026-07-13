<main class="mx-auto max-w-6xl px-6 py-10">
    <div class="space-y-8">
        <div class="space-y-2">
            <flux:heading
                level="1"
                size="xl"
            >
                Mis resultados
            </flux:heading>

            <flux:text size="lg">
                Consulta los resultados de tus evaluaciones finalizadas.
            </flux:text>
        </div>

        @if ($evaluations->isEmpty())
            <flux:callout
                icon="information-circle"
                variant="secondary"
            >
                <flux:callout.heading>
                    Todavía no tienes resultados
                </flux:callout.heading>

                <flux:callout.text>
                    Completa una evaluación para consultar
                    tu perfil de habilidades.
                </flux:callout.text>
            </flux:callout>

            <flux:button
                href="{{ route('evaluations.index') }}"
                variant="primary"
                icon="clipboard-document-check"
            >
                Ver evaluaciones
            </flux:button>
        @else
            <div class="grid gap-5 lg:grid-cols-2">
                @foreach ($evaluations as $evaluation)
                    <flux:card class="space-y-5">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="space-y-2">
                                <flux:heading size="lg">
                                    {{ $evaluation->questionnaireVersion->questionnaire->name }}
                                </flux:heading>

                                <flux:text>
                                    Finalizada el
                                    {{ $evaluation->submitted_at->format('d/m/Y H:i') }}
                                </flux:text>
                            </div>

                            <flux:badge
                                color="{{ $evaluation->result->level->color() }}"
                            >
                                {{ $evaluation->result->level->label() }}
                            </flux:badge>
                        </div>

                        <flux:separator />

                        <div class="space-y-2">
                            <div class="flex items-center justify-between gap-4">
                                <flux:text>
                                    Puntaje global
                                </flux:text>

                                <flux:heading size="lg">
                                    {{ number_format(
                                        (float) $evaluation->result->normalized_score,
                                        2
                                    ) }}
                                </flux:heading>
                            </div>

                            <flux:progress
                                :value="(float) $evaluation->result->normalized_score"
                            />
                        </div>

                        <flux:button
                            href="{{ route('results.show', $evaluation) }}"
                            variant="primary"
                            icon:trailing="arrow-right"
                            class="w-full"
                        >
                            Ver resultado completo
                        </flux:button>
                    </flux:card>
                @endforeach
            </div>
        @endif
    </div>
</main>