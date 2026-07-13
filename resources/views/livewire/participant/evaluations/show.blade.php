<main
    class="mx-auto max-w-4xl px-6 py-10"
    wire:poll.1s.visible="syncTimer"
>
    <div class="space-y-6">
        <flux:card class="space-y-5">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <flux:badge
                            color="{{ $evaluation->status->color() }}"
                        >
                            {{ $evaluation->status->label() }}
                        </flux:badge>

                        <flux:badge color="zinc">
                            {{ $evaluation->evaluation_type->label() }}
                        </flux:badge>
                    </div>

                    <flux:heading
                        level="1"
                        size="xl"
                    >
                        {{ $evaluation->questionnaireVersion->questionnaire->name }}
                    </flux:heading>
                </div>

                @if ($evaluation->isInProgress())
                    <flux:badge
                        color="{{ $remainingSeconds <= 300 ? 'red' : 'blue' }}"
                        size="lg"
                        icon="clock"
                    >
                        {{ $formattedRemainingTime }}
                    </flux:badge>
                @endif
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between gap-4">
                    <flux:text>
                        Progreso
                    </flux:text>

                    <flux:text>
                        {{ $evaluation->answers_count }}
                        de
                        {{ $evaluation->total_questions }}
                    </flux:text>
                </div>

                <flux:progress
                    :value="$progressPercentage"
                />
            </div>
        </flux:card>

        @if ($evaluation->status === \App\Enums\EvaluationStatus::SUBMITTED)
            <flux:callout
                icon="check-circle"
                variant="success"
            >
                <flux:callout.heading>
                    Evaluación enviada correctamente
                </flux:callout.heading>
            
                <flux:callout.text>
                    Tus respuestas fueron procesadas y el resultado
                    ya se encuentra disponible.
                </flux:callout.text>
            </flux:callout>
        
            <div class="flex flex-wrap gap-3">
                <flux:button
                    href="{{ route('results.show', $evaluation) }}"
                    variant="primary"
                    icon="chart-bar"
                >
                    Ver resultados
                </flux:button>
            
                <flux:button
                    href="{{ route('dashboard') }}"
                    variant="ghost"
                    icon="home"
                >
                    Volver al panel
                </flux:button>
            </div>
        @elseif ($evaluation->status === \App\Enums\EvaluationStatus::EXPIRED)
            <flux:callout
                icon="exclamation-triangle"
                variant="danger"
            >
                <flux:callout.heading>
                    Tiempo finalizado
                </flux:callout.heading>

                <flux:callout.text>
                    La evaluación expiró y ya no acepta respuestas.
                </flux:callout.text>
            </flux:callout>

            <flux:button
                href="{{ route('evaluations.index') }}"
                variant="primary"
                icon="arrow-left"
            >
                Volver a evaluaciones
            </flux:button>
        @elseif ($currentItem !== null)
            <flux:card class="space-y-7">
                <div class="space-y-3">
                    <flux:text>
                        Pregunta
                        {{ $currentPosition }}
                        de
                        {{ $evaluation->total_questions }}
                    </flux:text>

                    <flux:heading
                        level="2"
                        size="lg"
                    >
                        {{ $currentItem->statement }}
                    </flux:heading>
                </div>

                <flux:separator />

                <flux:radio.group
                    wire:model.live="answerValue"
                    label="Selecciona una respuesta"
                    variant="cards"
                    class="space-y-3"
                >
                    @foreach ($likertOptions as $value => $label)
                        <flux:radio
                            value="{{ $value }}"
                            label="{{ $label }}"
                        />
                    @endforeach
                </flux:radio.group>

                @error('answerValue')
                    <flux:callout
                        icon="exclamation-circle"
                        variant="danger"
                    >
                        {{ $message }}
                    </flux:callout>
                @enderror

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <flux:button
                        wire:click="previousQuestion"
                        variant="ghost"
                        icon="arrow-left"
                        :disabled="$currentPosition <= 1"
                    >
                        Anterior
                    </flux:button>

                    <div>
                        <flux:text wire:loading wire:target="answerValue">
                            Guardando respuesta...
                        </flux:text>
                    </div>

                    @if ($currentPosition < $evaluation->total_questions)
                        <flux:button
                            wire:click="nextQuestion"
                            variant="primary"
                            icon:trailing="arrow-right"
                        >
                            Siguiente
                        </flux:button>
                    @else
                        <flux:button
                            wire:click="submitEvaluation"
                            wire:loading.attr="disabled"
                            variant="primary"
                            icon="check"
                            :disabled="! $allAnswered"
                        >
                            <span
                                wire:loading.remove
                                wire:target="submitEvaluation"
                            >
                                Finalizar evaluación
                            </span>

                            <span
                                wire:loading
                                wire:target="submitEvaluation"
                            >
                                Finalizando...
                            </span>
                        </flux:button>
                    @endif
                </div>

                @error('evaluation')
                    <flux:callout
                        icon="exclamation-triangle"
                        variant="danger"
                    >
                        {{ $message }}
                    </flux:callout>
                @enderror
            </flux:card>
        @endif
    </div>
</main>